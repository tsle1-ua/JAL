<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingRequest extends FormRequest
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
        $imageTypes = implode(',', config('services.upload_limits.allowed_image_types', ['jpg','jpeg','png','gif']));
        $maxSize = config('services.upload_limits.max_image_size', 2048);

        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'zip_code' => 'nullable|string|max:10',
            'price' => 'required|numeric|min:0|max:999999.99',
            'type' => 'required|in:apartamento,residencia,habitacion,casa,estudio',
            'square_meters' => 'nullable|integer|min:1',
            'current_occupants' => 'nullable|integer|min:0',
            'max_occupants' => 'nullable|integer|min:1',
            'phone' => 'nullable|string|max:20',
            'bedrooms' => 'required|integer|min:1|max:20',
            'bathrooms' => 'required|numeric|min:0.5|max:10',
            'available_from' => 'required|date|after_or_equal:today',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'images' => 'nullable|array|max:5',
            'images.*' => "image|mimes:$imageTypes|max:$maxSize",
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
            'address' => 'dirección',
            'city' => 'ciudad',
            'zip_code' => 'código postal',
            'price' => 'precio',
            'type' => 'tipo de propiedad',
            'square_meters' => 'metros cuadrados',
            'current_occupants' => 'ocupantes actuales',
            'max_occupants' => 'aforo máximo',
            'phone' => 'teléfono',
            'bedrooms' => 'número de habitaciones',
            'bathrooms' => 'número de baños',
            'available_from' => 'disponible desde',
            'latitude' => 'latitud',
            'longitude' => 'longitud',
            'images' => 'imágenes',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        $imageTypesText = implode(', ', config('services.upload_limits.allowed_image_types', ['jpg', 'jpeg', 'png', 'gif']));
        $maxSizeMB = (int) (config('services.upload_limits.max_image_size', 2048) / 1024);

        return [
            'title.required' => 'El título es obligatorio.',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'description.required' => 'La descripción es obligatoria.',
            'description.max' => 'La descripción no puede exceder los 2000 caracteres.',
            'address.required' => 'La dirección es obligatoria.',
            'city.required' => 'La ciudad es obligatoria.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un número válido.',
            'price.min' => 'El precio no puede ser negativo.',
            'type.required' => 'El tipo de propiedad es obligatorio.',
            'type.in' => 'El tipo de propiedad debe ser: apartamento, habitación, casa o estudio.',
            'bedrooms.required' => 'El número de habitaciones es obligatorio.',
            'bedrooms.integer' => 'El número de habitaciones debe ser un número entero.',
            'bedrooms.min' => 'Debe haber al menos 1 habitación.',
            'bathrooms.required' => 'El número de baños es obligatorio.',
            'bathrooms.numeric' => 'El número de baños debe ser un número válido.',
            'bathrooms.min' => 'Debe haber al menos 0.5 baños.',
            'available_from.required' => 'La fecha de disponibilidad es obligatoria.',
            'available_from.date' => 'La fecha de disponibilidad debe ser válida.',
            'available_from.after_or_equal' => 'La fecha de disponibilidad no puede ser anterior a hoy.',
            'images.array' => 'Las imágenes deben enviarse como un array.',
            'images.max' => 'No puedes subir más de 5 imágenes.',
            'images.*.image' => 'Cada archivo debe ser una imagen válida.',
            'images.*.mimes' => "Las imágenes deben ser de tipo: {$imageTypesText}.",
            'images.*.max' => "Cada imagen no puede exceder los {$maxSizeMB}MB.",
        ];
    }
}