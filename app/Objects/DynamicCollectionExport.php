<?php

namespace App\Objects;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Str;

class DynamicCollectionExport implements
    FromCollection,
    WithHeadings,
    WithEvents,
    WithCustomStartCell
// ShouldAutoSize
{
    protected $data;
    protected $columns;
    protected $meta;

    public function __construct($data, array $meta = [])
    {
        $this->data = collect($data)->map(fn($row) => $row->getAttributes());

        $this->columns = $this->data->isNotEmpty()
            ? array_keys($this->data->first())
            : [];

        $this->meta = $meta;
    }

    /** data mulai dari baris ke-5 */
    public function startCell(): string
    {
        return 'A5';
    }

    public function collection()
    {
        return $this->data->map(fn($row) => array_values($row));
    }

    public function headings(): array
    {
        return collect($this->columns)
            ->map(fn($col) => $this->formatHeading($col))
            ->toArray();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $highestColumn = $sheet->getHighestColumn();
                $highestRow    = $sheet->getHighestRow();

                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")
                    ->getFont()
                    ->setName('Arial');

                $sheet->getStyle("A5:{$highestColumn}5")->applyFromArray([
                    'font' => ['bold' => true, 'sizec' => 12],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'E5E7EB', // abu-abu soft, profesional, tidak norak
                        ],
                    ],
                ]);

                $sheet->getStyle("A6:{$highestColumn}{$highestRow}")
                    ->applyFromArray([
                        'alignment' => [
                            'wrapText' => true,
                            'vertical' => Alignment::VERTICAL_TOP,
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                        'font' => ['size' => 10],
                    ]);

                $maxWidth = 30;

                foreach (range(1, Coordinate::columnIndexFromString($highestColumn)) as $colIndex) {
                    $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                    $sheet->getColumnDimension($colLetter)->setWidth($maxWidth);
                }
                // Judul
                // $sheet->setCellValue('A1', $this->meta['title'] ?? 'LAPORAN');
                $sheet->mergeCells('A1:' . $sheet->getHighestColumn() . '1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Info tambahan
                $row = 2;
                foreach ($this->meta as $label => $value) {
                    if ($label === 'title') continue;

                    $sheet->setCellValue("A{$row}", ucfirst($label));
                    $sheet->setCellValue("B{$row}", $value);
                    $row++;
                }

                // Header tabel bold
                $headerRow = 5;
                $sheet->getStyle("A{$headerRow}:" . $sheet->getHighestColumn() . "{$headerRow}")
                    ->getFont()->setBold(true);
            }
        ];
    }

    protected function formatHeading(string $key): string
    {
        return Str::of($key)->replace('_', ' ')->title();
    }
}
