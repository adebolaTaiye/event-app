<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;


class EventRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'user_id' => $this->user()->id
        ]);

    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'image' => ['required', File::image()->min('1kb')->max('10mb') ],
            'total_ticket' => 'nullable|int',
            'available_ticket' => 'nullable|int',
            'date' =>'required|date|after:tomorrow',
            'registration_expires_at' => 'required|date|before_or_equal:date|after:tomorrow',
            'ticket_info' => 'nullable|array',
           
        ];
    }
}
