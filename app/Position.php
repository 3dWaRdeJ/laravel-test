<?php

namespace App;

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
}
