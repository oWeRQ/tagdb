<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Model;

class TokenAccess extends Model
{
    protected $table = 'token_preset';

    protected $fillable = [
        'token_id',
        'preset_id',
        'can_create',
        'can_read',
        'can_update',
        'can_delete',
    ];

    public function token()
    {
        return $this->belongsTo(Token::class);
    }

    public function preset()
    {
        return $this->belongsTo(Preset::class);
    }
}
