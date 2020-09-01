<?php

namespace App;

use App\Exceptions\EmployeeException;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    const TABLE_NAME = 'employees';
    const DEFAULT_PHOTO_PATH = '/storage/default.png';

    public function getPosition(): Position
    {
        return $this->belongsTo(Position::class, 'position_id')->get()->first();
    }

    /**
     * @param Employee|null $chief
     * @throws EmployeeException
     */
    public function setChief(?Employee $chief)
    {
        $chief_id = null;
        if ($chief instanceof Employee) {
            if ($chief->getPosition()->level >= $this->getPosition()->level) {
                $chief_id = $chief->id;
            } else {
                throw new EmployeeException('Set wrong chief for employee, master level low then employee');
            }
        }
        $this->{'chief_id'} = $chief_id;
        $this->save();
    }

    /**
     * @param Employee|null $previousChief
     * @throws EmployeeException
     */
    public function setRandomChief(Employee $previousChief = null)
    {
        $position = $this->getPosition();
        $chiefPosition = $position->getChiefPosition();
        $possibleChiefs = $chiefPosition->getEmployees();
        $position->getEmployees()->each(function(Employee $item) use ($possibleChiefs) {
            if ($item->id !== $this->id)
                $possibleChiefs->add($item);
        });
        if ($previousChief instanceof Employee) {
            $possibleChiefs = $possibleChiefs->reject(function ($value, $key) use ($previousChief) {
                return $value->id == $previousChief->id;
            });
        }
        if ($possibleChiefs->isNotEmpty()) {
            $randomChief = $possibleChiefs->random(1)->first();
            $this->setChief($randomChief);
        }
    }
}
