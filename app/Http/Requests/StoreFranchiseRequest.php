<?php

namespace App\Http\Requests;

use App\Models\Franchise;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class StoreFranchiseRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return $this->user()->can('create', Franchise::class);
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
      'email' => 'required|string|email|max:255|unique:users,email',
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
      'maxcam_email' => 'required|string|email|max:255|unique:franchises,maxcam_email',
      'cnpj' => 'required|string|max:18|unique:franchises,cnpj',
      'max_client' => 'required|integer|min:0',
      'contract_start_date' => 'required|date',
      'actuation_region' => 'required|string|max:255',
      'document_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
      'observations' => 'nullable|string',
    ];
  }
}
