<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $table = 'Incomes';

    protected $fillable = [
        'user_id', 'name', 'amount', 'date_time', 'description'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
