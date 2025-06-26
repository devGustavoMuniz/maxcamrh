<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\Collaborator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class StoreCollaboratorRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return $this->user()->can('create', Collaborator::class);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    $rules = [
      'user.name' => 'required|string|max:255',
      'user.email' => 'required|string|email|max:255|unique:users,email',
      'user.password' => ['required', 'confirmed', Rules\Password::defaults()],

      'collaborator.photo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      'collaborator.curriculum_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
      'collaborator.date_of_birth' => 'nullable|date',
      'collaborator.gender' => 'nullable|string|max:255',
      'collaborator.is_special_needs_person' => 'boolean',
      'collaborator.marital_status' => 'nullable|string|max:255',
      'collaborator.scholarity' => 'nullable|string|max:255',
      'collaborator.father_name' => 'nullable|string|max:255',
      'collaborator.mother_name' => 'nullable|string|max:255',
      'collaborator.nationality' => 'nullable|string|max:255',
      'collaborator.personal_email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('collaborators', 'personal_email')],
      'collaborator.business_email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('collaborators', 'business_email')],
      'collaborator.phone' => 'nullable|string|max:20',
      'collaborator.cellphone' => 'nullable|string|max:20',
      'collaborator.emergency_phone' => 'nullable|string|max:20',
      'collaborator.department' => 'nullable|string|max:255',
      'collaborator.position' => 'nullable|string|max:255',
      'collaborator.type_of_contract' => 'nullable|string|max:255',
      'collaborator.salary' => 'nullable|string|max:255',
      'collaborator.admission_date' => 'nullable|date',
      'collaborator.direct_superior_name' => 'nullable|string|max:255',
      'collaborator.hierarchical_degree' => 'nullable|string|max:255',
      'collaborator.observations' => 'nullable|string',
      'collaborator.contract_start_date' => 'nullable|date',
      'collaborator.contract_expiration' => 'nullable|date|after_or_equal:collaborator.contract_start_date',
      'collaborator.cpf' => ['nullable', 'string', 'max:20', Rule::unique('collaborators', 'cpf')],
      'collaborator.rg' => 'nullable|string|max:20',
      'collaborator.cnh' => ['nullable', 'string', 'max:20', Rule::unique('collaborators', 'cnh')],
      'collaborator.reservista' => 'nullable|string|max:30',
      'collaborator.titulo_eleitor' => 'nullable|string|max:30',
      'collaborator.zona_eleitoral' => 'nullable|string|max:10',
      'collaborator.pis_ctps_numero' => 'nullable|string|max:30',
      'collaborator.ctps_serie' => 'nullable|string|max:20',
      'collaborator.banco' => 'nullable|string|max:255',
      'collaborator.agencia' => 'nullable|string|max:10',
      'collaborator.conta_corrente' => 'nullable|string|max:20',

      'address.cep' => 'required_with:address.street,address.city,address.state|nullable|string|max:9',
      'address.street' => 'required_with:address.cep,address.city,address.state|nullable|string|max:255',
      'address.number' => 'nullable|string|max:20',
      'address.complement' => 'nullable|string|max:255',
      'address.neighborhood' => 'nullable|string|max:255',
      'address.state' => 'required_with:address.cep,address.street,address.city|nullable|string|max:2',
      'address.city' => 'required_with:address.cep,address.street,address.state|nullable|string|max:255',
    ];

    $user = $this->user();
    if ($user->role === UserRole::ADMIN || $user->role === UserRole::FRANCHISE) {
      $rules['collaborator.client_id'] = ['nullable', 'exists:clients,id', function ($attribute, $value, $fail) use ($user) {
        if ($user->role === UserRole::FRANCHISE) {
          if ($user->franchise && !Client::where('id', $value)->where('franchise_id', $user->franchise_id)->exists()) {
            $fail('O cliente selecionado não pertence a esta franquia.');
          }
        }
      }];
    }

    return $rules;
  }
}
