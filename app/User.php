<?php

namespace App;

use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    const TABLE_NAME = 'users';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->attributes['api_token'] = str_random(20);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return User
     * @throws \Exception
     */
    static public function getRandom(): User
    {
        /** @var Builder $queryBuilder */
        $queryBuilder = self::query();
        $employee = $queryBuilder->inRandomOrder()->limit(1)->get();
        if ($employee->isEmpty()) {
            throw new \Exception('No users in table \'users\'');
        }
        return $employee->first();
    }
}
