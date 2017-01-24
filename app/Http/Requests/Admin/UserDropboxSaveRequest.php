<?php 
namespace App\Http\Requests\Admin;

use App\Http\Requests\AjaxRequest;

class UserDropboxSaveRequest extends AjaxRequest {

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'account' => 'required|exists:dropbox_accounts,id',
      'url' => 'required|min:5|max:255',
      'folder' => 'required|min:5|max:255',
    ];
  }

}
