<?php

namespace App\Exports;

use App\Exports\Concerns\WithPadLetterhead;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TableExport implements FromArray, WithHeadings, WithEvents
{
    use WithPadLetterhead;

    public function __construct(
        private array $headings,
        private array $rows,
        ?string $title = null
    ) {
        $this->letterheadTitle = $title;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function array(): array
    {
        return $this->rows;
    }

    protected function letterheadColumnCount(): int
    {
        return count($this->headings) ?: 1;
    }

    public function registerEvents(): array
    {
        return $this->letterheadEvents();
    }
}
