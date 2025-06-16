<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyUser extends Model
{
    protected $table = 'family_user';

    protected $fillable = [
        'family_id',
        'user_id',
        'role',
    ];
}
