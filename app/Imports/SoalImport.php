<?php

namespace App\Imports;

use App\Models\KategoriUjian;
use App\Models\Soal;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromSheet;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class SoalImport implements FromSheet, WithStartRow, WithEvents
{
    /**
     * @var int
     */
    private $row = 0;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var int
     */
    private $successCount = 0;

    /**
     * @var int
     */
    private $kategoriUjianId;

    /**
     * Constructor untuk set kategori_ujian_id
     */
    public function __construct($kategoriUjianId)
    {
        $this->kategoriUjianId = $kategoriUjianId;
    }

    /**
     * Start dari row 2 (skip header)
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Handle sheet untuk memproses baris
     */
    public function sheet(AfterSheet $event)
    {
        $sheet = $event->sheet->getDelegate();
        $rows = $sheet->toArray();

        $tenantId = tenancy()->tenant?->id;
        if (!$tenantId) {
            $this->errors[] = 'Tenant context tidak ditemukan';
            return;
        }

        // Get guru_id dari user yang login
        $user = Auth::user();
        if (!$user || !$user->guru) {
            $this->errors[] = 'User adalah bukan guru';
            return;
        }
        $guruId = $user->guru->id;

        // Validasi kategori ujian
        $kategoriUjian = KategoriUjian::where('id', $this->kategoriUjianId)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$kategoriUjian) {
            $this->errors[] = 'Kategori ujian tidak ditemukan atau tidak valid untuk tenant ini';
            return;
        }

        // Track soal yang sudah ada (untuk prevent duplicates)
        $existingSoal = Soal::where('tenant_id', $tenantId)
            ->where('kategori_ujian_id', $this->kategoriUjianId)
            ->pluck('pertanyaan')
            ->toArray();

        // Process each row
        foreach ($rows as $rowData) {
            $this->row++;
            $rowNum = $this->row + 1; // +1 karena header di row 1, actual data mulai row 2

            // Skip empty rows
            if (empty($rowData[0]) && empty($rowData[1])) {
                continue;
            }

            try {
                // Extract columns: Pertanyaan, Tipe, OpsiA, OpsiB, OpsiC, OpsiD, KunciJawaban, Bobot
                $pertanyaan = trim($rowData[0] ?? '');
                $tipe = strtoupper(trim($rowData[1] ?? ''));
                $opsiA = trim($rowData[2] ?? '');
                $opsiB = trim($rowData[3] ?? '');
                $opsiC = trim($rowData[4] ?? '');
                $opsiD = trim($rowData[5] ?? '');
                $kunciJawaban = trim($rowData[6] ?? '');
                $bobot = (int) ($rowData[7] ?? 1);

                // Validasi: Pertanyaan tidak boleh kosong
                if (empty($pertanyaan)) {
                    $this->errors[] = "Row {$rowNum}: Pertanyaan tidak boleh kosong";
                    continue;
                }

                // Validasi: Tipe soal harus PG atau Essay
                if (!in_array($tipe, ['PG', 'ESSAY'])) {
                    $this->errors[] = "Row {$rowNum}: Tipe soal harus 'PG' atau 'ESSAY' (ditemukan: {$tipe})";
                    continue;
                }

                // Validasi: Untuk PG, harus ada 4 opsi
                if ($tipe === 'PG') {
                    if (empty($opsiA) || empty($opsiB) || empty($opsiC) || empty($opsiD)) {
                        $this->errors[] = "Row {$rowNum}: Untuk soal PG, semua opsi (A, B, C, D) harus diisi";
                        continue;
                    }
                }

                // Validasi: Kunci jawaban tidak boleh kosong
                if (empty($kunciJawaban)) {
                    $this->errors[] = "Row {$rowNum}: Kunci jawaban tidak boleh kosong";
                    continue;
                }

                // Validasi: Untuk PG, kunci jawaban harus A, B, C, atau D
                if ($tipe === 'PG') {
                    if (!in_array(strtoupper($kunciJawaban), ['A', 'B', 'C', 'D'])) {
                        $this->errors[] = "Row {$rowNum}: Kunci jawaban PG harus A, B, C, atau D (ditemukan: {$kunciJawaban})";
                        continue;
                    }
                    $kunciJawaban = strtoupper($kunciJawaban);
                }

                // Validasi: Soal tidak boleh duplikat (dalam kategori yang sama)
                if (in_array($pertanyaan, $existingSoal)) {
                    $this->errors[] = "Row {$rowNum}: Pertanyaan sudah ada (duplikat)";
                    continue;
                }

                // Create soal record
                $soal = Soal::create([
                    'tenant_id' => $tenantId,
                    'kategori_ujian_id' => $this->kategoriUjianId,
                    'guru_id' => $guruId,
                    'pertanyaan' => $pertanyaan,
                    'tipe_soal' => $tipe === 'ESSAY' ? 'essay' : 'pilihan_ganda',
                    'opsi_a' => $opsiA,
                    'opsi_b' => $opsiB,
                    'opsi_c' => $opsiC,
                    'opsi_d' => $opsiD,
                    'kunci_jawaban' => $kunciJawaban,
                    'bobot' => $bobot > 0 ? $bobot : 1,
                    'is_active' => true,
                ]);

                // Add to existing soal untuk prevent duplicates dalam batch ini
                $existingSoal[] = $pertanyaan;
                $this->successCount++;

            } catch (\Exception $e) {
                $this->errors[] = "Row {$rowNum}: Error - " . $e->getMessage();
            }
        }
    }

    /**
     * Get registered events
     */
    public function registerEvents(): array
    {
        return [];
    }

    /**
     * Get success count
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * Get errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get total errors
     */
    public function getErrorCount(): int
    {
        return count($this->errors);
    }
}
