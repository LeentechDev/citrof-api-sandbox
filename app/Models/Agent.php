<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    use HasFactory;

    protected $table = 'agents';

    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';

    protected $fillable = [
        'created',
        'modified',
        'account_no',
        'referral_id',
        'level',
        'upline_id',
        'agent_username',
        'first_name',
        'last_name',
        'username',
        'ma_convention',
        'batch_no',
        'password',
        'birth_date',
        'mobile',
        'email',
        'email_code',
        'email_code_expiration',
        'email_verified',
        'status',
        'credits',
        'commision_credit',
        'commission_rate',
        'commission_rate_type',
        'password_code',
        'password_code_expire',
        'password_change',
        'last_login_ip',
        'last_login',
        'long_lat',
        'activation_token',
        'dial_code',
        'country_code',
        'building',
        'street',
        'city',
        'province',
        'country',
        'alpha_2_code',
        'zip_code',
        'token',
        'min_load_threshold',
        'max_load_threshold',
        'total_loaded',
        'contact_email',
        'contact_no',
        'contact_fb_url',
    ];
}
