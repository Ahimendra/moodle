$( document ).ready(function() {
	$("#mycourse").change(function() {
    var courseid = $(this).val(); 
    $(".groupset").remove();
    $(".userset").remove();
    $(".reportholder").html("");
    $("#getreport").prop('disabled', true);
    $("#notifyme").prop('disabled', true);
    $(this).find('option[value="' + courseid + '"]').attr("selected", true);
    if(courseid > 0) {
      $.ajax({
        url: $('#url').val(),
        data:{cid:courseid},
        type:'POST',
        success:function(response) {
          var resp = $.trim(response);
          console.log(resp.length);
          if(resp.length){
            //$('.groupset').css('display', 'inline-block');

            $(resp).insertAfter("#mycourse");

          }
          
          
        }
      });
    }
  });
  $("body").on("change", "#group", function(){
    $(".userset").remove();
    var groupid = $(this).val();
    
    $(this).find('option[value="' + groupid + '"]').attr("selected", true);
    console.log("groupid"+groupid);
    if(groupid != "") {
      $.ajax({
        url: $('#url').val(),
        data:{gid:groupid},
        type:'POST',
        success:function(response) {
          var resp = $.trim(response);
          /*$('.userset').css('display', 'inline-block');
          $("#users").html(resp);*/
          $(resp).insertBefore(".actionholder");
        }
      });
    }
  });
  var userids = [];
  $("body").on("change", "#users", function() {
      if($(this).val() != 0) {
        console.log('here1');
        userids = $(this).val();
      } else {
        console.log('here2');
        userids = 0;
      }
      
      console.log("User arr:-"+userids);
      $("#getreport").prop('disabled', false);
      $("#notifyme").prop('disabled', false);
    });
  $("#getreport").click(function() {
    console.log(userids);
    console.log($('#group').length);
    if($('#group').length > 0){
      if (userids.length) {
        $.ajax({
          url: $('#url').val(),
          data:{uid:userids, selectgid:$('#group').find(":selected").val(), 
          courseid:$('#mycourse').find(":selected").val()},
          type:'POST',
          success:function(response) {
            var resp = $.trim(response);
            console.log(resp);
            $(".reportholder").html(resp);
            
          }
        });
      } else {
        $.ajax({
          url: $('#url').val(),
          data:{uid:userids, selectgid:$('#group').find(":selected").val(), 
          courseid:$('#mycourse').find(":selected").val()},
          type:'POST',
          success:function(response) {
            var resp = $.trim(response);
            
            $(".reportholder").html(resp);
            
          }
        });
      }
    } else {
      $.ajax({
        url: $('#url').val(),
        data:{uid:userids, courseid:$('#mycourse').find(":selected").val()},
        type:'POST',
        success:function(response) {
          var resp = $.trim(response);
          
          $(".reportholder").html(resp);
          
        }
      });
    }
  });
  $("body").on("click", "#notifyme", function() {
    $.ajax({
        url: $(this).attr("attr-url"),
        data:{uid:userids, courseid:$('#mycourse').find(":selected").val()},
        type:'POST',
        success:function(response) {
          var resp = $.trim(response);
          
          $(".reportholder").html(resp);
          
        }
      });
  });
});