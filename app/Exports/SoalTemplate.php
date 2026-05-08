<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SoalTemplate implements FromArray, WithHeadings, WithColumnWidths, WithStyles
{
    /**
     * Get template array dengan contoh data
     */
    public function array(): array
    {
        return [
            // Contoh 1: Soal Pilihan Ganda
            [
                'Jelaskan proses fotosintesis pada tumbuhan hijau',
                'PG',
                'Proses pembentukan cahaya menjadi energi kimia',
                'Proses perubahan energi kimia menjadi cahaya',
                'Proses perubahan cahaya menjadi glukosa dan oksigen',
                'Proses pengubahan air menjadi karbohidrat',
                'A',
                '5',
            ],
            // Contoh 2: Soal Essay
            [
                'Apa perbedaan antara osmosis dan difusi?',
                'ESSAY',
                '',
                '',
                '',
                '',
                'Osmosis adalah pergerakan air melalui membran semi permabel sedangkan difusi adalah pergerakan partikel dari konsentrasi tinggi ke rendah tanpa membran khusus',
                '10',
            ],
            // Contoh 3: Soal Pilihan Ganda lagi
            [
                'Nilai dari 2 pangkat 3 adalah berapa?',
                'PG',
                '6',
                '8',
                '9',
                '12',
                'B',
                '3',
            ],
        ];
    }

    /**
     * Set column headings
     */
    public function headings(): array
    {
        return [
            'Pertanyaan',
            'Tipe (PG/ESSAY)',
            'Opsi A',
            'Opsi B',
            'Opsi C',
            'Opsi D',
            'Kunci Jawaban',
            'Bobot',
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 15,
            'C' => 25,
            'D' => 25,
            'E' => 25,
            'F' => 25,
            'G' => 25,
            'H' => 12,
        ];
    }

    /**
     * Style the sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('1:1')->getFont()->setBold(true);
        $sheet->getStyle('1:1')->getFont()->setSize(12);
        $sheet->getStyle('1:1')->setAlignment(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::ALIGNMENT_CENTER
        );
        $sheet->getStyle('1:1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('1:1')->getFill()->getStartColor()->setARGB('FFBDD7EE');

        // Center align columns B, G, H
        $sheet->getStyle('B:B')->setAlignment(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::ALIGNMENT_CENTER
        );
        $sheet->getStyle('G:G')->setAlignment(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::ALIGNMENT_CENTER
        );
        $sheet->getStyle('H:H')->setAlignment(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::ALIGNMENT_CENTER
        );

        // Wrap text for long content
        $sheet->getStyle('A:G')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A:G')->getAlignment()->setVertical(
            \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
        );

        // Set row heights for better readability
        $sheet->getRowDimension(1)->setRowHeight(25);
        $sheet->getRowDimension(2)->setRowHeight(40);
        $sheet->getRowDimension(3)->setRowHeight(40);
        $sheet->getRowDimension(4)->setRowHeight(40);

        return [];
    }
}
