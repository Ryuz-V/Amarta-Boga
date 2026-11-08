<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'restaurant_name', 'value' => 'Amarta Boga Resto & Cafe', 'group' => 'general'],
            ['key' => 'restaurant_tagline', 'value' => 'Cita Rasa Nusantara & Fusion Jepang', 'group' => 'general'],
            ['key' => 'restaurant_address', 'value' => 'Jl. Jenderal Sudirman Kav. 88, Senayan, Jakarta Selatan', 'group' => 'general'],
            ['key' => 'restaurant_phone', 'value' => '0812-3456-7890', 'group' => 'general'],
            ['key' => 'restaurant_email', 'value' => 'info@amartaboga.com', 'group' => 'general'],
            ['key' => 'currency_symbol', 'value' => 'Rp', 'group' => 'general'],

            // Tax & Service Charge
            ['key' => 'tax_enabled', 'value' => '1', 'group' => 'tax'],
            ['key' => 'tax_percentage', 'value' => '10', 'group' => 'tax'],
            ['key' => 'service_charge_enabled', 'value' => '1', 'group' => 'tax'],
            ['key' => 'service_charge_percentage', 'value' => '5', 'group' => 'tax'],

            // Thermal Receipt
            ['key' => 'receipt_paper_width', 'value' => '80mm', 'group' => 'receipt'], // 58mm or 80mm
            ['key' => 'receipt_header', 'value' => "AMARTA BOGA RESTO & CAFE\nJl. Sudirman Kav. 88, Jakarta Selatan\nTelp: 0812-3456-7890", 'group' => 'receipt'],
            ['key' => 'receipt_footer', 'value' => "Terima kasih atas kunjungan Anda!\nKritik & Saran WA: 0812-3456-7890\nInstagram: @amartaboga.resto", 'group' => 'receipt'],
            ['key' => 'receipt_show_tax', 'value' => '1', 'group' => 'receipt'],
            ['key' => 'receipt_show_service', 'value' => '1', 'group' => 'receipt'],

            // Kitchen / KDS
            ['key' => 'kds_sound_alert', 'value' => '1', 'group' => 'kitchen'],
            ['key' => 'kds_auto_refresh_sec', 'value' => '5', 'group' => 'kitchen'],
            ['key' => 'kds_warning_time_min', 'value' => '10', 'group' => 'kitchen'],
            ['key' => 'kds_danger_time_min', 'value' => '20', 'group' => 'kitchen'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'group' => $setting['group']]
            );
        }
    }
}
