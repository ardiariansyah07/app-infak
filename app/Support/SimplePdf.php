<?php

namespace App\Support;

class SimplePdf
{
    private array $pages = [];

    private array $current = [];

    private int $y = 800;

    public function __construct(
        private readonly string $title,
    ) {
        $this->addPage();
    }

    public function heading(string $text): void
    {
        $this->line($text, 16, true);
        $this->space(8);
    }

    public function subheading(string $text): void
    {
        $this->line($text, 12, true);
        $this->space(4);
    }

    public function line(string $text = '', int $size = 10, bool $bold = false): void
    {
        if ($this->y < 60) {
            $this->addPage();
        }

        $font = $bold ? 'F2' : 'F1';
        $this->current[] = sprintf(
            'BT /%s %d Tf 40 %d Td (%s) Tj ET',
            $font,
            $size,
            $this->y,
            $this->escape($text)
        );
        $this->y -= $size + 6;
    }

    public function tableRow(array $columns, array $widths, bool $bold = false): void
    {
        $x = 40;
        $height = 18;

        if ($this->y < 60) {
            $this->addPage();
        }

        foreach ($columns as $index => $column) {
            $font = $bold ? 'F2' : 'F1';
            $width = $widths[$index] ?? 80;
            $text = $this->fit((string) $column, $widths[$index] ?? 80);
            $this->current[] = sprintf('%d %d %d %d re S', $x, $this->y - 5, $width, $height);
            $this->current[] = sprintf(
                'BT /%s 9 Tf %d %d Td (%s) Tj ET',
                $font,
                $x + 4,
                $this->y,
                $this->escape($text)
            );
            $x += $width;
        }

        $this->y -= $height;
    }

    public function space(int $height = 10): void
    {
        $this->y -= $height;
    }

    public function output(): string
    {
        $this->finishPage();

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '',
        ];

        $pageObjects = [];
        $contentObjects = [];
        $nextObject = 3;

        foreach ($this->pages as $page) {
            $pageObject = $nextObject++;
            $contentObject = $nextObject++;
            $pageObjects[] = $pageObject.' 0 R';
            $contentObjects[] = [$contentObject, implode("\n", $page)];
            $objects[$pageObject - 1] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> /F2 << /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >> >> >> /Contents {$contentObject} 0 R >>";
        }

        $objects[1] = '<< /Type /Pages /Kids ['.implode(' ', $pageObjects).'] /Count '.count($pageObjects).' >>';

        foreach ($contentObjects as [$objectNumber, $stream]) {
            $objects[$objectNumber - 1] = '<< /Length '.strlen($stream)." >>\nstream\n{$stream}\nendstream";
        }

        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $number => $object) {
            $offsets[$number + 1] = strlen($pdf);
            $pdf .= ($number + 1)." 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size ".(count($objects) + 1).' /Root 1 0 R /Title ('.$this->escape($this->title).") >>\n";
        $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

        return $pdf;
    }

    private function addPage(): void
    {
        $this->finishPage();
        $this->current = [];
        $this->y = 800;
    }

    private function finishPage(): void
    {
        if ($this->current !== []) {
            $this->pages[] = $this->current;
            $this->current = [];
        }
    }

    private function fit(string $text, int $width): string
    {
        $max = max(8, (int) floor($width / 5));

        return mb_strlen($text) > $max ? mb_substr($text, 0, $max - 3).'...' : $text;
    }

    private function escape(string $text): string
    {
        $text = str_replace(['Rp ', '.'], ['Rp ', '.'], $text);
        $text = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text) ?: $text;

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
