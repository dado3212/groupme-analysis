$(document).ready(function() {
  $('#added').on('click', function() {
    var name = $(this).data('name');

    // Run PHP script
    $.post('php/check.php', {
      name: name, 
    })
    .done(function(data) {
      var res = $.parseJSON(data);
      console.log(res);

      if (res.response === "error") {
        console.log("Failed");
      } else {
        console.log("Succeeded!");

        $.post('php/analyze.php', {
          name: name,
        }).done(function(data) {
          var res = $.parseJSON(data);

          if (res.response === "error") {
            console.log("Failed");
          } else {
            console.log("Succeeded!");
            console.log(res.url);
          }
        });
      }
    });
  });
});