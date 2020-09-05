<?php

namespace App;

use App\Exceptions\PositionException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    const TABLE_NAME = 'positions';
    const MAX_LEVEL = 5;

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
        'name',
        'level',
        'chief_position_id',
        'admin_create_id',
        'admin_update_id'
    ];

    /**
     * @return Collection
     */
    public function getEmployees(): Collection
    {
        return $this->hasMany(Employee::class, 'position_id', 'id')->get();
    }

    public function setChiefPosition(?Position $position) {
        if ($position instanceof Position) {
            $this->chief_position_id = $position->id;
        } else {
            $this->chief_position_id = null;
        }
        $this->save();
    }

    /**
     * @return Position|null
     */
    public function getChiefPosition(): ?Position
    {
        $result = null;
        $position = $this->belongsTo(Position::class,'chief_position_id')->get();
        if ($position->isNotEmpty()) {
            $result = $position->first();
        }
        return $result;
    }

    /**
     * @return Collection
     */
    public function getSubPositions(int $minLevel = null): Collection
    {
        $subPositions = $this->hasMany(Position::class, 'chief_position_id')->get();
        if (is_int($minLevel)) {
            /** @var Position $subPosition */
            foreach ($subPositions as $subPosition) {
                if ($subPosition->level >= $minLevel) {
                    $subPositions = $subPositions->merge($subPosition->getSubPositions($minLevel));
                }
            }
        }
        return $subPositions;
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
        string $orderColumn = 'name',
        string $orderDirection = 'asc',
        string $searchValue = ''
    ): Collection
    {
        /** @var Collection $positions */
        $queryBuilder = self::filterBuilder($orderColumn, $orderDirection, $searchValue);
        $positions = $queryBuilder->offset($offset)->limit($count)->get();

        return $positions;
    }

    static public function filterBuilder(
        string $orderColumn = 'name',
        string $orderDirection = 'asc',
        string $searchValue = ''
    ): Builder {
        $queryBuilder = self::query();

        return $queryBuilder
            ->orderBy($orderColumn, $orderDirection)
            ->where('name', 'LIKE' , '%' . $searchValue . '%')
            ->orWhere('updated_at', 'LIKE', '%' . $searchValue . '%')
            ->orWhere('level', 'LIKE' , '%' . $searchValue . '%');
    }

    /**
     * @param int $id
     * @return Position
     * @throws PositionException
     */
    static public function getById(int $id): Position
    {
        $queryBuilder = self::query()->where('id', $id);
        /** @var Collection $position */
        $position = $queryBuilder->get();
        if ($position->isEmpty()) {
            throw new PositionException('Position with id ' . $id . ' doesn`t exist');
        }
        $position = $position->first();
        /** @var Position $position */
        return $position;
    }

    /**
     * @return Collection
     */
    public function getChiefPositions(): Collection
    {
        $chiefPositions = new Collection();
        $position = $this;
        do {
            $chiefPositions->push($position);
            $position = $position->getChiefPosition();
        }while ($position instanceof Position);
        return $chiefPositions;
    }
}
