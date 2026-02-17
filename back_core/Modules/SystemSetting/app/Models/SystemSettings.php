<?php

namespace Modules\SystemSetting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Server\Database\Factories\SystemSettingsFactory;

class SystemSettings extends Model
{
    use HasFactory;

    protected $casts = [
        'monitoring_attribute' => 'json'
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'monitoring_attribute',
        'is_login_2FA',
        'is_login_sms',
        'config_connection_sms',
        'orginal_vm_ip',
        'active_online_capcha',
        'recaptcha_secret_key',
        'recaptcha_site_name',

        'subscriber_address',
        'kpi_address',
        'kpi_file_name',
    ];

}
