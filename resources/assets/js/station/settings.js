window.StationSettings = {

  BindValidator: function(){
    $("#entryform").validate({
      // debug: true,
      submitHandler: () => window.StationSettings.Submit(),
      rules: {
        useremail: {
          required: true,
          email: true,
          minlength: 5,
          maxlength: 255
        },
        userpassword: {
          minlength: 5
        },
        userpassword_confirm: {
          minlength: 5,
          equalTo: "#userpassword"
        },
      },
      messages: {
        useremail: "Please enter a valid email address",
        userpassword: {
          required: "Please provide a password",
          minlength: "Your password must be at least 5 characters long"
        },
        userpassword_confirm: {
          required: "Please provide a password",
          minlength: "Your password must be at least 5 characters long",
          equalTo: "Please enter the same password as above"
        }
      }
     });
  },

  Submit: function(){
    const data = {
      email: $('#useremail').val(),
      password: $('#userpassword').val(),
      password_confirmation: $('#userpassword_confirm:checked').val(),
    };

    $.ajax({
      url: '/station/settings',
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
  },

};