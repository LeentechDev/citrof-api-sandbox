<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'no',
        'player_id',
        'agent_id',
        'amount',
        'type',
        'event_id',
        'bet_id',
        'loading_id',
        'amount',
        'count_notification_id',
        'previous_credit',
        'current_credit',
        'description',
        'created',
        'modified',
    ];

    protected $table = 'transactions';

    public $timestamps = false;
}
