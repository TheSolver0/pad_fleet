<?php

namespace App\Exports\Concerns;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

/**
 * Ajoute l'en-tête PAD (logo + typographie) en haut de chaque export Excel.
 * À combiner avec `WithEvents` : implémenter `registerEvents()` en fusionnant
 * `$this->letterheadEvents()` avec les événements propres à l'export.
 */
trait WithPadLetterhead
{
    /** Titre affiché sous "Direction des Affaires Générales" (facultatif). */
    protected ?string $letterheadTitle = null;

    /** Nombre de colonnes du tableau, pour fusionner les cellules du titre. */
    abstract protected function letterheadColumnCount(): int;

    public function letterheadEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(max(1, $this->letterheadColumnCount()));

                $sheet->insertNewRowBefore(1, 3);

                $sheet->setCellValue('A1', 'PORT AUTONOME DE DOUALA');
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('A2', 'Direction des Affaires Générales');
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                if ($this->letterheadTitle) {
                    $sheet->setCellValue('A3', $this->letterheadTitle);
                    $sheet->mergeCells("A3:{$lastCol}3");
                    $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(11);
                    $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                }

                $logoPath = public_path('img/logo.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo PAD');
                    $drawing->setPath($logoPath);
                    $drawing->setHeight(45);
                    $drawing->setCoordinates('A1');
                    $drawing->setOffsetX(2);
                    $drawing->setOffsetY(2);
                    $drawing->setWorksheet($sheet);
                }

                $sheet->getRowDimension(1)->setRowHeight(20);
                $sheet->getRowDimension(2)->setRowHeight(16);
            },
        ];
    }
}
