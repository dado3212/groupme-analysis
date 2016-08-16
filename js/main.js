$(document).ready(function() {
  // Handle tab switching for demo statistics
  $('.demo .tab').on('click', function() {
    $('.demo > div').css('display', 'none');
    $('.demo .' + $(this).data('name')).css('display', 'flex');
  });

  // Handle 'Added' button functionality
  $('#added').on('click', function() {
    var name = $(this).data('name');

    // Start spinner
    $('#spinner').css('display', 'inline-block');

    // Check to see if the bot is in a group with that name
    $.post('php/check.php', {
      name: name, 
    })
    .done(function(data) {
      var res = $.parseJSON(data);

      // Error handling
      if (res.response === 'error') {
        $('#alert').removeClass().addClass('error');
        $('#alert').html(res.message);

        $('#spinner').css('display', 'none');
      // Found group
      } else {
        $('#alert').removeClass().addClass('success');
        $('#alert').html('Found group!  Beginning to analyze...');

        // Starts analyzing, stores to DB, returns update when done
        $.post('php/analyze.php', {
          name: name,
        }).done(function(data) {
          var res = $.parseJSON(data);

          if (res.response === "error") {
            $('#alert').removeClass().addClass('error');

            $('#alert').html(res.message);

            $('#spinner').css('display', 'none');
          } else {
            $('#alert').removeClass().addClass('success');

            // Generate a new code
            $.get('php/code.php')
            .done(function(data) {
              var code = $.parseJSON(data).code;

              $('#added').data('name', code);
              $('.well').html(code);

              $('#alert').html(res.message + '  New code generated.');

              $('#spinner').css('display', 'none');
            }).fail(function() {
              $('#alert').html(res.message + '  Reload the page for a new code.');

              $('#spinner').css('display', 'none');
            });
          }
        });
      }
    })
    .fail(function() {
      $('#alert').html('Something went wrong.  Contact the site administrator.');

      $('#spinner').css('display', 'none');
    });
  });

  // Smooth scrolling through targets (source: https://css-tricks.com/snippets/jquery/smooth-scrolling/)
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