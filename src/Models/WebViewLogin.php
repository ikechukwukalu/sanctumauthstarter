<?php

namespace Ikechukwukalu\Sanctumauthstarter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebViewLogin extends Model
{
    use HasFactory;

    protected $table = 'web_view_logins';

    protected $fillable = [
        'user_uuid',
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'used',
        'password',
        'salt',
        'type',
    ];

    protected $hidden = [];
}
