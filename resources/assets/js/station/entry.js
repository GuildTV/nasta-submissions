window.StationEntry = {

  BindValidator: function(){
    $("#entryform").validate({
      debug: true,
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
      message: "By submitting your entry you acknowledge that your entry is complete. You can unsubmit your entry if you wish to make more changes",
      callback: r => r ? StationEntry.DoSubmit(category, data) : null
    });
  },

  DoSubmit: function(category, data){
    $.ajax({
      url: '/station/categories/' + category + '/submit',
      method: 'POST',
      data: data,
      success: function(res) {
          return window.location.reload();
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
      message: "This will change your submission from submitted to a draft. If you do not resubmit your entry before the deadline it will be counted as late",
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
  },

  ShowUpload: function(btn){
    const url = btn.getAttribute("data-url");
    const filename = btn.getAttribute("data-filename");
    if (!url)
      return alert("Missing upload url!")

    bootbox.confirm({
      title: "Upload Files",
      message: "A new window will now open, please follow the instructions to upload your submission. When the upload is complete, please close the window to return to this page. <br/>"
              +"Filenames should be of the format: " + filename,
      buttons: {
        confirm: {
          label: 'To the uploader!',
          className: 'btn-success'
        },
          cancel: {
            label: 'Cancel',
        }
      },
      callback: r => {
        if (!r) return;

        window.open(url, "upload_window");
      }
    });
  }

};