<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = false;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => "required"
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'correo es campo requerido',
            'email.email' => 'El campor email debe ser un correo valido',
            'password.required' => 'password es requerida',
        ];
    }

    // /**
    //  * Get the "after" validation callables for the request.
    //  */
    // public function after(): array
    // {
    //     return [
    //         "meta" => [
    //             "success" => true,
    //             "errors" => []
    //         ]
    //     ];
    // }
}
