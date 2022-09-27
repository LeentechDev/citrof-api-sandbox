<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'created',
        'password',
        'account_no',
        'agent_id',
        'agent_username',
        'agent_level',
        'partner_id',
        'first_name',
        'last_name',
        'username',
        'password',
        'birth_date',
        'mobile',
        'email',
        'email_code',
        'email_code_expiration',
        'email_verified',
        'status',
        'credits',
        'odds_type',
        'daily_bet_limit',
        'daily_win_limit',
        'weekly_loss_limit',
        'weekly_win_limit',
        'total_bets_today',
        'total_bets_week',
        'total_win_today',
        'total_win_week',
        'total_loss_today',
        'total_loss_week',
        'password_code',
        'password_code_expire',
        'password_changed',
        'last_login_ip',
        'last_login',
        'activation_token',
        'building',
        'street',
        'city',
        'province',
        'country',
        'token',
        'jwt',
        'currency_display',
        'language',
        'ticket_number',
        'total_win_arc_week',
        'total_loss_arc_week',
        'total_bets_arc_week',
        'datetime_last_bet',
        'datetime_last_reset',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    protected $table = 'players';
}
