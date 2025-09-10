<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;

class HardLevelsSeeder extends Seeder
{
    public function run(): void
    {
        $levels = $this->generateHardLevels(50, 8, 12);
        foreach ($levels as $lvl) {
            Level::updateOrCreate(
                [
                    'code' => $lvl['code'],
                    'difficulty' => 'hard',
                ],
                [
                    'start_at' => $lvl['start_at'],
                    'correct_answer' => $lvl['correct_answer'],
                ]
            );
        }

        // Preview 5 contoh ke console artisan
        $this->command?->info("Preview 5 level hard:");
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

    private function generateHardLevels(int $count, int $stepsMin, int $stepsMax): array
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
                $rand = mt_rand(1,100);

                // Pola 1: while loop dengan kondisi kompleks (20%)
                if ($rand <= 20 && ($steps + 4) <= $maxSteps) {
                    $whileType = mt_rand(1,3);
                    $iterations = min(mt_rand(2,3), ($maxSteps - $steps) / 2);
                    
                    switch ($whileType) {
                        case 1:
                            $codeParts[] = 'while x < 4 and y > 0:';
                            $codeParts[] = '    moveRight()';
                            $codeParts[] = '    moveUp()';
                            for ($w=0; $w<$iterations && $x<4 && $y>0; $w++) {
                                $applyMove('R', 1); $applyMove('U', 1); $steps += 2;
                            }
                            break;
                        case 2:
                            $codeParts[] = 'while x > 1 or y < 3:';
                            $codeParts[] = '    if x % 2 == 0:';
                            $codeParts[] = '        moveLeft()';
                            $codeParts[] = '    else:';
                            $codeParts[] = '        moveDown()';
                            for ($w=0; $w<$iterations && ($x>1 || $y<3); $w++) {
                                if (($x % 2) == 0) { $applyMove('L', 1); } else { $applyMove('D', 1); }
                                $steps += 1;
                            }
                            break;
                        default:
                            $codeParts[] = 'while not (x == 0 and y == 4):';
                            $codeParts[] = '    moveLeft()';
                            $codeParts[] = '    moveDown()';
                            for ($w=0; $w<$iterations && !($x==0 && $y==4); $w++) {
                                $applyMove('L', 1); $applyMove('D', 1); $steps += 2;
                            }
                            break;
                    }
                }
                // Pola 2: nested for loops (15%)
                else if ($rand <= 35 && ($steps + 4) <= $maxSteps) {
                    $outerLoop = min(mt_rand(2,3), ($maxSteps - $steps) / 2);
                    $innerLoop = 2;
                    $dir1 = $ops[array_rand($ops)];
                    $dir2 = $ops[array_rand($ops)];
                    
                    $codeParts[] = "for i in range($outerLoop):";
                    $codeParts[] = "    for j in range($innerLoop):";
                    $codeParts[] = "        {$name[$dir1]}()";
                    $codeParts[] = "    {$name[$dir2]}()";
                    
                    for ($o=0; $o<$outerLoop; $o++) {
                        for ($in=0; $in<$innerLoop; $in++) {
                            $applyMove($dir1, 1); $steps++;
                        }
                        $applyMove($dir2, 1); $steps++;
                    }
                }
                // Pola 3: complex if dengan and/or/not (18%)
                else if ($rand <= 53 && ($steps + 3) <= $maxSteps) {
                    $condType = mt_rand(1,4);
                    switch ($condType) {
                        case 1:
                            $codeParts[] = 'if x > 2 and y < 3:';
                            $codeParts[] = '    moveLeft()';
                            $codeParts[] = '    moveDown()';
                            $codeParts[] = '    moveUp()';
                            if ($x > 2 && $y < 3) {
                                $applyMove('L', 1); $applyMove('D', 1); $applyMove('U', 1); $steps += 3;
                            }
                            break;
                        case 2:
                            $codeParts[] = 'if x == 0 or y == 4:';
                            $codeParts[] = '    for i in range(2):';
                            $codeParts[] = '        moveRight()';
                            $codeParts[] = '    moveDown()';
                            if ($x == 0 || $y == 4) {
                                $applyMove('R', 2); $applyMove('D', 1); $steps += 3;
                            }
                            break;
                        case 3:
                            $codeParts[] = 'if not (x % 2 == 0 and y % 2 == 1):';
                            $codeParts[] = '    moveUp()';
                            $codeParts[] = '    moveRight()';
                            if (!(($x % 2) == 0 && ($y % 2) == 1)) {
                                $applyMove('U', 1); $applyMove('R', 1); $steps += 2;
                            }
                            break;
                        default:
                            $codeParts[] = 'if (x + y) % 3 == 0 and x != y:';
                            $codeParts[] = '    moveLeft()';
                            $codeParts[] = '    moveLeft()';
                            $codeParts[] = '    moveDown()';
                            if ((($x + $y) % 3) == 0 && $x != $y) {
                                $applyMove('L', 2); $applyMove('D', 1); $steps += 3;
                            }
                            break;
                    }
                }
                // Pola 4: if-elif-else kompleks (15%)
                else if ($rand <= 68 && ($steps + 3) <= $maxSteps) {
                    $codeParts[] = 'if x < 2:';
                    $codeParts[] = '    moveRight()';
                    $codeParts[] = '    moveRight()';
                    $codeParts[] = 'elif y > 2:';
                    $codeParts[] = '    moveUp()';
                    $codeParts[] = '    moveLeft()';
                    $codeParts[] = 'else:';
                    $codeParts[] = '    moveDown()';
                    
                    if ($x < 2) {
                        $applyMove('R', 2); $steps += 2;
                    } elseif ($y > 2) {
                        $applyMove('U', 1); $applyMove('L', 1); $steps += 2;
                    } else {
                        $applyMove('D', 1); $steps += 1;
                    }
                }
                // Pola 5: for loop dengan complex condition (12%)
                else if ($rand <= 80 && ($steps + 3) <= $maxSteps) {
                    $n = min(mt_rand(2,4), $maxSteps - $steps);
                    $condType = mt_rand(1,2);
                    
                    if ($condType == 1) {
                        $codeParts[] = "for i in range($n):";
                        $codeParts[] = '    if x % 2 == 0 and y < 4:';
                        $codeParts[] = '        moveDown()';
                        $codeParts[] = '    else:';
                        $codeParts[] = '        moveUp()';
                        
                        for ($f=0; $f<$n; $f++) {
                            if (($x % 2) == 0 && $y < 4) {
                                $applyMove('D', 1);
                            } else {
                                $applyMove('U', 1);
                            }
                            $steps++;
                        }
                    } else {
                        $codeParts[] = "for i in range($n):";
                        $codeParts[] = '    if not (x == 4 or y == 0):';
                        $codeParts[] = '        moveRight()';
                        $codeParts[] = '    else:';
                        $codeParts[] = '        moveLeft()';
                        
                        for ($f=0; $f<$n; $f++) {
                            if (!($x == 4 || $y == 0)) {
                                $applyMove('R', 1);
                            } else {
                                $applyMove('L', 1);
                            }
                            $steps++;
                        }
                    }
                }
                // Pola 6: triple nested conditions (10%)
                else if ($rand <= 90 && ($steps + 2) <= $maxSteps) {
                    $codeParts[] = 'if x > 1:';
                    $codeParts[] = '    if y < 3:';
                    $codeParts[] = '        if x + y > 3:';
                    $codeParts[] = '            moveLeft()';
                    $codeParts[] = '            moveUp()';
                    $codeParts[] = '        else:';
                    $codeParts[] = '            moveRight()';
                    $codeParts[] = '    else:';
                    $codeParts[] = '        moveDown()';
                    
                    if ($x > 1) {
                        if ($y < 3) {
                            if (($x + $y) > 3) {
                                $applyMove('L', 1); $applyMove('U', 1); $steps += 2;
                            } else {
                                $applyMove('R', 1); $steps += 1;
                            }
                        } else {
                            $applyMove('D', 1); $steps += 1;
                        }
                    }
                }
                // Pola 7: gerakan dasar (10%)
                else {
                    $dir = $ops[array_rand($ops)];
                    $codeParts[] = $name[$dir] . '()';
                    $applyMove($dir, 1);
                    $steps += 1;
                }
            }

            $code = implode("\n", $codeParts);
            $levels[] = [
                'difficulty' => 'hard',
                'code' => $code,
                'start_at' => ['x'=>$x0,'y'=>$y0],
                'correct_answer' => ['x'=>$x,'y'=>$y],
            ];
        }
        return $levels;
    }
}
