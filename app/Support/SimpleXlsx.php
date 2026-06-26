<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

class SimpleXlsx
{
    public static function template(array $headers, string $sheetName = 'Template'): string
    {
        $sharedStrings = '';
        $cells = '';

        foreach (array_values($headers) as $index => $header) {
            $sharedStrings .= '<si><t>'.self::escape($header).'</t></si>';
            $cells .= '<c r="'.self::column($index + 1).'1" t="s"><v>'.$index.'</v></c>';
        }

        $zipPath = tempnam(sys_get_temp_dir(), 'xlsx_');
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Tidak bisa membuat file template XLSX.');
        }

        $zip->addFromString('[Content_Types].xml', self::contentTypes());
        $zip->addFromString('_rels/.rels', self::rootRels());
        $zip->addFromString('xl/workbook.xml', self::workbook($sheetName));
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::workbookRels());
        $zip->addFromString('xl/sharedStrings.xml', '<?xml version="1.0" encoding="UTF-8"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.count($headers).'" uniqueCount="'.count($headers).'">'.$sharedStrings.'</sst>');
        $zip->addFromString('xl/styles.xml', self::styles());
        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData><row r="1">'.$cells.'</row></sheetData></worksheet>');
        $zip->close();

        return $zipPath;
    }

    public static function rows(string $path): array
    {
        $zip = new ZipArchive;

        if ($zip->open($path) !== true) {
            throw new RuntimeException('File XLSX tidak bisa dibuka.');
        }

        $shared = self::readSharedStrings($zip);
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheet === false) {
            throw new RuntimeException('Sheet pertama tidak ditemukan.');
        }

        $xml = simplexml_load_string($sheet);
        $xml->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $rows = [];

        foreach ($xml->xpath('//m:sheetData/m:row') as $row) {
            $values = [];

            foreach ($row->xpath('m:c') as $cell) {
                $ref = (string) $cell['r'];
                $columnIndex = self::columnIndex(preg_replace('/\d+/', '', $ref));
                $value = (string) ($cell->v ?? '');

                if ((string) $cell['t'] === 's') {
                    $value = $shared[(int) $value] ?? '';
                }

                $values[$columnIndex] = trim($value);
            }

            if (array_filter($values, fn ($value) => $value !== '')) {
                ksort($values);
                $rows[] = array_values($values);
            }
        }

        return $rows;
    }

    private static function readSharedStrings(ZipArchive $zip): array
    {
        $content = $zip->getFromName('xl/sharedStrings.xml');

        if ($content === false) {
            return [];
        }

        $xml = simplexml_load_string($content);
        $xml->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        return array_map(
            fn ($item) => (string) $item->t,
            $xml->xpath('//m:si') ?: []
        );
    }

    private static function column(int $index): string
    {
        $name = '';

        while ($index > 0) {
            $index--;
            $name = chr(65 + ($index % 26)).$name;
            $index = intdiv($index, 26);
        }

        return $name;
    }

    private static function columnIndex(string $column): int
    {
        $index = 0;

        foreach (str_split($column) as $char) {
            $index = $index * 26 + ord($char) - 64;
        }

        return $index - 1;
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    private static function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>';
    }

    private static function rootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
    }

    private static function workbook(string $sheetName): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="'.self::escape($sheetName).'" sheetId="1" r:id="rId1"/></sheets></workbook>';
    }

    private static function workbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/></Relationships>';
    }

    private static function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts><fills count="1"><fill><patternFill patternType="none"/></fill></fills><borders count="1"><border/></borders><cellStyleXfs count="1"><xf/></cellStyleXfs><cellXfs count="1"><xf/></cellXfs></styleSheet>';
    }
}
