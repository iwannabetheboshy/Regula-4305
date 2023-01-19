$( document ).ready(function() 
{  
   $(".list-group a").on("click", function(e) 
   {
      //console.log("click");
      $.ajax({
        type: "POST",
        url: "actions.php",
        data: { type: 0, IdTest: $(this).attr('data-id') },
        cache: false,
        async: false,
        success: function (data) 
        {
          if (data == 'ok')
            $(location).attr('href', "questions/");
          console.log("click1");
        }
      });        
    });
});