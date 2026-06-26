<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEtapaPrecontractualRequest extends FormRequest
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
            'estado' => 'required|in:en_proceso,aprobado,rechazado',
            'documentos' => 'nullable|array',
            'documentos.*' => [
                'nullable',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    $allowedExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, $allowedExts)) {
                        $fail('El archivo "' . $value->getClientOriginalName() . '" debe ser de tipo: pdf, doc, docx, xls, xlsx, jpg, jpeg, png.');
                        return;
                    }

                    $mime = $value->getMimeType();
                    $allowedMimes = [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'image/webp',
                        'image/svg+xml',
                        'application/octet-stream',
                    ];

                    if (!in_array($mime, $allowedMimes)) {
                        $fail('El archivo "' . $value->getClientOriginalName() . '" no tiene un formato válido (' . $mime . ').');
                    }
                }
            ],
        ];
    }
}
