<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sharing extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(SharingItem::class);
    }

    public function expenses()
    {
        return $this->hasMany(SharingExpense::class);
    }

    public function shares()
    {
        return $this->hasMany(Shared::class);
    }
}
