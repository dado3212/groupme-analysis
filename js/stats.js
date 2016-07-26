$(document).ready(function() {
  $.fn.dataTableExt.oSort['comment-asc']  = function(x,y) {
    const val = /<span( style="display: none;")?>(\d)<\/span>/;

    const a = x.match(val)[2];
    const b = y.match(val)[2];

    return a - b;
  };
   
  $.fn.dataTableExt.oSort['comment-desc'] = function(x,y) {
    const val = /<span( style="display: none;")?>(\d)<\/span>/;

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
    ]
  });
});