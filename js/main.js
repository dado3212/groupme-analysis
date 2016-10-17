$(document).ready(function() {
  // Handle tab switching for demo statistics
  $('.demo .tab').on('click', function() {
    $('.demo > div').css('display', 'none');
    $('.demo .' + $(this).data('name')).css('display', 'flex');
  });

  // Handle 'Added' button functionality
  $('#added').on('click', function() {
    // Ignore clicks if still processing (try and avoid repeats)
    if (!$(this).hasClass('disabled')) {
      $(this).addClass('disabled');

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
          $(this).removeClass('disabled');
        // Found group
        } else {
          $('#alert').removeClass().addClass('success');
          $('#alert').html('Found group!  Beginning to analyze...');

          // Starts analyzing, stores to DB, returns update when done
          $.post('php/analyze.php', {
            name: name,
          }).done(function(data) {
            try {
              var res = $.parseJSON(data);

              if (res.response === "error") {
                $('#alert').removeClass().addClass('error');

                $('#alert').html(res.message);

                $('#spinner').css('display', 'none');
                $(this).removeClass('disabled');
              } else {
                $('#alert').removeClass().addClass('success');

                // Generate a new code
                $.get('php/code.php')
                .done(function(data) {
                  try {
                    var code = $.parseJSON(data).code;

                    $('#added').data('name', code);
                    $('.well').html(code);

                    $('#alert').html(res.message + '  New code generated.');

                    $('#spinner').css('display', 'none');
                    $(this).removeClass('disabled');
                  } catch(err) {
                    console.log(err);
                    $('#alert').html(res.message + '  Reload the page for a new code.');

                    $('#spinner').css('display', 'none');
                    $(this).removeClass('disabled');
                  }
                }).fail(function() {
                  $('#alert').html(res.message + '  Reload the page for a new code.');

                  $('#spinner').css('display', 'none');
                  $(this).removeClass('disabled');
                });
              }
            } catch(err) {
              console.log(err);
              $('#alert').removeClass().addClass('error');

              $('#alert').html('Something went wrong.  Check the group to see if analysis worked, otherwise contact the site administrator.');

              $('#spinner').css('display', 'none');
              $(this).removeClass('disabled');
            }
          })
          .fail(function() {
            $('#alert').removeClass().addClass('error');
            $('#alert').html('Something went wrong.  Contact the site administrator.');

            $('#spinner').css('display', 'none');
            $(this).removeClass('disabled');
          });
        }
      })
      .fail(function() {
        $('#alert').removeClass().addClass('error');
        $('#alert').html('Something went wrong.  Contact the site administrator.');

        $('#spinner').css('display', 'none');
        $(this).removeClass('disabled');
      });
    }
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