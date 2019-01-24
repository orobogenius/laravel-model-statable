<?php

namespace Orobogenius\Statable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Orobogenius\Statable\Statable;

class User extends Model
{
    use Statable;

    protected $guarded = ['id'];

    public function stateAdmin()
    {
        return [
            'is_admin' => true
        ];
    }

    public function stateModerator()
    {
        return [
            'is_moderator' => true
        ];
    }

    public function stateSuperAdmin()
    {
        return [
            'is_super_admin' => function () {
                return $this->is_admin && $this->is_moderator;
            }
        ];
    }
}
