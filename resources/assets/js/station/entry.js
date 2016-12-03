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
    if (document.activeElement.id == "entryedit")
      return StationEntry.Edit();

    const data = {
      name: $('#entryname').val(),
      description: $('#entrydescription').val(),
      rules: $('#entryrules:checked').length,
      submit: document.activeElement.id == "entrysubmit" ? 1 : 0,
    };
    const category = $('#entrycategory').val();

    if (category == undefined || category.length == 0)
      return bootbox.alert("Unrecoverable error: Invalid category");

    if (data.submit == 0)
      return StationEntry.DoSubmit(category, data);

    if (data.rules == 0)
      return bootbox.alert("You must accept the rules before submitting your entry");

    bootbox.confirm({
      title: "Submit Entry",
      message: "Blah blah blah. Kill in progress uploads etc",
      callback: r => r ? StationEntry.DoSubmit(category, data) : null
    });
  },

  DoSubmit: function(category, data){
    $.ajax({
      url: '/station/categories/' + category + '/submit',
      method: 'POST',
      data: data,
      success: function(res) {
        // submitted entry, so reload to get everything be readonly
        // if (data.submit == 1)
          // return window.location.reload();

        alert("Saved!");
      },
      error: function(res) {
        console.log(res);
        alert("An error occured");
      }
    });
  },

  Edit: function(){
    const category = $('#entrycategory').val();

    if (category == undefined || category.length == 0)
      return alert("Unrecoverable error: Invalid category");

    bootbox.confirm({
      title: "Edit Entry",
      message: "Blah blah blah. Allow to make changes. Entry will count as late if done after the deadline",
      callback: r => {
        if (!r) return;

        $.ajax({
          url: '/station/categories/' + category + '/edit',
          method: 'POST',
          success: function(res) {
            // submitted entry, so reload to get everything be readonly
            window.location.reload();
          },
          error: function(res) {
            console.log(res);
            alert("An error occured");
          }
        });
      }
    });
  }

};