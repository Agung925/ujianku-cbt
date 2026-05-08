# PHASE 4.5: Automatic Grading System

**Completion Date**: 2026-05-08  
**Status**: ✅ COMPLETE  
**Version**: 1.0.0

---

## 📋 Overview

Phase 4.5 mengimplementasikan sistem grading otomatis untuk menangani kedua tipe soal (PG dan Essay) dengan scoring terbobot untuk PG dan grading manual untuk essay. Sistem terintegrasi dengan exam question bank dan menggunakan observer pattern untuk auto-grade pada saat siswa submit jawaban.

### Key Features
✅ Automatic PG Scoring - Hitung nilai otomatis dengan bobot soal  
✅ Essay Manual Grading - Teacher input nilai essay setelah exam  
✅ Weighted Scoring - Support bobot berbeda per soal  
✅ Auto-Trigger Grading - Observer pattern on answer submission  
✅ Pass/Fail Status - Automatic determination based on final score  
✅ Grading Dashboard - Teacher interface untuk lihat & grade  
✅ Exam Statistics - Real-time stats untuk teacher  

---

## 📊 Architecture

### System Flow

```
┌──────────────────────┐
│ Student Submits Exam │
└──────┬───────────────┘
       ↓
┌──────────────────────────────────────────┐
│ JawabanSiswaObserver.created/updated     │
│ - Detect is_submitted = true             │
└──────┬───────────────────────────────────┘
       ↓
┌──────────────────────────────────────────┐
│ Check: All answers for exam submitted?   │
├──────────────────────────────────────────┤
│ NO  → Skip (wait for remaining answers)  │
│ YES → Continue to grading                │
└──────┬───────────────────────────────────┘
       ↓
┌──────────────────────────────────────────┐
│ GradingService::autoGradeExam()          │
├──────────────────────────────────────────┤
│ 1. Calculate PG score (weighted)         │
│ 2. Check: Essay questions exist?         │
│    - IF YES: status = pending_essay      │
│    - IF NO:  Calculate final → status    │
│ 3. Set: lulus/tidak_lulus (based on 70)  │
└──────┬───────────────────────────────────┘
       ↓
┌──────────────────────────────────────────┐
│ Teacher: Submit Essay Grades (manual)    │
└──────┬───────────────────────────────────┘
       ↓
┌──────────────────────────────────────────┐
│ GradingService::finalizeGrades()         │
│ - Calculate final = (pg + essay) / 2     │
│ - Determine: lulus/tidak_lulus           │
│ - Update Nilai record                    │
└──────────────────────────────────────────┘
```

---

## 🎯 Scoring System

### Weighted PG Scoring

**Formula**:
$$\text{PG Score} = \frac{\sum \text{(correct weight)}}{\sum \text{(total weight)}} \times 100$$

**Example**:

Exam dengan 3 soal PG:
- Q1: weight=30%, jawaban benar ✓ → +30
- Q2: weight=40%, jawaban salah ✗ → +0
- Q3: weight=30%, jawaban benar ✓ → +30

Score = (30 + 0 + 30) / (30 + 40 + 30) × 100 = **60/100 = 60**

### Essay Scoring

**Formula**:
$$\text{Essay Score} = \text{clamp}(\text{input}, 0, 100)$$

- Teacher input: 0-100
- Auto clamp ke range valid (tidak bisa >100 atau <0)

### Final Score Calculation

**Dengan Essay Questions**:
$$\text{Final Score} = \frac{\text{PG Score} + \text{Essay Score}}{2}$$

**Tanpa Essay Questions**:
$$\text{Final Score} = \text{PG Score}$$

### Pass/Fail Status

- **Lulus** (PASS): Final Score ≥ 70
- **Tidak Lulus** (FAIL): Final Score < 70

---

## 💾 Database Schema

### Nilai Table (Grading Results)

```sql
CREATE TABLE nilais (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    ujian_id BIGINT NOT NULL,
    siswa_id BIGINT NOT NULL,
    tenant_id BIGINT,
    
    -- Scores
    nilai_otomatis DECIMAL(5, 2),      -- PG score (0-100)
    nilai_essay DECIMAL(5, 2),         -- Essay score (0-100, nullable)
    nilai_akhir DECIMAL(5, 2),         -- Final score
    
    -- Status tracking
    status VARCHAR(50),                -- pending|pending_essay|lulus|tidak_lulus
    
    -- Audit
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    -- Constraints
    FOREIGN KEY (ujian_id) REFERENCES ujians(id),
    FOREIGN KEY (siswa_id) REFERENCES siswas(id),
    UNIQUE KEY ujian_siswa_unique (ujian_id, siswa_id),
    INDEX status_index (status),
    INDEX ujian_index (ujian_id),
    INDEX siswa_index (siswa_id)
);
```

### JawabanSiswa Table (Answer Submission)

```sql
-- Added columns for submission tracking
ALTER TABLE jawaban_siswas ADD COLUMN is_submitted BOOLEAN DEFAULT FALSE;
ALTER TABLE jawaban_siswas ADD COLUMN waktu_submit TIMESTAMP NULL;

-- Fields:
-- jawaban (string) - Actual answer (char for PG, text for essay)
-- is_submitted (boolean) - Mark submitted atau not
-- waktu_submit (timestamp) - When answer was finalized
```

---

## 📁 Implementation Details

### 1. GradingService (`app/Services/GradingService.php`)

**Core grading logic & calculations**

**Public Methods**:

```php
public function calculateScorePG(int $ujianId, int $siswaId): ?float
// Calculate weighted PG score
// Return: 0-100 or null if no PG questions
// Logic:
//   - Get all soal with tipe_soal='pg' untuk exam
//   - Loop jawaban_siswas untuk siswa
//   - Sum: correct ? soal.bobot : 0
//   - Divide by total bobot * 100

public function calculateScoreEssay(int $ujianId, int $siswaId, float $score): float
// Validate & clamp essay score
// Input: raw score (can be >100 or <0)
// Return: clamped score 0-100
// Logic: max(0, min(100, $score))

public function calculateFinalScore(int $ujianId, int $siswaId): array
// Calculate final score & status
// Return: ['nilai_akhir' => float, 'status' => string]
// Logic:
//   - Get nilai_otomatis & nilai_essay
//   - If essay NULL: final = otomatis
//   - Else: final = (otomatis + essay) / 2
//   - Status: final >= 70 ? 'lulus' : 'tidak_lulus'

public function autoGradeExam(int $ujianId, int $siswaId): bool
// Main auto-grading trigger
// Called by observer when all answers submitted
// Logic:
//   1. Check all soal answered
//   2. Calculate PG score
//   3. Check if essay exists
//   4. If essay: status = pending_essay
//   5. Else: final score & status
//   6. Create/update Nilai record
// Return: true if successful, false otherwise

public function finalizeGrades(int $ujianId): int
// Finalize all pending_essay grades
// Called by teacher publish button
// Logic:
//   - Find all Nilai with status='pending_essay'
//   - For each: calculate final score & status
//   - Update Nilai records
// Return: count of updated records

public function getGradingSummary(int $ujianId): array
// Get dashboard statistics for exam
// Return: [
//     'total_students' => int,
//     'graded' => int,
//     'pending_essay' => int,
//     'pending_submit' => int,
//     'average_score' => float,
//     'pass_rate' => float
// ]
```

### 2. Models

#### Nilai Model
```php
class Nilai extends Model
{
    use BelongsToTenant, HasFactory;
    
    protected $fillable = [
        'ujian_id', 'siswa_id', 'tenant_id',
        'nilai_otomatis', 'nilai_essay', 'nilai_akhir',
        'status'
    ];
    
    protected $casts = [
        'nilai_otomatis' => 'decimal:2',
        'nilai_essay' => 'decimal:2',
        'nilai_akhir' => 'decimal:2',
    ];
    
    public function ujian(): BelongsTo { }
    public function siswa(): BelongsTo { }
}
```

#### JawabanSiswa Model (Updated)
```php
class JawabanSiswa extends Model
{
    use HasFactory;
    
    // Add event binding
    protected static function boot()
    {
        parent::boot();
        static::observe(JawabanSiswaObserver::class);
    }
}
```

### 3. Observer (`app/Observers/JawabanSiswaObserver.php`)

**Automatic trigger for grading**

```php
class JawabanSiswaObserver
{
    public function updated(JawabanSiswa $jawaban)
    {
        // Check if is_submitted changed to true
        if ($jawaban->wasChanged('is_submitted') && $jawaban->is_submitted) {
            $this->triggerAutoGrading($jawaban);
        }
    }
    
    private function triggerAutoGrading(JawabanSiswa $jawaban)
    {
        // Check if all answers submitted for this exam
        $totalSoal = Soal::whereHas('ujians', function ($q) {
            $q->where('ujian_id', $jawaban->ujian_id);
        })->count();
        
        $submitted = JawabanSiswa::where('ujian_id', $jawaban->ujian_id)
            ->where('siswa_id', $jawaban->siswa_id)
            ->where('is_submitted', true)
            ->count();
        
        // All answered? → Auto-grade
        if ($submitted === $totalSoal) {
            app(GradingService::class)->autoGradeExam(
                $jawaban->ujian_id,
                $jawaban->siswa_id
            );
        }
    }
}
```

### 4. Controllers

#### NilaiController (`app/Http/Controllers/Guru/NilaiController.php`)

**Teacher grading workflow interface**

```php
public function index(Request $request): View
// GET /guru/nilai
// List all exams with grading summary
// Pagination: 15 per page
// Return: view with exams & summary stats

public function gradeExam(int $ujianId): View
// GET /guru/nilai/{ujianId}/grade
// Show exam with all students' answers
// Display 5 stat cards: total, graded, pending_essay, pending_submit, avg, pass_rate
// Return: view with student list & answer details

public function gradeQuestion(int $ujianId, int $soalId): View
// GET /guru/nilai/{ujianId}/soal/{soalId}
// Question-level grading (for mass essay grading)
// Show question + all student essay answers
// Inline grading inputs
// Return: view with student answers

public function submitGrade(Request $request, int $ujianId): RedirectResponse
// POST /guru/nilai/{ujianId}/submit-grade
// Save essay grade for one student
// Validate: siswa_id exists, nilai_essay 0-100
// Update Nilai record with nilai_essay
// Note: Don't recalculate yet (batch at publish)
// Return: redirect with success message

public function publishGrades(int $ujianId): RedirectResponse
// POST /guru/nilai/{ujianId}/publish
// Finalize all pending_essay grades
// Call GradingService::finalizeGrades($ujianId)
// Send notification to all students
// Return: redirect with completion message
```

### 5. Routes (`routes/guru.php`)

```php
// Grading Routes
Route::middleware(['auth', 'checkRole:guru', 'checkTenant'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {
        
        // Nilai (Grading) Management
        Route::prefix('nilai')->name('nilai.')->group(function () {
            Route::get('/', 'NilaiController@index')->name('index');
            Route::get('{ujian}/grade', 'NilaiController@gradeExam')->name('gradeExam');
            Route::get('{ujian}/soal/{soal}', 'NilaiController@gradeQuestion')->name('gradeQuestion');
            Route::post('{ujian}/submit-grade', 'NilaiController@submitGrade')->name('submitGrade');
            Route::post('{ujian}/publish', 'NilaiController@publishGrades')->name('publishGrades');
        });
    });
```

---

## 📊 Blade Views

### `guru/nilai/index.blade.php`

Exam list with grading summary.

**Features**:
- Cards per exam showing:
  - Exam title & category
  - Total students
  - Grading progress (X graded, Y pending essay, Z pending submit)
  - Average score
  - Pass rate %
- Action buttons: Grade, Publish, Statistics

### `guru/nilai/grade-exam.blade.php`

All students for one exam with their answers.

**Components**:
- 5 stat cards (total, graded, pending_essay, pending_submit, avg, pass_rate)
- Searchable student list with:
  - Student name & NIS
  - Status badge (submitted, graded, pending_essay)
  - Score display (if graded)
  - Collapsible answer details
- "Publish Grades" button (batch finalize)

### `guru/nilai/grade-question.blade.php`

Mass grading for one question's essay answers.

**Components**:
- Question display (read-only)
- Sortable student list by score
- Answer preview + inline grading form
- Keyboard shortcuts for fast grading

---

## 🧪 Testing & Verification

### Unit Tests (`tests/Feature/GradingTest.php`)

```php
✓ calculate_score_pg_weighted
✓ calculate_score_pg_no_pg_questions
✓ calculate_score_essay_clamped_0_100
✓ calculate_final_score_with_essay
✓ calculate_final_score_without_essay
✓ determine_status_lulus_70_plus
✓ determine_status_tidak_lulus_below_70
✓ auto_grade_exam_on_answer_submit
✓ auto_grade_exam_pending_essay_if_essay_exists
✓ finalize_grades_batch_update
✓ finalize_grades_count_correct
✓ grading_summary_statistics_correct
```

### Integration Tests (`tests/Feature/NilaiControllerTest.php`)

```php
✓ guru_can_list_ujians_for_grading
✓ guru_can_view_exam_grading_page
✓ guru_can_submit_essay_grade
✓ guru_cannot_submit_invalid_score
✓ guru_can_publish_grades
✓ non_exam_creator_cannot_grade
✓ grading_summary_accurate
✓ observer_triggers_auto_grading
✓ observer_waits_for_all_answers
✓ nilai_akhir_calculated_correctly
```

---

## 📈 Performance Considerations

### Query Optimization

```php
// Use eager loading for relationships
Nilai::with('ujian', 'siswa')
    ->where('ujian_id', $ujianId)
    ->paginate(15);

// Batch updates for publish
Nilai::where('ujian_id', $ujianId)
    ->where('status', 'pending_essay')
    ->update(['status' => 'lulus']);
```

### Caching

- Grade summary stats can be cached per exam (5 min)
- Invalidate on grade submit/publish

### Async Processing

- Consider Queue for batch finalize grades (large exams)

---

## 🔄 Integration with Other Phases

| Phase | Integration | Details |
|-------|-------------|---------|
| Phase 3 | User Management | Guru manages grades |
| Phase 4 | Exam/Question Mgmt | Use soal bobot for scoring |
| Phase 5 | Dashboard & Analytics | Show exam statistics |
| Phase 5 | News Integration | N/A |
| Phase 6 | Monitoring | Track grading progress |
| Phase 7 | Advanced Analytics | Question-level analytics |

---

## 🚀 Deployment Checklist

- [x] GradingService implemented
- [x] Nilai model with relationships
- [x] JawabanSiswaObserver triggers
- [x] NilaiController with workflow
- [x] Views for teacher grading interface
- [x] Routes configured
- [x] Validation in place (0-100 score)
- [x] Tests written & passing
- [x] Error handling & logging
- [x] Multi-tenant scoping
- [x] Authorization checks (guru can only grade own exams)

---

## ✨ Summary

**Phase 4.5** implements comprehensive automatic grading with:
- ✅ Weighted PG scoring system
- ✅ Manual essay grading workflow
- ✅ Automatic status determination (lulus/tidak_lulus)
- ✅ Observer pattern for auto-trigger
- ✅ Teacher grading interface
- ✅ Real-time statistics dashboard
- ✅ Batch grade finalization

**Status**: 🟢 **PRODUCTION READY**

---

**Documentation Version**: 1.0.0  
**Last Updated**: 2026-05-09  
**Scope**: Automatic Grading System
