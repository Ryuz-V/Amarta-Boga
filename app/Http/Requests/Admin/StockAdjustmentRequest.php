<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'type' => ['required', 'in:in,out,adjustment,waste'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'cost_per_unit' => ['nullable', 'numeric', 'min:0'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'ingredient_id.required' => 'Bahan baku wajib dipilih.',
            'type.required' => 'Jenis pergerakan stok wajib dipilih.',
            'quantity.required' => 'Jumlah stok wajib diisi.',
            'quantity.min' => 'Jumlah stok harus lebih besar dari 0.',
        ];
    }
}
