<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    use HasFactory;

    protected $table = 'bets';

    public function player(){
        return $this->belongsTo(Player::class, 'player_id', 'id');
    }

    public function agent(){
        return $this->belongsTo(Agent::class, 'agent_id', 'id')->select(['id', 'account_no', 'username as agent_username']);
    }
}
