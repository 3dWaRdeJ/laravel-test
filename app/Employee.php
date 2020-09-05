<?php

namespace App;

use App\Exceptions\EmployeeException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    const TABLE_NAME = 'employees';
    const DEFAULT_PHOTO_PATH = '/storage/default.png';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $admin = auth()->user();
        if ($admin instanceof User) {
            $this->attributes['admin_create_id'] = $admin->id;
            $this->attributes['admin_update_id'] = $admin->id;
        }
    }

    protected $fillable = [
        'full_name',
        'salary',
        'start_date',
        'phone',
        'email',
        'photo_path',
        'chief_id',
        'position_id',
        'admin_create_id',
        'admin_update_id'
    ];

    public function getPosition(): Position
    {
        return $this->belongsTo(Position::class, 'position_id')->get()->first();
    }

    /**
     * @param int $id
     * @param bool $withPosition
     * @return Employee
     * @throws EmployeeException
     */
    static public function getById(int $id, bool $withPosition = false): Employee
    {
        $queryBuilder = self::query()->where('id', $id);
        $employeeCol = $queryBuilder->get();
        if ($employeeCol->isEmpty()) {
            throw new EmployeeException('Employee with id ' . $id . ' doesn`t exist');
        }
        /** @var Employee $employee */
        $employee = $employeeCol->first();
        if ($withPosition) {
            $employee->position = $employee->getPosition();
        }
        return $employee;
    }

    static public function search(int $offset = 0, int $count = 10, bool $withChief = false, bool $withPosition = false)
    {
        /** @var Builder $queryBuilder */
        $queryBuilder = Employee::query();

        $employees = $queryBuilder->offset($offset)->limit($count)->get();

        if ($withPosition) {
            $employees->each(function (Employee $employee) {
                $employee->position = $employee->getPosition();
            });
        }

        if ($withChief) {
            $employees->each(function (Employee $employee) use ($withPosition){
                $chief = $employee->getChief();
                $employee->chief = $chief;
                if ($withPosition
                    && $chief instanceof Employee
                ) {
                    $chief->position = $chief->getPosition();
                }
            });
        }
        return $employees;
    }

    /**
     * @param Employee|null $chief
     * @return $this
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
        return $this;
    }

    /**
     * @return Employee|null
     */
    public function getChief(): ?Employee
    {
        return $this->belongsTo(Employee::class, 'chief_id', 'id')->get()->first();
    }

    /**
     * @return Collection
     */
    public function getSubEmployees(): Collection
    {
        return $this->hasMany(Employee::class, 'chief_id', 'id')->get();
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
        return $this;
    }

    /**
     * @param Position $position
     * @return $this
     */
    public function setPosition(Position $position)
    {
        $this->position_id = $position->id;
        $this->save();
        return $this;
    }

    /**
     * @param UploadedFile $photo
     * @return string
     * @throws FileNotFoundException
     */
    public function savePhoto(UploadedFile $photo): string
    {
        $photoName = auth()->user()->id . '-' . date('Y-m-d_His') . '.' . $photo->extension();
        Storage::put('public\\' . $photoName , $photo->get());
        $this->photo_path = '/storage/' . $photoName;
        return $this->photo_path;
    }

    public function deletePhoto()
    {
        $photoName = preg_replace('/^\/storage\//', '', $this->photo_path);
        if ($photoName !== 'default.png') {
            Storage::delete('public\\' . $photoName);
        }
        $this->photo_path = self::DEFAULT_PHOTO_PATH;
    }
}
