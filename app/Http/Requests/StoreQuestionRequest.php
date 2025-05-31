<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question' => 'required|string|min:10',
            'difficulty' => 'required|in:easy,medium,hard',
            'options' => 'required|array|min:2|max:6',
            'options.*.option_text' => 'required|string|max:255',
            'options.*.is_correct' => 'required|boolean',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $options = $this->input('options', []);
            $correctAnswers = collect($options)->where('is_correct', true)->count();

            if ($correctAnswers !== 1) {
                $validator->errors()->add('options', 'Debe haber exactamente una respuesta correcta.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'question.required' => 'La pregunta es requerida',
            'question.min' => 'La pregunta debe tener al menos 10 caracteres',
            'difficulty.required' => 'La dificultad es requerida',
            'difficulty.in' => 'La dificultad debe ser: easy, medium o hard',
            'options.required' => 'Las opciones son requeridas',
            'options.min' => 'Debe haber al menos 2 opciones',
            'options.max' => 'No puede haber más de 6 opciones',
            'options.*.option_text.required' => 'El texto de la opción es requerido',
            'options.*.is_correct.required' => 'Debe especificar si la opción es correcta',
        ];
    }
}
