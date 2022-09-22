<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loading extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'no',
        'player_id',
        'agent_id',
        'amount',
        'weekly_summary_id',
        'description',
        'status',
        'notes',
        'date_completed',
        'count_notification_id',
        'comment',
        'created',
        'modified',
    ];

    protected $table = 'loadings';

    public $timestamps = false;
}
