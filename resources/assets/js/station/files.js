window.StationFiles = {

  Delete: function(e) {
    const id = e.getAttribute("data-id");

    if (id == undefined)
      return;

    bootbox.confirm({
      title: "Delete File",
      message: "File will be deleted and removed from any entry even submitted",
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
  },

  Link: function(e) {
    const id = e.getAttribute("data-id");
    const name = e.getAttribute("data-name");

    if (id == undefined)
      return;

    bootbox.prompt({
      title: "Link File" + (name ? " - " + name : ""),
      inputType: 'select',
      inputOptions: window.OpenCategories,
      callback: function (r) {
        if (!r) return;

        $.ajax({
          url: '/station/files/' + id + '/link/' + r,
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
  }

};