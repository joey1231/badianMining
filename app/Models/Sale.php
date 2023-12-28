<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function expenses()
    {
        return $this->hasMany(SaleExpense::class);
    }
}
