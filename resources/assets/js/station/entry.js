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

    if (!window.readonly)
      window.StationEntry._RunClock();
  },

  _RunClock: function() {
    const interval = 30;

    const div = $('<div>').addClass('countdown-circle center run-clock')
      .append($('<div>').addClass('count').text(interval))
      .append($('<div>').addClass('l-half'))
      .append($('<div>').addClass('r-half'));
    $('#countdown-holder').html(div);

    let n = interval-1;
    setInterval(function() {
      if (n >= 0) {
        $('.count').html(n--);
      } else { 
        $('.count').html(interval); n=interval-1;
        $('.countdown-circle ').removeClass('run-clock');
        window.StationEntry.ScrapeFiles();
        setTimeout(() => $('.countdown-circle ').addClass('run-clock'), 10);
      }
    }, 1000);
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
      message: "By submitting your entry you acknowledge that your entry is complete, and that all your files are shown above. You can unsubmit your entry if you wish to make more changes",
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
              +"Filenames should be of the format: " + filename + "<br />"
              +"You should not submit your entry until the entry page is showing all the files you have uploaded.",
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
  },

  DeleteFile: function(e) {
    const id = e.getAttribute("data-id");

    if (id == undefined)
      return false;

    bootbox.confirm({
      title: "Delete File",
      message: "File will be deleted and removed the entry",
      callback: r => {
        if (!r) return;

        $.ajax({
          url: '/station/files/' + id + '/delete',
          method: 'POST',
          success: function(res) {
            // submitted entry, so reload to get everything be readonly
            window.location.reload();
          },
          error: function(res) {
            console.log(res);
            bootbox.alert("An error occured whilst attempting to delete this file. <br /> Please reload the page and try again");
          }
        });
      }
    });

    return false;
  },

  ScrapeFiles: function(){
    const category = $('#entrycategory').val();
    if (category == undefined || category.length == 0)
      return;

    $.ajax({
      url: '/station/categories/' + category + '/files',
      method: 'GET',
      success: function(res) {
        const dest = $('#file-table-body');
        dest.empty();

        for (var i=0; i < res.length; i++) {
          const file = res[i];

          const row = $('<tr>');
          row.append($('<td>').text(file.name));
          row.append($('<td>').text(file.uploaded_at));

          if (!window.readonly)
            row.append(
              $('<button>')
                .addClass('btn btn-danger btn-small')
                .attr('data-id', file.id)
                .attr('onclick', 'window.StationEntry.DeleteFile(this);return false')
                .text('Delete')
            );

          dest.append(row);
        }

      },
      error: function(res) {
        console.log("Failed to scrape files", res);
      }
    })

  }

};