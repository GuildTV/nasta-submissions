window.JudgeScore = {

  BindValidator: function(){
    $("#scoreform").validate({
      // debug: true,
      submitHandler: () => window.JudgeScore.SaveScore(),
      rules: {
        score: {
          required: true,
          min: 0,
          max: 20
        },
        feedback: {
          required: true,
          minlength: 25
        },
      },
      messages: {
        score: {
          required: "Please provide a score",
          min: "Please provide a score between 0 and 20",
          max: "Please provide a score between 0 and 20",
        },
        feedback: {
          minlength: "Please enter at least 25 characters",
        }
      }
     });
  },

  SaveScore: function(){
    const id = $('#entryid').val();
    if (!id)
      return alert("Missing ID");

    const data = {
      score: $('#score').val(),
      feedback: $('#feedback').val(),
    };
    
    $.ajax({
      url: '/judge/entry/' + id + '/score',
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
  }

};