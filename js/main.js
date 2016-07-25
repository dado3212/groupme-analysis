$(document).ready(function() {
  $('#added').on('click', function() {
    // Run PHP script
    $.post('php/check.php', {
      name: $(this).data('name'), 
    })
    .done(function(data) {
      var res = $.parseJSON(data);

      if (res.response === "error") {
        console.log("Failed");
      } else {
        console.log("Succeeded");
      }
    });
  });
});