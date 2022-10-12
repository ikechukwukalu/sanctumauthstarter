<?php

namespace Ikechukwukalu\Sanctumauthstarter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class OldPassword extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'password',
    ];

    protected $hidden = [
        'password'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
