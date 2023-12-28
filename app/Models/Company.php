<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    //protected $connection = 'mysql_admin';
    use SoftDeletes;

    public function users()
    {
        return $this->hasMany(User::class);
    }

}
