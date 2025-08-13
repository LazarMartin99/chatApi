<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddFriendRequest extends FormRequest
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
            'friend_id' => 'required|exists:users,id|different:' . auth()->id(),
        ];
    }

    public function messages(): array
    {
        return [
            'friend_id.required' => 'A felhasználó azonosító megadása kötelező.',
            'friend_id.exists' => 'A megadott felhasználó nem található.',
        ];
    }
}
