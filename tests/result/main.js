$( document ).ready(function() 
{    
    var currect  = 1, length = 0;

    $.ajax({
      type: "POST",
      url: "actions.php",
      data: { type: 0 },
      cache: false,
      async: false,
      dataType: 'json',
      success: function (data) 
      {
        // init quest
        length = data.length;
        addItem(data, 0);
        // init bootpag
        $('#page-selection').bootpag({
            total: data.length
        }).on("page", function(event, num)
        {
            currect = num;
            addItem(data, num-1);

            if (currect == length)
            {  
              $('.btn').html('Завершить тест');
              $('.btn').addClass('btn-danger');
            }
        });
      }
    });  

    function sendAnswer(Answer, Value)
    {
       $.ajax({
        type: "POST",
        url: "actions.php",
        data: { type: 1, IdAnswer: Answer, isSet: Value },
        cache: false,
        async: false,
        success: function (data) 
        {
          console.log(data);
        }
      });      
    }

    function addItem(data, id) 
    {
        var item = '<h4>' + data[id].TestCaption + '. ' + data[id].TestText + '</h4>';
        
        if (data[id].ImagePath != null)
          item = item + '<img src="/images/questions/' + data[id].ImagePath + '" style="height: 340px">'

        if (data[id].answers != null)
        {
            item = item + '<div class="col-md-offset-3" style="text-align: left;">';
            item = item + '</br><b>Выберите правильные варианты ответа:</b></br>';
            $.each(data[id].answers, function(index, object) 
            {
                if (data[id].AnswersCount > 1)
                {  
                    item = item + '<div class="checkbox" data-id="' + object.IdAnswer + '"><label><input type="checkbox" value="">'
                    + object.AnswerText + '</label></div>';
                } else 
                {
                    item = item + '<div class="radio" data-id="' + object.IdAnswer + '"><label><input type="radio" name="optradio">'
                    + object.AnswerText + '</label></div>';
                }
            });
            item = item + '</div>';
        }

        $("#content").html(item);

        $('.checkbox').on('change', ':checkbox', function () 
        {     
           sendAnswer($(this).parent().parent().data('id'), $(this).is(':checked'));
        });

        $('.radio').on('change', ':radio', function () 
        {
            sendAnswer($(this).parent().parent().data('id'), $(this).is(':checked'));              
        });
    }

    $('.btn').on('click', function() 
    {
        if ($(this).hasClass('btn-danger'))
        { 
          $(location).attr('href', "/tests/result");
        } else {
          $('#page-selection').bootpag({page: currect + 1});
          $('#page-selection').trigger('page', currect + 1);
        }
    });

});