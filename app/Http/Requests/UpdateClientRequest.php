<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateClientRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
      return $this->user()->can('update', $this->route('client'));
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
      /** @var Client $client */
      $client = $this->route('client');
      /** @var User $clientUser */
      $clientUser = $client->user;

      $rules = [
          'name' => 'required|string|max:255',
          'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($clientUser->id)],
          'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
          'cnpj' => ['required', 'string', 'max:255', Rule::unique(Client::class)->ignore($client->id)],
          'test_number' => 'nullable|string|max:255',
          'contract_end_date' => 'nullable|date',
          'is_monthly_contract' => 'required|boolean',
          'phone' => 'nullable|string|max:255',
          'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
      ];

    if ($this->user()->role === UserRole::ADMIN) {
      $rules['franchise_id'] = 'nullable|exists:franchises,id';
    }

    return $rules;
  }
}
