<?php 
namespace App\Http\Requests\Admin;

use App\Http\Requests\AjaxRequest;

class UserSaveRequest extends AjaxRequest {

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'name' => 'required|min:5|max:255',
      'email' => 'required|min:5|max:255',
      'password' => 'confirmed|min:5',
    ];
  }

}
