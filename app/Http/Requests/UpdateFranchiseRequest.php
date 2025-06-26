<?php

namespace App\Http\Requests;

use App\Models\Franchise;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdateFranchiseRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
      return $this->user()->can('update', $this->route('franchise'));
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
      /** @var Franchise $franchise */
      $franchise = $this->route('franchise');
      /** @var User $user */
      $user = $franchise->user;

    return [
      'name' => 'required|string|max:255',
      'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
      'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
      'maxcam_email' => ['required', 'string', 'email', 'max:255', Rule::unique(Franchise::class)->ignore($franchise->id)],
      'cnpj' => ['required', 'string', 'max:18', Rule::unique(Franchise::class)->ignore($franchise->id)],
      'max_client' => 'required|integer|min:0',
      'contract_start_date' => 'required|date',
      'actuation_region' => 'required|string|max:255',
      'document_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
      'observations' => 'nullable|string',
    ];
  }
}
