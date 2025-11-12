<?php

namespace Src\Admin\Order\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'item_id' => 'required|exists:items,id',
            'price' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|in:pending,processing,shipped,delivered,cancelled',
            
            // Address fields
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            
            // Contact fields
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            
            // Additional fields
            'notes' => 'nullable|string',
            'weight' => 'nullable|numeric|min:0',
            'delivery_date' => 'nullable|date',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'item_id' => 'item',
            'address_line_1' => 'address line 1',
            'address_line_2' => 'address line 2',
            'postal_code' => 'postal code',
            'delivery_date' => 'delivery date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'item_id.exists' => 'The selected item does not exist.',
            'status.in' => 'The status must be one of: pending, processing, shipped, delivered, or cancelled.',
        ];
    }
}