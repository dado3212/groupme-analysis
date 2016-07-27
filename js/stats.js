$(document).ready(function() {
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

  changePerson = function (person) {
    // Update name
    $("#detail h1").html(person.name);

    var commaRegex = /(\d)(?=(\d\d\d)+(?!\d))/g;
    // Update additional details
    $("#detail .comments span").html(person.total_number.toString().replace(commaRegex, "$1,") + " comments");
    $("#detail .words span").html(person.total_words.toString().replace(commaRegex, "$1,") + " words");
    $("#detail .likes-received span").html(person.total_likes_received.toString().replace(commaRegex, "$1,") + " likes received");
    $("#detail .likes-given span").html(person.total_likes_given.toString().replace(commaRegex, "$1,") + " likes given");
    $("#detail .self-likes span").html(person.self_likes.toString().replace(commaRegex, "$1,") + " self likes");
    $("#detail > img").attr("src", person.image);

    // Update histogram
    var data = person.times.map(function (num, time) {
      return [[time, 0, 0], num];
    });
    drawChart(data, person.name);
  }
});
