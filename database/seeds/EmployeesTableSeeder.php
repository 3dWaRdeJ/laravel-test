<?php

use App\Employee;
use App\Position;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class EmployeesTableSeeder extends Seeder
{
    /** @var Collection $allPositions */
    protected $allPositions;

    public function __invoke()
    {
        $this->allPositions = Position::all();
        return parent::__invoke();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employeeCreateCount = env('SEED_EMPLOYEE_COUNT', 50000);
        $this->createEmployees($employeeCreateCount);
    }

    /**
     * @param int $createCount
     */
    protected function createEmployees(int $createCount)
    {
        $maxLevel = $this->allPositions->max('level');
        $employeesByPositionId = [];
        for ($i = 0; $i < $createCount; $i++) {
            $position = $this->allPositions[$i % $this->allPositions->count()];
            if (isset($employeesByPositionId[$position->id]) == false) {
                $employeesByPositionId[$position->id] = new Collection();
            }
            $chief = null;
            if ($position->level < $maxLevel
                && isset($employeesByPositionId[$position->chief_position_id])
                && $employeesByPositionId[$position->chief_position_id] instanceof Collection
            ) {
                /** @var Collection $possibleChiefs */
                $possibleChiefs = $employeesByPositionId[$position->chief_position_id];
                if ($possibleChiefs->count() > 0) {
                    $chief = $possibleChiefs->random();
                }
            }

            $employeesByPositionId[$position->id]->push($this->createEmployee($position, $chief));
        }
    }

    /**
     * @param Position $position
     * @param Employee|null $chief
     * @return Employee
     */
    protected function createEmployee(Position $position, ?Employee $chief = null): Employee
    {
        $createArgs = [
            'position_id' => $position->id
        ];
        if ($chief instanceof Employee) {
            $createArgs['chief_id'] = $chief->id;
        }
        return factory(Employee::class)->create($createArgs);
    }
}
