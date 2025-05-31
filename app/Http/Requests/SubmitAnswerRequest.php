<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAnswerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isPlayer();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question_id' => 'required|exists:questions,id',
            'question_option_id' => 'required|exists:question_options,id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $triviaId = $this->route('trivia');
            $questionId = $this->input('question_id');
            $optionId = $this->input('question_option_id');
            $userId = $this->user()->id;

            // Verificar que la pregunta pertenece a la trivia
            $triviaHasQuestion = \App\Models\Trivia::where('id', $triviaId)
                ->whereHas('questions', function($query) use ($questionId) {
                    $query->where('questions.id', $questionId);
                })
                ->exists();

            if (!$triviaHasQuestion) {
                $validator->errors()->add('question_id', 'La pregunta no pertenece a esta trivia.');
            }

            // Verificar que la opción pertenece a la pregunta
            $questionHasOption = \App\Models\Question::where('id', $questionId)
                ->whereHas('options', function($query) use ($optionId) {
                    $query->where('id', $optionId);
                })
                ->exists();

            if (!$questionHasOption) {
                $validator->errors()->add('question_option_id', 'La opción no pertenece a esta pregunta.');
            }

            // Verificar que el usuario no haya respondido ya esta pregunta
            $alreadyAnswered = \App\Models\UserAnswer::where('trivia_id', $triviaId)
                ->where('user_id', $userId)
                ->where('question_id', $questionId)
                ->exists();

            if ($alreadyAnswered) {
                $validator->errors()->add('question_id', 'Ya has respondido esta pregunta.');
            }

            // Verificar que el usuario esté asignado a la trivia
            $userAssigned = \App\Models\Trivia::where('id', $triviaId)
                ->whereHas('users', function($query) use ($userId) {
                    $query->where('users.id', $userId);
                })
                ->exists();

            if (!$userAssigned) {
                $validator->errors()->add('trivia', 'No estás asignado a esta trivia.');
            }

            // Verificar que la trivia esté activa
            $triviaActive = \App\Models\Trivia::where('id', $triviaId)
                ->where('status', 'active')
                ->exists();

            if (!$triviaActive) {
                $validator->errors()->add('trivia', 'La trivia no está activa.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'question_id.required' => 'La pregunta es requerida',
            'question_id.exists' => 'La pregunta no existe',
            'question_option_id.required' => 'La opción de respuesta es requerida',
            'question_option_id.exists' => 'La opción de respuesta no existe',
        ];
    }
}
