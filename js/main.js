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
          console.log(data);
          var res = $.parseJSON(data);

          if (res.response === "error") {
            console.log("Failed");
          } else {
            console.log("Succeeded!");
          }
        });
      }
    });
  });

  // Smooth scrolling through targets
  $('a[href*="#"]:not([href="#"])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    }
  });
});