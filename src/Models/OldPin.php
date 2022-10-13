<?php

namespace Ikechukwukalu\Sanctumauthstarter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class OldPin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pin',
    ];

    protected $hidden = [
        'pin'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
