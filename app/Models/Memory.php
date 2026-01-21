<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Memory extends Model
{
    protected $table = 'memory';
    protected $fillable = ['session_id', 'message'];
    protected $casts = ['message' => 'array'];
    
    // Desactivar timestamps porque la tabla no tiene created_at/updated_at
    public $timestamps = false;
}
