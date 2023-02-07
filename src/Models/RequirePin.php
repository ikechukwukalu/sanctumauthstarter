<?php

namespace Ikechukwukalu\Sanctumauthstarter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class RequirePin extends Model
{
    use HasFactory;

    protected $table = 'require_pins';

    protected $fillable = [
        'user_id',
        'uuid',
        'ip',
        'device',
        'payload',
        'method',
        'route_arrested',
        'redirect_to',
        'pin_validation_url',
        'approved_at',
        'cancelled_at',
        'expires_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
