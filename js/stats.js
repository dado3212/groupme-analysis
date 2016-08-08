$(document).ready(function() {
  // Set up Tabs
  $('#tabs').tabs();

  // Set up DataTables
  $.fn.dataTableExt.oSort['comment-asc']  = function(x,y) {
    const val = /<span( style="display: none;")?>(\d+)<\/span>/;

    const a = x.match(val)[2];
    const b = y.match(val)[2];

    return a - b;
  };
   
  $.fn.dataTableExt.oSort['comment-desc'] = function(x,y) {
    const val = /<span( style="display: none;")?>(\d+)<\/span>/;

    const a = x.match(val)[2];
    const b = y.match(val)[2];

    return b - a;
  };

  $('#members').DataTable({
    'columns': [
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      null,
      { 'type': 'comment' },
      null,
    ]
  });

  // Update individual details
  changePerson = function (person, me) {
    // Change highlighted
    if (me) {
      $(".people > div").removeClass("active");
      $(me).addClass("active");
    }

    // Update name
    $(".detail h1").html(person.name);

    var commaRegex = /(\d)(?=(\d\d\d)+(?!\d))/g;
    // Update additional details
    $(".detail .comments span").html(person.total_number.toString().replace(commaRegex, "$1,"));
    $(".detail .words span").html(person.total_words.toString().replace(commaRegex, "$1,"));
    $(".detail .likes-received span").html(person.total_likes_received.toString().replace(commaRegex, "$1,"));
    $(".detail .likes-given span").html(person.total_likes_given.toString().replace(commaRegex, "$1,"));
    $(".detail .self-likes span").html(person.self_likes.toString().replace(commaRegex, "$1,"));
    $(".detail .image").css("background-image", "url(" + person.image + ")");

    // People with common likes
    $(".shared").html("<h4># of Commonly Liked Posts</h4>");
    var shared_sorted = Object.keys(person.shared).sort(function(a,b) {return person.shared[b]-person.shared[a]});
    for (var key of shared_sorted) {
      var sharer = people.filter(function (p) {
        return (p.id === key);
      });
      if (sharer.length > 0) {
        $(".shared").append("<div class='sharer'><div class='profile' style='background-image: url(" + sharer[0].image + ")'></div><span class='name'>" + sharer[0].name + "</span><span class='number'>" + person.shared[key] + "</span>");
      }
    }

    // People who like you
    $(".loved").html("<h4>% of Received Likes</h4>");
    var love_sorted = Object.keys(person.loved).sort(function(a,b) {return person.loved[b]-person.loved[a]});
    for (var key of love_sorted) {
      var lover = people.filter(function (p) {
        return (p.id === key);
      });
      if (lover.length > 0) {
        $(".loved").append("<div class='liker'><div class='profile' style='background-image: url(" + lover[0].image + ")'></div><span class='name'>" + lover[0].name + "</span><span class='number'>" + Math.round(person.loved[key]/person.total_likes_received*100) + "%</span>");
      }
    }

    // Update histogram
    var data = person.times.map(function (num, time) {
      return [[time, 0, 0], num];
    });
    drawChart(data, person.name);
  }
});
