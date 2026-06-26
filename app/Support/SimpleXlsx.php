<?php

namespace App\Support;

use RuntimeException;
use ZipArchive;

class SimpleXlsx
{
    private const SPREADSHEET_NAMESPACE = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

    public static function template(array $headers, string $sheetName = 'Template', array $examples = []): string
    {
        $sharedStrings = '';
        $rows = '';
        $stringIndex = 0;

        foreach (array_values($headers) as $index => $header) {
            $sharedStrings .= '<si><t>'.self::escape($header).'</t></si>';
            $rows .= '<c r="'.self::column($index + 1).'1" t="s"><v>'.$stringIndex.'</v></c>';
            $stringIndex++;
        }

        $sheetRows = '<row r="1">'.$rows.'</row>';

        foreach (array_values($examples) as $rowIndex => $example) {
            $cells = '';
            $excelRow = $rowIndex + 2;

            foreach (array_values($headers) as $columnIndex => $header) {
                $value = (string) ($example[$header] ?? '');
                $sharedStrings .= '<si><t>'.self::escape($value).'</t></si>';
                $cells .= '<c r="'.self::column($columnIndex + 1).$excelRow.'" t="s"><v>'.$stringIndex.'</v></c>';
                $stringIndex++;
            }

            $sheetRows .= '<row r="'.$excelRow.'">'.$cells.'</row>';
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
        $zip->addFromString('xl/sharedStrings.xml', '<?xml version="1.0" encoding="UTF-8"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="'.$stringIndex.'" uniqueCount="'.$stringIndex.'">'.$sharedStrings.'</sst>');
        $zip->addFromString('xl/styles.xml', self::styles());
        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'.$sheetRows.'</sheetData></worksheet>');
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

        if (! $xml) {
            throw new RuntimeException('Sheet pertama tidak valid.');
        }

        $rows = [];

        foreach ($xml->children(self::SPREADSHEET_NAMESPACE)->sheetData->row ?? [] as $row) {
            $values = [];

            foreach ($row->children(self::SPREADSHEET_NAMESPACE) as $cell) {
                if ($cell->getName() !== 'c') {
                    continue;
                }

                $attributes = $cell->attributes();
                $ref = (string) ($attributes['r'] ?? '');

                if ($ref === '') {
                    continue;
                }

                $columnIndex = self::columnIndex(preg_replace('/\d+/', '', $ref));
                $cellChildren = $cell->children(self::SPREADSHEET_NAMESPACE);
                $value = (string) ($cellChildren->v ?? '');
                $type = (string) ($attributes['t'] ?? '');

                if ($type === 's') {
                    $value = $shared[(int) $value] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string) ($cellChildren->is->t ?? '');
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

        return array_map(function ($item) {
            $children = $item->children(self::SPREADSHEET_NAMESPACE);

            if (isset($children->t)) {
                return (string) $children->t;
            }

            $parts = [];

            foreach ($children->r ?? [] as $run) {
                $parts[] = (string) ($run->children(self::SPREADSHEET_NAMESPACE)->t ?? '');
            }

            return implode('', $parts);
        }, $xml->xpath('//m:si') ?: []);
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
