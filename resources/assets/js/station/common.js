window.StationCommon = {

  ViewFile: function(e){
    const id = e.getAttribute("data-id");
    const name = e.getAttribute("data-name");
    const type = e.getAttribute("data-type");
    const url = e.getAttribute("data-url");
    const errors = e.getAttribute("data-errors");
    if (id == undefined || name == undefined || type == undefined || url == undefined)
      return false;

    const modal = $('#view-modal');
    modal.find('.modal-title').text('View ' + name);

    modal.find('.cannot-preview').css('display', type == "video" ? "none" : "block");
    modal.find('#preview-player').html("");
    modal.find('.download-file').attr('href', url);

    if (window.previewPlayer != undefined){
      window.previewPlayer.destroy();
      window.previewPlayer = undefined;
    }

    if (type == "video")
      window.previewPlayer = new Clappr.Player({
        width: '100%',
        mimeType: "video/mp4",
        source: url,
        parentId: "#preview-player"
      });

    const errHolder = modal.find('#errors');
    errHolder.empty();

    try {
      const errs = JSON.parse(errors);
      if (errs.length > 0)
        errHolder.append($('<h3>').text("Errors"));

      for(var i=0; i<errs.length; i++)
        errHolder.append($('<p>').text(errs[i]));

    } catch (e2){
      console.log(e2)
    }

    modal.modal();
  },

};