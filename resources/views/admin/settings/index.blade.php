<x-layouts.app title="Pengaturan Toko & Struk Thermal" subtitle="Konfigurasi identitas restoran, pajak, service charge, format struk, dan sistem dapur">
    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
            @csrf

            <!-- 1. Identitas Restoran -->
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-soft space-y-6">
                <div class="flex items-center space-x-3 pb-4 border-b border-gray-100">
                    <span class="text-xl">🏪</span>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Identitas Usaha & Restoran</h3>
                        <p class="text-xs text-gray-400">Informasi utama yang tampil pada header aplikasi dan struk kasir</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Restoran / Brand</label>
                        <input type="text" name="settings[restaurant_name]" value="{{ \App\Models\Setting::get('restaurant_name', 'Amarta Boga Resto & Cafe') }}" required
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900 font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tagline Slogan</label>
                        <input type="text" name="settings[restaurant_tagline]" value="{{ \App\Models\Setting::get('restaurant_tagline', 'Cita Rasa Nusantara & Fusion Jepang') }}"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Alamat Lengkap</label>
                    <textarea name="settings[restaurant_address]" rows="2"
                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900">{{ \App\Models\Setting::get('restaurant_address', 'Jl. Jenderal Sudirman Kav. 88, Senayan, Jakarta Selatan') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">No. Telepon / WA</label>
                        <input type="text" name="settings[restaurant_phone]" value="{{ \App\Models\Setting::get('restaurant_phone', '0812-3456-7890') }}"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Email Usaha</label>
                        <input type="email" name="settings[restaurant_email]" value="{{ \App\Models\Setting::get('restaurant_email', 'info@amartaboga.com') }}"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs text-gray-900">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Simbol Mata Uang</label>
                        <input type="text" name="settings[currency_symbol]" value="{{ \App\Models\Setting::get('currency_symbol', 'Rp') }}"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-bold text-brand-500">
                    </div>
                </div>
            </div>

            <!-- 2. Pajak & Service Charge -->
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-soft space-y-6">
                <div class="flex items-center space-x-3 pb-4 border-b border-gray-100">
                    <span class="text-xl">🧾</span>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Pajak Restoran & Service Charge</h3>
                        <p class="text-xs text-gray-400">Atur persentase pungutan otomatis pada transaksi kasir</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Tax -->
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-800">Pajak Restoran (PB1 / PPN)</span>
                            <input type="hidden" name="settings[tax_enabled]" value="0">
                            <input type="checkbox" name="settings[tax_enabled]" value="1" {{ \App\Models\Setting::get('tax_enabled', '1') == '1' ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-brand-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 mb-1">Persentase Pajak (%)</label>
                            <input type="number" step="0.01" name="settings[tax_percentage]" value="{{ \App\Models\Setting::get('tax_percentage', '10') }}"
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-mono font-bold text-gray-900">
                        </div>
                    </div>

                    <!-- Service Charge -->
                    <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-800">Biaya Layanan (Service Charge)</span>
                            <input type="hidden" name="settings[service_charge_enabled]" value="0">
                            <input type="checkbox" name="settings[service_charge_enabled]" value="1" {{ \App\Models\Setting::get('service_charge_enabled', '1') == '1' ? 'checked' : '' }}
                                class="w-4 h-4 rounded text-brand-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 mb-1">Persentase Service Charge (%)</label>
                            <input type="number" step="0.01" name="settings[service_charge_percentage]" value="{{ \App\Models\Setting::get('service_charge_percentage', '5') }}"
                                class="w-full px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-mono font-bold text-gray-900">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Format Struk Thermal Kasir -->
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-soft space-y-6">
                <div class="flex items-center space-x-3 pb-4 border-b border-gray-100">
                    <span class="text-xl">🖨️</span>
                    <div>
                        <h3 class="text-base font-black text-gray-900">Format Cetak Struk Thermal</h3>
                        <p class="text-xs text-gray-400">Atur header, footer, dan ukuran kertas printer kasir POS</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Ukuran Kertas Thermal Printer</label>
                    <div class="grid grid-cols-2 gap-4">
                        @php
                            $width = \App\Models\Setting::get('receipt_paper_width', '80mm');
                        @endphp
                        <label class="p-4 rounded-2xl border cursor-pointer flex items-center space-x-3 {{ $width === '80mm' ? 'bg-brand-50/50 border-brand-500' : 'bg-gray-50 border-gray-200' }}">
                            <input type="radio" name="settings[receipt_paper_width]" value="80mm" {{ $width === '80mm' ? 'checked' : '' }} class="text-brand-500">
                            <div>
                                <p class="text-xs font-bold text-gray-900">Lebar 80mm (Standar Resto)</p>
                                <p class="text-[11px] text-gray-400">Cocok untuk printer POS EPSON / thermal besar</p>
                            </div>
                        </label>
                        <label class="p-4 rounded-2xl border cursor-pointer flex items-center space-x-3 {{ $width === '58mm' ? 'bg-brand-50/50 border-brand-500' : 'bg-gray-50 border-gray-200' }}">
                            <input type="radio" name="settings[receipt_paper_width]" value="58mm" {{ $width === '58mm' ? 'checked' : '' }} class="text-brand-500">
                            <div>
                                <p class="text-xs font-bold text-gray-900">Lebar 58mm (Printer Mini / Mobile)</p>
                                <p class="text-[11px] text-gray-400">Cocok untuk printer bluetooth kasir portable</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Header Struk (Atas)</label>
                        <textarea name="settings[receipt_header]" rows="3"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono text-gray-900">{{ \App\Models\Setting::get('receipt_header', "AMARTA BOGA RESTO & CAFE\nJl. Sudirman Kav. 88, Jakarta Selatan\nTelp: 0812-3456-7890") }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Footer Struk (Bawah)</label>
                        <textarea name="settings[receipt_footer]" rows="3"
                            class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-2xl text-xs font-mono text-gray-900">{{ \App\Models\Setting::get('receipt_footer', "Terima kasih atas kunjungan Anda!\nKritik & Saran WA: 0812-3456-7890\nInstagram: @amartaboga.resto") }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Save Changes Button -->
            <div class="flex items-center justify-end">
                <button type="submit"
                    class="py-3.5 px-8 rounded-2xl bg-gradient-to-r from-brand-500 to-brand-600 hover:from-brand-600 text-white font-bold text-sm shadow-lg shadow-brand-500/25 transition-all">
                    Simpan Seluruh Pengaturan Toko
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
