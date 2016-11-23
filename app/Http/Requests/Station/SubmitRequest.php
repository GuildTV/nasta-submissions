<?php 
namespace App\Http\Requests\Station;

use App\Http\Requests\AjaxRequest;

use Auth;
use Entrust;

class SubmitRequest extends AjaxRequest {

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'name' => 'required|min:5|max:255',
      'description' => '',
      'rule' => 'boolean',
      'submit' => 'boolean',
    ];
  }

  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return Auth::check() && Auth::user()->can('station');
  }

}
