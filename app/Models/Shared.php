<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shared extends Model
{
    use HasFactory;

    protected $table = 'shared';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function share()
    {
        return $this->belongsTo(Sharing::class, 'sharing_id');
    }
}
