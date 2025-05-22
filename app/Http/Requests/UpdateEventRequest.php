<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'date' => 'required|date',
            'time' => 'nullable|date_format:H:i',
            'end_datetime' => 'nullable|date|after:date',
            'place_id' => 'nullable|exists:places,id',
            'category' => 'required|in:academico,cultural,deportivo,social,musical,gastronomico,tecnologico,artistico,voluntariado',
            'is_public' => 'boolean',
            'price' => 'nullable|numeric|min:0|max:999999.99',
            'max_attendees' => 'nullable|integer|min:1|max:10000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'título',
            'description' => 'descripción',
            'date' => 'fecha',
            'time' => 'hora',
            'end_datetime' => 'fecha y hora de finalización',
            'place_id' => 'lugar',
            'category' => 'categoría',
            'is_public' => 'visibilidad pública',
            'price' => 'precio',
            'max_attendees' => 'máximo de asistentes',
            'image' => 'imagen',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'description.max' => 'La descripción no puede exceder los 2000 caracteres.',
            'date.required' => 'La fecha es obligatoria.',
            'date.date' => 'La fecha debe ser válida.',
            'time.date_format' => 'La hora debe tener el formato HH:MM (ejemplo: 14:30).',
            'end_datetime.date' => 'La fecha de finalización debe ser válida.',
            'end_datetime.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio.',
            'place_id.exists' => 'El lugar seleccionado no existe.',
            'category.required' => 'La categoría es obligatoria.',
            'category.in' => 'La categoría debe ser una de las opciones válidas.',
            'is_public.boolean' => 'La visibilidad debe ser verdadero o falso.',
            'price.numeric' => 'El precio debe ser un número válido.',
            'price.min' => 'El precio no puede ser negativo.',
            'max_attendees.integer' => 'El máximo de asistentes debe ser un número entero.',
            'max_attendees.min' => 'Debe permitir al menos 1 asistente.',
            'max_attendees.max' => 'El máximo de asistentes no puede exceder 10,000.',
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o gif.',
            'image.max' => 'La imagen no puede exceder los 2MB.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Asegurar que is_public sea un booleano
        if ($this->has('is_public')) {
            $this->merge([
                'is_public' => $this->boolean('is_public'),
            ]);
        }

        // Limpiar el precio si está vacío
        if ($this->price === '') {
            $this->merge([
                'price' => 0,
            ]);
        }
    }
}