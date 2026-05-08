<?php

namespace App\Imports;

use App\Models\KategoriUjian;
use App\Models\Soal;
use Exception;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Row;

class SoalImport implements OnEachRow, WithStartRow
{
    /**
     * @var array
     */
    private array $errors = [];

    /**
     * @var int
     */
    private int $successCount = 0;

    /**
     * @var int
     */
    private int $kategoriUjianId;

    /**
     * @var string|null
     */
    private ?string $tenantId = null;

    /**
     * @var int|null
     */
    private ?int $guruId = null;

    /**
     * @var array
     */
    private array $existingSoal = [];

    /**
     * @var bool
     */
    private bool $initialized = false;

    public function __construct(int $kategoriUjianId)
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
     * Process each row
     */
    public function onRow(Row $row): void
    {
        $rowIndex = $row->getIndex();
        $rowData  = $row->toArray();

        // Lazy-init context once on first row
        if (! $this->initialized) {
            $this->initContext();
        }

        if (! $this->tenantId || ! $this->guruId) {
            return;
        }

        // Skip empty rows
        if (empty($rowData[0]) && empty($rowData[1])) {
            return;
        }

        try {
            $pertanyaan  = trim($rowData[0] ?? '');
            $tipe        = strtoupper(trim($rowData[1] ?? ''));
            $opsiA       = trim($rowData[2] ?? '');
            $opsiB       = trim($rowData[3] ?? '');
            $opsiC       = trim($rowData[4] ?? '');
            $opsiD       = trim($rowData[5] ?? '');
            $kunciJawaban = trim($rowData[6] ?? '');
            $bobot       = (int) ($rowData[7] ?? 1);

            if (empty($pertanyaan)) {
                $this->errors[] = "Row {$rowIndex}: Pertanyaan tidak boleh kosong";
                return;
            }

            if (! in_array($tipe, ['PG', 'ESSAY'])) {
                $this->errors[] = "Row {$rowIndex}: Tipe soal harus 'PG' atau 'ESSAY' (ditemukan: {$tipe})";
                return;
            }

            if ($tipe === 'PG') {
                if (empty($opsiA) || empty($opsiB) || empty($opsiC) || empty($opsiD)) {
                    $this->errors[] = "Row {$rowIndex}: Untuk soal PG, semua opsi (A, B, C, D) harus diisi";
                    return;
                }
            }

            if (empty($kunciJawaban)) {
                $this->errors[] = "Row {$rowIndex}: Kunci jawaban tidak boleh kosong";
                return;
            }

            if ($tipe === 'PG') {
                if (! in_array(strtoupper($kunciJawaban), ['A', 'B', 'C', 'D'])) {
                    $this->errors[] = "Row {$rowIndex}: Kunci jawaban PG harus A, B, C, atau D (ditemukan: {$kunciJawaban})";
                    return;
                }
                $kunciJawaban = strtoupper($kunciJawaban);
            }

            if (in_array($pertanyaan, $this->existingSoal)) {
                $this->errors[] = "Row {$rowIndex}: Pertanyaan sudah ada (duplikat)";
                return;
            }

            Soal::create([
                'tenant_id'        => $this->tenantId,
                'kategori_ujian_id' => $this->kategoriUjianId,
                'guru_id'          => $this->guruId,
                'pertanyaan'       => $pertanyaan,
                'tipe_soal'        => $tipe === 'ESSAY' ? 'essay' : 'pilihan_ganda',
                'opsi_a'           => $opsiA,
                'opsi_b'           => $opsiB,
                'opsi_c'           => $opsiC,
                'opsi_d'           => $opsiD,
                'kunci_jawaban'    => $kunciJawaban,
                'bobot'            => $bobot > 0 ? $bobot : 1,
                'is_active'        => true,
            ]);

            $this->existingSoal[] = $pertanyaan;
            $this->successCount++;

        } catch (Exception $e) {
            $this->errors[] = "Row {$rowIndex}: Error - " . $e->getMessage();
        }
    }

    /**
     * Initialize tenant/guru context and existing soal list.
     */
    private function initContext(): void
    {
        $this->initialized = true;

        $this->tenantId = tenancy()->tenant?->id;
        if (! $this->tenantId) {
            $this->errors[] = 'Tenant context tidak ditemukan';
            return;
        }

        $user = Auth::user();
        if (! $user || ! $user->guru) {
            $this->errors[] = 'User bukan guru';
            return;
        }
        $this->guruId = $user->guru->id;

        $kategoriUjian = KategoriUjian::where('id', $this->kategoriUjianId)
            ->where('tenant_id', $this->tenantId)
            ->first();

        if (! $kategoriUjian) {
            $this->errors[] = 'Kategori ujian tidak ditemukan atau tidak valid untuk tenant ini';
            $this->tenantId = null;
            return;
        }

        $this->existingSoal = Soal::where('tenant_id', $this->tenantId)
            ->where('kategori_ujian_id', $this->kategoriUjianId)
            ->pluck('pertanyaan')
            ->toArray();
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getErrorCount(): int
    {
        return count($this->errors);
    }
}
