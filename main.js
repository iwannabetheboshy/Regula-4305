"use strict";
$( document ).ready(function() 
{  
  $.ajax({
        type: "POST",
        url: "auth.php?type=1",
        success: function(json) 
        {    
            var data = $.parseJSON(json);
            var list = $(".trainers");

            $.each(data, function(index, object) 
            {
                //console.log(data);
				for (var i = 0; i < 6; i++) {
				   console.log(i);
				   var tr = $('<a href="#"><div class="col-sm-3"><h3>' + object.Name + '</h3><p></p>' +
                           '<img src="/' + object.MachineImage + '" class="thumbnail" style="height:175px">' + 
                           '</div><a/>');
					if (object.Name == '«Регула» 4305М') {
						tr = $('<a href="#"><div class="col-sm-3"></div><div class="col-sm-3"><h3>' + object.Name + '</h3><p></p>' +
                           '<img src="/' + object.MachineImage + '" class="thumbnail" style="height:175px">' + 
                           '</div><a/>');
					}
					
					else if (object.Name == '«Регула» 7505M') {
						tr = $('<a href="http://localhost:3000"><div class="col-sm-3"><h3>' + object.Name + '</h3><p></p>' +
                           '<img src="/' + object.MachineImage + '" class="thumbnail" style="height:175px">' + 
                           '</div><a/>');
					}
				}
				
                list.append(tr);
                

                tr.on('click', function(e)
                {
                    //console.log(e);
                    $("input[name='id']").val(object.IdTrainer);
                    $('#sendStud').submit();
                });
            });
        },
        cache: false,
        contentType: false,
        processData: false
    });

   $("input[name='fio']").val(localStorage.getItem('fio') ?? '')

   $('#nextStep').on('click', function(e) {
      //Тут какие то правила заполнения мб
      if ($("input[name='fio']").val().length === 0)
        $('#message').html('Пожалуйста, укажите необходимые данные').fadeIn(300).delay(2500).fadeOut(300);
      else
      {
          localStorage.setItem("fio", $("input[name='fio']").val());

          $(".step1").fadeOut(500);
          $(".step2").fadeIn(500);
          $(".trainers").fadeIn(300);
      }

      e.preventDefault();
   });

   $("input[name='fio']").keypress(function (e) 
   {
     var key = e.which;
     if(key == 13)  // the enter key code
      {
        $('#nextStep').click();
        return false;  
      }
   }); 

  $('.testbtn').on('click', function(e) 
  {
      $(location).attr('href', "tests?name=" + $("input[name='fio']").val());   
      e.preventDefault();
   });   

});