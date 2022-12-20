<?php

namespace Ikechukwukalu\Sanctumauthstarter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialiteUserDeviceLogin extends Model
{
    use HasFactory;

    protected $table = 'socialite_user_device_logins';

    protected $fillable = [
        'user_uuid',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected $hidden = [];
}
