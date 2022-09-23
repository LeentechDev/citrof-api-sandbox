<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerLoadings extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'transaction_no',
        'player_id',
        'amount',
        'previous_credits',
        'current_credits',
        'type',
        'description'
    ];
}
