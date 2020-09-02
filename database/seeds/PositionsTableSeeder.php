<?php

use App\Position;
use Illuminate\Database\Seeder;

class PositionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createPosition();
    }

    private function createPosition(Position $chiefPosition = null)
    {
        $level = Position::MAX_LEVEL;
        if ($chiefPosition instanceof Position) {
            $level = $chiefPosition->level - 1;
            if ($level < 1) {
                return;
            }
        }

        factory(Position::class, rand(1,5))
            ->create([
                'level' => $level,
                'chief_position_id' => $chiefPosition->id ?? null
            ])
            ->each(function (Position $position) {
                $this->createPosition($position);
            });
    }
}
