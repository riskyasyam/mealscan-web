<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $attendances;

    public function __construct($attendances)
    {
        $this->attendances = $attendances;
    }

    /**
     * Return collection of data
     */
    public function collection()
    {
        return $this->attendances;
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Waktu',
            'NIK',
            'Nama',
            'Kategori Makan',
            'Jumlah',
            'Status',
            'Saran'
        ];
    }

    /**
     * Map data for each row
     */
    public function map($attendance): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $attendance->attendance_date->format('d/m/Y'),
            $attendance->attendance_time->format('H:i:s'),
            $attendance->nik,
            $attendance->employee->name ?? '-',
            $this->getMealTypeLabel($attendance->meal_type),
            $attendance->quantity,
            ucfirst($attendance->status),
            $attendance->remarks ?? '-'
        ];
    }

    /**
     * Get meal type label in Indonesian
     */
    private function getMealTypeLabel($mealType): string
    {
        $labels = [
            'breakfast' => 'Makan Pagi',
            'lunch' => 'Makan Siang',
            'dinner' => 'Makan Malam'
        ];

        return $labels[$mealType] ?? ucfirst($mealType);
    }

    /**
     * Define column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 12,  // Tanggal
            'C' => 10,  // Waktu
            'D' => 15,  // NIK
            'E' => 25,  // Nama
            'F' => 15,  // Kategori
            'G' => 10,  // Jumlah
            'H' => 12,  // Status
            'I' => 30,  // Saran
        ];
    }

    /**
     * Apply styles to worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Style for header row
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'], // Blue background
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Get last row number
        $lastRow = $sheet->getHighestRow();

        // Style for all data cells
        $sheet->getStyle('A1:I' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Center align for specific columns
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B2:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C2:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D2:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F2:F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G2:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H2:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Auto-wrap text for Saran column
        $sheet->getStyle('I2:I' . $lastRow)->getAlignment()->setWrapText(true);

        return [];
    }

    /**
     * Define sheet title
     */
    public function title(): string
    {
        return 'Laporan Absensi';
    }
}
