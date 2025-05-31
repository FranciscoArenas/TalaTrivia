<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTriviaRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:draft,active,completed',
            'starts_at' => 'nullable|date|after:now',
            'ends_at' => 'nullable|date|after:starts_at',
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Verificar que todas las preguntas tengan respuestas correctas definidas
            if ($this->has('question_ids')) {
                $questionsWithoutCorrectAnswer = \App\Models\Question::whereIn('id', $this->question_ids)
                    ->whereDoesntHave('options', function($query) {
                        $query->where('is_correct', true);
                    })
                    ->exists();

                if ($questionsWithoutCorrectAnswer) {
                    $validator->errors()->add('question_ids', 'Todas las preguntas deben tener una respuesta correcta definida.');
                }
            }

            // Verificar que todos los usuarios sean jugadores
            if ($this->has('user_ids')) {
                $nonPlayerUsers = \App\Models\User::whereIn('id', $this->user_ids)
                    ->where('role', '!=', 'player')
                    ->exists();

                if ($nonPlayerUsers) {
                    $validator->errors()->add('user_ids', 'Solo se pueden asignar usuarios con rol de jugador.');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la trivia es requerido',
            'question_ids.required' => 'Debe seleccionar al menos una pregunta',
            'question_ids.*.exists' => 'Una o más preguntas seleccionadas no existen',
            'user_ids.required' => 'Debe asignar al menos un usuario',
            'user_ids.*.exists' => 'Uno o más usuarios seleccionados no existen',
            'starts_at.after' => 'La fecha de inicio debe ser posterior al momento actual',
            'ends_at.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
        ];
    }
}
