$(document).ready(function() {
  $('#added').on('click', function() {
    // Run PHP script
    $.post('php/check.php', {
      name: $(this).data('name'), 
    }, function(data) {
      console.log(data);
    });
  });
});