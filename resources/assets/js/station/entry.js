window.StationEntry = {

  BindValidator: function(){
    $("#entryform").validate({
      submitHandler: function(form) {
        window.StationEntry.Submit();
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
      complete: function(res) {

      },
      error: function(res) {

      }
    });
  }

};