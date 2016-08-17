$('#ejemplo').click(function(){

  var error_icon = '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ',
      success_icon = '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ',
      process_icon = '<span class="fa fa-spinner fa-spin" aria-hidden="true"></span> ';

  $('#ajax_ejemplo').removeClass('alert-danger');
  $('#ajax_ejemplo').removeClass('alert-warning');
  $('#ajax_ejemplo').addClass('alert-warning');
  $("#ajax_ejemplo").html(process_icon  + 'Procesando por favor espere...');
  $('#ajax_ejemplo').removeClass('hide');

  $.ajax({
    type : "POST",
    url : "api/ejemplo",
    data : $('#ejemplo_form').serialize(),
    success : function(json) {
      var obj = jQuery.parseJSON(json);
      if(obj.success == 1) {
        $('#ajax_ejemplo').html(success_icon + obj.message);
        $("#ajax_ejemplo").removeClass('alert-warning');
        $("#ajax_ejemplo").addClass('alert-success');
        setTimeout(function(){
          location.reload();
        },1000);
      } else {
        $('#ajax_ejemplo').html(error_icon  + obj.message);
        $("#ajax_ejemplo").removeClass('alert-warning');
        $("#ajax_ejemplo").addClass('alert-danger');
      }
    },
    error : function() {
      window.alert('#ejemplo ERORR');
    }
  });
});
