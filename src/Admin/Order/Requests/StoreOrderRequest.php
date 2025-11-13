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
            'customer_name' => 'required|string|max:255',
            'status' => 'nullable|string|in:pending,processing,shipped,delivered,cancelled',
            
            // Address fields
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:255',
            
            // Contact fields
            'contact_number_one' => 'nullable|string|max:20',
            'contact_number_two' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            
            // Additional fields
            'other' => 'nullable|string',
            'due_date' => 'nullable|date',
            'lead_from' => 'nullable|string|in:facebook,whatsapp,advertisement,other',
            'notes' => 'nullable|string',
            
            // Order items
            'order_items' => 'required|array|min:1',
            'order_items.*.product_id' => 'required|exists:items,id',
            'order_items.*.qty' => 'required|integer|min:1',
            'order_items.*.sale_amount' => 'nullable|numeric|min:0',
            'order_items.*.del_fee' => 'nullable|numeric|min:0',
            'order_items.*.is_invoiced' => 'nullable|boolean',
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
            'customer_name' => 'customer name',
            'contact_number_one' => 'primary contact number',
            'contact_number_two' => 'secondary contact number',
            'due_date' => 'due date',
            'lead_from' => 'lead source',
            'order_items.*.product_id' => 'product',
            'order_items.*.qty' => 'quantity',
            'order_items.*.sale_amount' => 'sale amount',
            'order_items.*.del_fee' => 'delivery fee',
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
            'order_items.required' => 'At least one order item is required.',
            'order_items.*.product_id.exists' => 'The selected product does not exist.',
            'status.in' => 'The status must be one of: pending, processing, shipped, delivered, or cancelled.',
            'lead_from.in' => 'The lead source must be one of: facebook, whatsapp, advertisement, or other.',
        ];
    }
}