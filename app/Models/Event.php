<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'created',
        'modified',
        'name',
        'date_time',
        'date',
        'time',
        'status',
        'weekly_summary_id',
        'arena_id',
        'media_server_id',
        'minimum_bet',
        'minimum_load',
        'first_declaration_start',
        'last_declaration_end',
        'total_rake',
        'total_surplus',
        'total_commission',
        'total_commission_surplus',
        'net_rake',
        'count_meron',
        'count_wala',
        'count_draw',
        'count_cancel',
        'sum_wala',
        'sum_meron',
        'banner_message',
        'banner_message_open',
        'banner_message_close',
        'banner_message_declaration',
        'is_bet_migrated',
        'is_fight_migrated',
        'status_agent_commission',
        'status_event_report',
        'no_fights',
        'operator_rake',
        'system_rake',
        'content_rake',
    ];

    protected $table = 'events';
}
