<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'center.name', 'value' => "Gabriela Children's Rehabilitation Center", 'group' => 'general', 'label' => 'Center Name'],
            ['key' => 'center.address', 'value' => '', 'group' => 'general', 'label' => 'Address'],
            ['key' => 'center.phone', 'value' => '', 'group' => 'general', 'label' => 'Phone'],
            ['key' => 'center.email', 'value' => '', 'group' => 'general', 'label' => 'Email'],
            ['key' => 'center.logo', 'value' => '', 'group' => 'general', 'label' => 'Logo Path'],
            ['key' => 'system.child_id_prefix', 'value' => 'GCRC', 'group' => 'system', 'label' => 'Child ID Prefix'],
            ['key' => 'system.session_timeout_minutes', 'value' => '30', 'type' => 'integer', 'group' => 'system', 'label' => 'Session Timeout'],
            ['key' => 'system.require_strong_passwords', 'value' => '1', 'type' => 'boolean', 'group' => 'system', 'label' => 'Require Strong Passwords'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}