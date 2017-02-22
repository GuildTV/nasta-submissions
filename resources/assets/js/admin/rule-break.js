window.AdminRuleBreak = {

  SubmitFile: function(e){
    e = $(e);

    const id = e.find('.fileid').val();
    if (!id)
      return alert("Missing ID");

    const data = {
      result: e.find('#result :selected').val(),
      notes: e.find('#notes').val()
    };

    $.ajax({
      url: '/admin/rule-break/file/' + id + '/save',
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

  SubmitEntry: function(e){
    e = $(e);

    const id = e.find('.entryid').val();
    if (!id)
      return alert("Missing ID");

    const data = {
      result: e.find('#result :selected').val(),
      notes: e.find('#notes').val()
    };

    $.ajax({
      url: '/admin/rule-break/entry/' + id + '/save',
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

  StartTranscode: function(e){
    e = $(e);

    const id = e.attr('data-id')
    if (!id)
      return alert("Missing ID");

    $.ajax({
      url: '/admin/transcode/' + id + '/' + e.attr('data-profile'),
      method: 'POST',
      success: function(res) {
        bootbox.alert("Queued!", function(){
          window.location.reload();
        });
      },
      error: function(res) {
        console.log(res);
        bootbox.alert("An error occured");
      }
    });
  }

};