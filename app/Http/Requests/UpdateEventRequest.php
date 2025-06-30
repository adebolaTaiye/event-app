<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateEventRequest extends FormRequest
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
            'name' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image' => ['nullable', File::image()->min('1kb')->max('10mb') ],
            'total_ticket' => 'nullable|int',
            'available_ticket' => 'nullable|int',
            'date' =>'nullable|date|after:tomorrow',
            'registration_expires_at' => 'nullable|date|before_or_equal:date|after:tomorrow',
            'ticket_info' => 'nullable|array'
        ];
    }
}
