<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasAnyRole(['staff', 'admin']);
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:100',
            'order_type' => 'required|in:dine_in,take_away,takeaway,delivery',
            'table_id' => 'nullable|required_if:order_type,dine_in|exists:tables,id',
            'discount_code' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',

            // Cart Items
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1|max:100',
            'items.*.variant_id' => 'nullable|exists:menu_variants,id',
            'items.*.addon_ids' => 'nullable|array',
            'items.*.addon_ids.*' => 'exists:menu_addons,id',
            'items.*.notes' => 'nullable|string|max:255',

            // Payment Processing
            'payment_method' => 'required|in:cash,qris,debit,credit,split',
            'cash_tendered' => 'nullable|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',

            // Split payments array (optional for split bill)
            'split_payments' => 'nullable|array',
            'split_payments.*.method' => 'required_with:split_payments|in:cash,qris,debit,credit',
            'split_payments.*.amount' => 'required_with:split_payments|numeric|min:1',
            'split_payments.*.reference' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama pelanggan/tamu wajib diisi.',
            'order_type.required' => 'Tipe pesanan (Dine In / Takeaway) wajib dipilih.',
            'table_id.required_if' => 'Nomor meja wajib dipilih untuk pesanan Dine-In.',
            'items.required' => 'Keranjang pesanan tidak boleh kosong.',
            'items.min' => 'Pilih minimal satu menu untuk memproses pesanan.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'cash_tendered.min' => 'Nominal uang tunai tidak boleh kurang dari 0.',
        ];
    }
}
