<?php

namespace App;

use App\Exceptions\EmployeeException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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

    /**
     * @param int $offset
     * @param int $count
     * @param string $orderColumn
     * @param string $orderDirection
     * @param string $searchValue
     * @return Collection
     */
    static public function filter(
        int $offset = 0,
        int $count = 10,
        string $orderColumn = 'id',
        string $orderDirection = 'asc',
        string $searchValue = ''
    ): Collection {
        $queryBuilder = self::filterBuilder($orderColumn, $orderDirection, $searchValue);

        $queryBuilder->offset($offset)
            ->limit($count);
        return $queryBuilder->get();
    }

    static public function filterBuilder(
        string $orderColumn = 'id',
        string $orderDirection = 'asc',
        string $searchValue = ''
    ): Builder {
        $queryBuilder = self::query();

        return $queryBuilder
            ->orderBy($orderColumn, $orderDirection)
            ->join(Position::TABLE_NAME, 'employees.position_id', '=', 'positions.id')
            ->select('employees.*', 'positions.name as positionName')
            ->where('full_name', 'like', "%$searchValue%")
            ->orWhere('positions.name', 'like', "%$searchValue%")
            ->orWhere('salary', 'like', "%$searchValue%")
            ->orWhere('start_date', 'like', "%$searchValue%")
            ->orWhere('phone', 'like', "%$searchValue%")
            ->orWhere('email', 'like', "%$searchValue%")
            ->orderBy($orderColumn, $orderDirection);
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
