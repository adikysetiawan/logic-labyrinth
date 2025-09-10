<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;

class EasyLevelsSeeder extends Seeder
{
    public function run(): void
    {
        $levels = $this->generateEasyLevels(50, 5, 8);
        foreach ($levels as $lvl) {
            Level::updateOrCreate(
                [
                    'code' => $lvl['code'],
                    'difficulty' => 'easy',
                ],
                [
                    'start_at' => $lvl['start_at'],
                    'correct_answer' => $lvl['correct_answer'],
                ]
            );
        }

        // Preview 5 contoh ke console artisan
        $this->command?->info("Preview 5 level mudah:");
        foreach (array_slice($levels, 0, 5) as $i => $p) {
            $this->command?->line(sprintf(
                "[%02d] start=(%d,%d) target=(%d,%d)\n%s\n---",
                $i+1,
                $p['start_at']['x'], $p['start_at']['y'],
                $p['correct_answer']['x'], $p['correct_answer']['y'],
                $p['code']
            ));
        }
    }

    private function generateEasyLevels(int $count, int $stepsMin, int $stepsMax): array
    {
        $levels = [];
        for ($i=0; $i<$count; $i++) {
            $maxSteps = mt_rand($stepsMin, $stepsMax);
            // titik awal acak
            $x = mt_rand(0,4); $y = mt_rand(0,4); $x0=$x; $y0=$y;

            $codeParts = [];
            $ops = ['U','D','L','R'];
            $name = ['U'=>'moveUp','D'=>'moveDown','L'=>'moveLeft','R'=>'moveRight'];

            // mapping pergerakan (selaras UI)
            $applyMove = function(string $op, int $n = 1) use (&$x,&$y) {
                for ($j=0; $j<$n; $j++) {
                    switch ($op) {
                        case 'U': $y = ($y - 1 + 5) % 5; break; // up
                        case 'D': $y = ($y + 1) % 5; break;     // down
                        case 'L': $x = ($x - 1 + 5) % 5; break; // left
                        case 'R': $x = ($x + 1) % 5; break;     // right
                    }
                }
            };

            $steps = 0;
            while ($steps < $maxSteps) {
                $dir = $ops[array_rand($ops)];
                $rand = mt_rand(1,100);

                // Pola 1: for-loop dengan variasi (peluang 25%)
                if ($rand <= 25 && ($steps + 2) <= $maxSteps) {
                    $n = min(mt_rand(2,4), $maxSteps - $steps);
                    $codeParts[] = "for i in range($n):";
                    $codeParts[] = "    {$name[$dir]}()";
                    $applyMove($dir, $n);
                    $steps += $n;
                }
                // Pola 2: if-else dengan 2 cabang (peluang 20%)
                else if ($rand <= 45 && ($steps + 2) <= $maxSteps) {
                    $condType = mt_rand(1,4);
                    switch ($condType) {
                        case 1:
                            $codeParts[] = 'if x % 2 == 0:';
                            $codeParts[] = '    moveUp()';
                            $codeParts[] = 'else:';
                            $codeParts[] = '    moveDown()';
                            if (($x % 2) == 0) { $applyMove('U', 1); } else { $applyMove('D', 1); }
                            $steps += 1;
                            break;
                        case 2:
                            $codeParts[] = 'if y == 0:';
                            $codeParts[] = '    moveLeft()';
                            $codeParts[] = 'else:';
                            $codeParts[] = '    moveRight()';
                            if ($y == 0) { $applyMove('L', 1); } else { $applyMove('R', 1); }
                            $steps += 1;
                            break;
                        case 3:
                            $codeParts[] = 'if x == 4:';
                            $codeParts[] = '    moveLeft()';
                            $codeParts[] = 'else:';
                            $codeParts[] = '    moveRight()';
                            if ($x == 4) { $applyMove('L', 1); } else { $applyMove('R', 1); }
                            $steps += 1;
                            break;
                        default:
                            $codeParts[] = 'if y == 4:';
                            $codeParts[] = '    moveUp()';
                            $codeParts[] = 'else:';
                            $codeParts[] = '    moveDown()';
                            if ($y == 4) { $applyMove('U', 1); } else { $applyMove('D', 1); }
                            $steps += 1;
                            break;
                    }
                }
                // Pola 3: for-loop di dalam if (peluang 15%)
                else if ($rand <= 60 && ($steps + 3) <= $maxSteps) {
                    $condType = mt_rand(1,2);
                    $n = min(mt_rand(2,3), $maxSteps - $steps - 1);
                    if ($condType == 1) {
                        $codeParts[] = 'if x < 3:';
                        $codeParts[] = "    for i in range($n):";
                        $codeParts[] = "        moveRight()";
                        if ($x < 3) { $applyMove('R', $n); $steps += $n; }
                    } else {
                        $codeParts[] = 'if y > 1:';
                        $codeParts[] = "    for i in range($n):";
                        $codeParts[] = "        moveUp()";
                        if ($y > 1) { $applyMove('U', $n); $steps += $n; }
                    }
                }
                // Pola 4: kombinasi if dengan 2 gerakan (peluang 15%)
                else if ($rand <= 75 && ($steps + 2) <= $maxSteps) {
                    $condType = mt_rand(1,3);
                    switch ($condType) {
                        case 1:
                            $codeParts[] = 'if x % 2 == 1:';
                            $codeParts[] = '    moveLeft()';
                            $codeParts[] = '    moveDown()';
                            if (($x % 2) == 1) { $applyMove('L', 1); $applyMove('D', 1); $steps += 2; }
                            break;
                        case 2:
                            $codeParts[] = 'if y % 2 == 0:';
                            $codeParts[] = '    moveUp()';
                            $codeParts[] = '    moveRight()';
                            if (($y % 2) == 0) { $applyMove('U', 1); $applyMove('R', 1); $steps += 2; }
                            break;
                        default:
                            $codeParts[] = 'if x + y > 4:';
                            $codeParts[] = '    moveLeft()';
                            $codeParts[] = '    moveUp()';
                            if (($x + $y) > 4) { $applyMove('L', 1); $applyMove('U', 1); $steps += 2; }
                            break;
                    }
                }
                // Pola 5: nested if sederhana (peluang 10%)
                else if ($rand <= 85 && ($steps + 2) <= $maxSteps) {
                    $codeParts[] = 'if x > 2:';
                    $codeParts[] = '    if y < 2:';
                    $codeParts[] = '        moveDown()';
                    $codeParts[] = '    else:';
                    $codeParts[] = '        moveUp()';
                    if ($x > 2) {
                        if ($y < 2) { $applyMove('D', 1); } else { $applyMove('U', 1); }
                        $steps += 1;
                    }
                }
                // Pola 6: gerakan dasar (sisanya)
                else {
                    $codeParts[] = $name[$dir] . '()';
                    $applyMove($dir, 1);
                    $steps += 1;
                }
            }

            $code = implode("\n", $codeParts);
            $levels[] = [
                'difficulty' => 'easy',
                'code' => $code,
                'start_at' => ['x'=>$x0,'y'=>$y0],
                'correct_answer' => ['x'=>$x,'y'=>$y],
            ];
        }
        return $levels;
    }
}
