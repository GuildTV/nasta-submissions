<?php 
namespace App\Http\Requests\Judge;

use App\Http\Requests\AjaxRequest;

class ScoreRequest extends AjaxRequest {

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'score' => 'required|integer|min:0|max:20',
      'feedback' => 'min:25',
    ];
  }

}
