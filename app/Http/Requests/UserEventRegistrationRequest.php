<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserEventRegistrationRequest extends FormRequest
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
              'event_id' => 'required|exists:events,id',
              'ticket_type_id' => 'nullable|exists:ticket_types,id',
              'quantity' => 'nullable|int',
              'total' => 'nullable|int'
        ];
    }
}
