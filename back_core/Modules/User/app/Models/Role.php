<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as ModelsRole;

// use Modules\User\Database\Factories\RoleFactory;

class Role extends ModelsRole
{
    use HasFactory;


    const ADMIN = 'admin';
    const EXPERT = 'expert';
    const VISITOR = 'visitor';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): RoleFactory
    // {
    //     // return RoleFactory::new();
    // }
}
