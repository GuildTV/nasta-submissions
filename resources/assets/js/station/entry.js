window.StationEntry = {

  BindValidator: function(){
    $("#entryform").validate({
      submitHandler: () => window.StationEntry.Submit(),
      rules: {
        entryname: {
          required: true,
          minlength: 5,
          maxlength: 255
        },
        entrydescription: {
          required: false,
          minlength: 10
        }
      }
     });
  },

  Submit: function(){
    const data = {
      name: $('#entryname').val(),
      description: $('#entrydescription').val(),
      rules: $('#entryrules:checked').length,
      submit: document.activeElement.id == "entrysubmit" ? 1 : 0,
    };

    $.ajax({
      url: '/station/categories/animation/submit',
      method: 'POST',
      data: data,
      success: function(res) {
        alert("Saved!");
      },
      error: function(res) {
        console.log(res);
        alert("An error occured");
      }
    });
  }

};