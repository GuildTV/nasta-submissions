window.JudgeDashboard = {

  PromptSave: function(form){
    bootbox.confirm("Are you sure you want to finalise this category? After doing so you will be unable to make any changes to the scores or feedback", ok => {
      if (!ok) return;

      window.JudgeDashboard.SaveResults(form);
    });
  },

  SaveResults: function(form){
    form = $(form);
    window.foreafs = form;
    const id = form.find('#category_id').val();
    if (!id)
      return alert("Missing ID");

    const data = {};

    const winId = form.find('#winner_id');
    if (winId.length != 0){
      data.winner_id = winId.val();

      const comment = form.find('#winner_comment');
      data.winner_comment = comment.length == 0 ? "" : comment.val();
    }

    const commendId = form.find('#commended_id');
    if (commendId.length != 0){
      data.commended_id = commendId.val();

      const comment = form.find('#commended_comment');
      data.commended_comment = comment.length == 0 ? "" : comment.val();
    }

    $.ajax({
      url: '/judge/finalize/' + id,
      method: 'POST',
      data: data,
      success: function(res) {
        bootbox.alert("Saved!", () => window.location.reload());
      },
      error: function(res) {
        console.log(res);
        bootbox.alert("An error occured");
      }
    });
  }

};