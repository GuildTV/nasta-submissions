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

  ReloadFileList: function(){
    const frame = document.querySelector('#filelist');
    if (frame == null)
      return;

    frame.src = frame.getAttribute('data-src') + "?_=" + Date.now();
  },

  OpenFolder: function(){
    // link handles new tab, this wants to delay reload the file list
    setTimeout(window.StationEntry.ReloadFileList, 15 * 1000);
  },

  Submit: function(){
    const data = {
      name: $('#entryname').val(),
      description: $('#entrydescription').val(),
      rules: $('#entryrules:checked').length,
      submit: document.activeElement.id == "entrysubmit" ? 1 : 0,
    };
    const category = $('#entrycategory').val();

    if (category == undefined || category.length == 0)
      return alert("Unrecoverable error: Invalid category");

    $.ajax({
      url: '/station/categories/' + category + '/submit',
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