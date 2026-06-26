<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RombelImportSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_rombel_table_has_tingkat_column(): void
    {
        $this->assertTrue(Schema::hasColumn('rombel', 'tingkat'));
    }
}
