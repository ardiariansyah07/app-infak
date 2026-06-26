<?php

namespace Tests\Feature;

use App\Support\SimpleXlsx;
use Tests\TestCase;
use ZipArchive;

class SimpleXlsxRowsTest extends TestCase
{
    public function test_rows_can_be_read_from_namespaced_sheet_xml(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'xlsx_test_');
        $zip = new ZipArchive;

        $this->assertTrue($zip->open($path, ZipArchive::OVERWRITE) === true);

        $zip->addFromString('xl/sharedStrings.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="2" uniqueCount="2">
  <si><t>Nama</t></si>
  <si><t>Rombel</t></si>
</sst>
XML);

        $zip->addFromString('xl/worksheets/sheet1.xml', <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>
    <row r="1">
      <c r="A1" t="s"><v>0</v></c>
      <c r="B1" t="s"><v>1</v></c>
    </row>
    <row r="2">
      <c r="A2"><v>XI</v></c>
      <c r="B2"><v>2</v></c>
    </row>
  </sheetData>
</worksheet>
XML);

        $zip->close();

        $this->assertSame([
            ['Nama', 'Rombel'],
            ['XI', '2'],
        ], SimpleXlsx::rows($path));

        unlink($path);
    }
}
