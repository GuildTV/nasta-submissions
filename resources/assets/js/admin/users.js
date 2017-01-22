window.AdminUsers = {

  Submit: function(){
    const id = $('#userid').val();
    if (!id)
      return alert("Missing ID");

    const data = {
      name: $('#username').val(),
      email: $('#useremail').val(),
      password: $('#userpassword').val(),
      password_confirmation: $('#userpassword_confirm:checked').val(),
    };

    $.ajax({
      url: '/admin/users/' + id + '/save',
      method: 'POST',
      data: data,
      success: function(res) {
        bootbox.alert("Saved!");
      },
      error: function(res) {
        console.log(res);
        bootbox.alert("An error occured");
      }
    });
  }

};