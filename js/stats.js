$(document).ready(function() {
  // Set up Tabs
  $('#tabs').tabs();

  // Sorting handling
  var sort = "name";
  var ascending = true;
  var sort_function;

  $("#sort .type").on('click', function() {
    // Stores current sort order
    if (sort == $(this).data('sort')) {
      ascending = !ascending;
    } else {
      ascending = true;
    }
    sort = $(this).data('sort');

    // Change sort indicator
    $("#sort span").html('');
    if (ascending) {
      $(this).find("span").html('↑');
    } else {
      $(this).find("span").html('↓');
    }

    // Handle custom sort functions
    if (sort == "name") {
      sort_function = function(a,b) {
        return (ascending ? -1 : 1) * (people[b]["name"].localeCompare(people[a]["name"]))
      };
    } else if (sort == "comments") {
      sort_function = function(a,b) {
        return (ascending ? -1 : 1) * (people[a]["total_number"] - people[b]["total_number"])
      };
    } else if (sort == "words") {
      sort_function = function(a,b) {
        return (ascending ? -1 : 1) * (people[a]["total_words"] - people[b]["total_words"])
      };
    } else if (sort == "likes_received") {
      sort_function = function(a,b) {
        return (ascending ? -1 : 1) * (people[a]["total_likes_received"] - people[b]["total_likes_received"])
      };
    } else if (sort == "likes_given") {
      sort_function = function(a,b) {
        return (ascending ? -1 : 1) * (people[a]["total_likes_given"] - people[b]["total_likes_given"])
      };
    } else if (sort == "self_likes") {
      sort_function = function(a,b) {
        return (ascending ? -1 : 1) * (people[a]["self_likes"] - people[b]["self_likes"])
      };
    }

    // Gets current active person
    var active = $(".people .active").data('id');

    // Sorts accordingly
    var people_sorted = Object.keys(people).sort(sort_function);

    // Repopulates with sorted
    $(".people").html('');
    for (var key of people_sorted) {
      $(".people").append("<div data-id='" + key + "' onclick='changePerson(people[" + key + "], this)'><div class='profile' style=\"background-image: url(" + (people[key].image ? people[key].image : "''") + ")\"></div><span>" + people[key].name + "</span></div>");
    }

    // Reactivates in sidebar
    $(".people div[data-id=" + active + "]").addClass('active');
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
    $(".detail .image").attr('style', "background-image: url(" + (person.image ? person.image : "''") + ");");

    // Best comment
    if (person.best_comment) {
      $(".detail .best-comment .top .likes").html("<img src='../assets/images/heart.png' />" + person.max_likes);
      if (person.best_comment.attachments.length == 0 || (person.best_comment.attachments.length > 0 && person.best_comment.attachments[0].type !== "image")) {
        $(".detail .best-comment .content").html("<div><q>" + person.best_comment.text + "</q></div>");
      } else {
        var comment = "<img src='" + person.best_comment.attachments[0].url + "' /><div>";
        if (person.best_comment.text)
          comment += "<q>" + person.best_comment.text + "</q>";
        comment += "</div>";
        $(".detail .best-comment .content").html(comment);
      }
    } else {
      $(".detail .best-comment .top .likes").html("");
      $(".detail .best-comment .content").html("<h4>No liked comments.</h4>");
    }

    // People with common likes
    $(".shared").html("<h4># of Commonly Liked Posts</h4>");
    var shared_sorted = Object.keys(person.shared).sort(function(a,b) {return person.shared[b]-person.shared[a]});
    for (var key of shared_sorted) {
      var sharer = people.filter(function (p) {
        return (p.id === key);
      });
      if (sharer.length > 0) {
        $(".shared").append("<div class='sharer'><div class='profile' style=\"background-image: url(" + (sharer[0].image ? sharer[0].image : "''") + ");\"></div><span class='name'>" + sharer[0].name + "</span><span class='number'>" + person.shared[key] + "</span>");
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
        $(".loved").append("<div class='liker'><div class='profile' style=\"background-image: url(" + (lover[0].image ? lover[0].image : "''") + ")\"></div><span class='name'>" + lover[0].name + "</span><span class='number'>" + Math.round(person.loved[key]/person.total_likes_received*100) + "%</span>");
      }
    }

    // Update histogram
    chartOptions.title = person.name + '\'s Activity by Time';
    $(window).off('resize');
    var data = person.times.map(function (num, time) {
      return [[time, 0, 0], num];
    });
    drawChart(data);
  }

  // Set up fix
  $("a[href='#individuals']").one('click', function() {
    if (people[0])
      changePerson(people[0], null);
  });
});
