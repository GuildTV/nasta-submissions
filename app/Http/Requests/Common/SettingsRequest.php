<?php 
namespace App\Http\Requests\Common;

use App\Http\Requests\AjaxRequest;

use Auth;

class SettingsRequest extends AjaxRequest {

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'email' => 'required|min:5|max:255',
      'password' => 'confirmed|min:5',
    ];
  }

  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return Auth::check();
  }

}
