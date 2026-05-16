<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insert([
            // Phase 2: Branding
            ['key' => 'system_logo', 'value' => '', 'label' => 'System Logo Path', 'type' => 'image', 'created_at' => now(), 'updated_at' => now()],
            
            // Phase 3: Social Links
            ['key' => 'facebook_url', 'value' => 'https://facebook.com', 'label' => 'Facebook URL', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com', 'label' => 'Twitter URL', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com', 'label' => 'Instagram URL', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            
            // Phase 4: Notification Preferences
            ['key' => 'admin_email_alerts', 'value' => 'true', 'label' => 'Admin Email Alerts', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'auto_reply_enabled', 'value' => 'false', 'label' => 'Auto-Reply Enabled', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            
            // Phase 5: Advanced Security
            ['key' => 'session_timeout_duration', 'value' => '120', 'label' => 'Session Timeout Duration (Minutes)', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'system_logo',
            'facebook_url',
            'twitter_url',
            'instagram_url',
            'admin_email_alerts',
            'auto_reply_enabled',
            'session_timeout_duration'
        ])->delete();
    }
};
