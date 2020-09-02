<?php

namespace App;

use App\Exceptions\PositionException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    const TABLE_NAME = 'positions';

    /**
     * @return Collection
     */
    public function getEmployees(): Collection
    {
        return $this->hasMany(Employee::class, 'position_id', 'id')->get();
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
