<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * NOTE: Seeder ini TIDAK otomatis dipanggil. Ia hanya akan membuat data
     * saat kamu menjalankan: php artisan db:seed --class=LevelsSeeder
     */
    public function run(): void
    {
        // Konfigurasi jumlah level per tingkat kesulitan
        $easyCount = 50;
        $hardCount = 50;

        // Generator level memastikan jawaban benar (x,y) sesuai rute di code
        $easy = $this->generateLevels('easy', $easyCount, stepsMin: 5, stepsMax: 7);
        $hard = $this->generateLevels('hard', $hardCount, stepsMin: 10, stepsMax: 14);

        // Simpan ke DB (idempotent berdasarkan hash code+difficulty+answer)
        foreach (array_merge($easy, $hard) as $lvl) {
            Level::updateOrCreate(
                [
                    // Kunci unik logis agar rerun tidak menduplikasi
                    'code' => $lvl['code'],
                    'difficulty' => $lvl['difficulty'],
                ],
                [
                    'start_at' => $lvl['start_at'] ?? ['x' => 0, 'y' => 0],
                    'correct_answer' => $lvl['correct_answer'],
                ]
            );
        }
    }

    /**
     * Membuat sekumpulan level dengan rute yang valid pada grid 5x5.
     * - Titik awal acak (x0,y0) dalam 0..4.
     * - Pergerakan wrap/tembus dinding (mod 5) — SELARAS DENGAN UI:
     *   - moveRight: x = (x + 1) mod 5
     *   - moveLeft : x = (x - 1 + 5) mod 5
     *   - moveUp   : y = (y - 1 + 5) mod 5   // ke atas mengurangi y
     *   - moveDown : y = (y + 1) mod 5       // ke bawah menambah y
     * - Gaya code:
     *   - easy: langkah sederhana + for loop ringan / kondisi ringan.
     *   - hard: rute lebih panjang + kombinasi loop/kondisi lebih sering.
     * - correct_answer adalah koordinat akhir rute dari titik awal.
     */
    private function generateLevels(string $difficulty, int $count, int $stepsMin, int $stepsMax): array
    {
        $levels = [];
        for ($i = 0; $i < $count; $i++) {
            $maxSteps = mt_rand($stepsMin, $stepsMax);

            // Titik awal acak
            $x = mt_rand(0, 4);
            $y = mt_rand(0, 4);
            $x0 = $x; $y0 = $y;

            // Tanpa header komentar; langsung mulai dari instruksi
            $codeParts = [];

            // Preferensi kompleksitas berbeda untuk easy vs hard
            $useLoopChance = ($difficulty === 'hard') ? 55 : 20;   // %
            $useCondChance = ($difficulty === 'hard') ? 50 : 25;   // %

            // Helper applyMove dengan wrap
            $applyMove = function(string $op, int $n = 1) use (&$x, &$y) {
                for ($j = 0; $j < $n; $j++) {
                    switch ($op) {
                        case 'U': $y = ($y - 1 + 5) % 5; break;               // moveUp (ke atas)
                        case 'D': $y = ($y + 1) % 5; break;                   // moveDown (ke bawah)
                        case 'L': $x = ($x - 1 + 5) % 5; break;               // moveLeft
                        case 'R': $x = ($x + 1) % 5; break;                   // moveRight
                    }
                }
            };

            $ops = ['U','D','L','R'];
            $name = ['U' => 'moveUp', 'D' => 'moveDown', 'L' => 'moveLeft', 'R' => 'moveRight'];

            $stepsMade = 0;
            while ($stepsMade < $maxSteps) {
                $dir = $ops[array_rand($ops)];
                $doLoop = (mt_rand(1,100) <= $useLoopChance) && ($stepsMade + 2 <= $maxSteps);
                $doCond = (mt_rand(1,100) <= $useCondChance);

                // Khusus HARD: peluang menyisipkan pola while+if+for seperti contoh user
                if ($difficulty === 'hard' && mt_rand(1,100) <= 30 && ($stepsMade + 4 <= $maxSteps)) {
                    // Batasi jumlah iterasi agar aman
                    $wIter = min(mt_rand(2, 4), $maxSteps - $stepsMade);
                    $codeParts[] = 'while y > 0:';
                    $codeParts[] = '    if x % 2 == 0:';
                    $codeParts[] = '        for i in range(2): moveRight()';
                    $codeParts[] = '        moveDown()';
                    $codeParts[] = '    else:';
                    $codeParts[] = '        moveLeft()';
                    $codeParts[] = '        moveDown()';
                    // Simulasikan wIter langkah siklus while
                    for ($wi = 0; $wi < $wIter; $wi++) {
                        if (($x % 2) == 0) {
                            // for i in range(2): moveRight()
                            $applyMove('R', 2);
                            // moveDown
                            $applyMove('D', 1);
                            $stepsMade += 3;
                        } else {
                            // moveLeft(); moveDown()
                            $applyMove('L', 1);
                            $applyMove('D', 1);
                            $stepsMade += 2;
                        }
                        if ($stepsMade >= $maxSteps) { break; }
                    }
                    continue; // lanjut iterasi while utama
                }

                if ($doLoop) {
                    $repeat = min(mt_rand(2, 3), $maxSteps - $stepsMade);
                    // Tulis for-loop dalam dua baris: header + body terindent 4 spasi
                    $codeParts[] = "for i in range($repeat):";
                    $codeParts[] = "    {$name[$dir]}()";
                    $applyMove($dir, $repeat);
                    $stepsMade += $repeat;
                } else {
                    $applyMove($dir, 1);
                    $codeParts[] = $name[$dir] . '()';
                    $stepsMade += 1;
                }

                if ($doCond && $stepsMade < $maxSteps) {
                    // Tambah kondisi ringan yang tidak mengubah langkah terlalu banyak
                    $condType = mt_rand(1,3);
                    switch ($condType) {
                        case 1:
                            $codeParts[] = 'if x % 2 == 0:';
                            $codeParts[] = '    moveUp()';
                            if ($x % 2 == 0) { $applyMove('U', 1); $stepsMade++; }
                            break;
                        case 2:
                            $codeParts[] = 'if y == 0:';
                            $codeParts[] = '    moveLeft()';
                            if ($y == 0) { $applyMove('L', 1); $stepsMade++; }
                            break;
                        default:
                            $codeParts[] = 'if x == 4:';
                            $codeParts[] = '    moveDown()';
                            if ($x == 4) { $applyMove('D', 1); $stepsMade++; }
                            break;
                    }
                }
            }

            $code = implode("\n", $codeParts);

            $levels[] = [
                'difficulty' => $difficulty,
                'code' => $code,
                'start_at' => ['x' => $x0, 'y' => $y0],
                'correct_answer' => ['x' => $x, 'y' => $y],
            ];
        }
        return $levels;
    }
}
