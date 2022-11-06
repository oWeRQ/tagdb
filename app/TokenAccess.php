<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TokenAccess extends Model
{
    protected $table = 'token_preset';

    public function token()
    {
        return $this->belongsTo('App\Token');
    }

    public function preset()
    {
        return $this->belongsTo('App\Preset');
    }
}
