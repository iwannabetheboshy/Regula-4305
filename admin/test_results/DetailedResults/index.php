<?php
if (!isset($_SESSION))
	session_start();
	//$_SESSION['SessionId']= session_id();
/*Проверка авторизации*/
if (!( (isset( $_SESSION['isAdmin'])) && ($_SESSION['isAdmin'] == 1) )) {
  header("Location: ../../auth.php ");
  die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Панель Администратора</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../../../js/bootstrap-3.3.5-dist/css/bootstrap.min.css">  
  <script src="../../../js/jquery.min.js"></script>
  <script src="../../../js/jquery-ui/external/jquery/jquery.js"></script>
  <script src="../../../js/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
  <script src="../../../js/datepicker/moment-with-locales.js"></script>
  
  <script>
  $(document).ready(function() 
  {
    $("#HeaderName").text('Результаты тестирования');
  });
  function ShowAnswers() {
    $("#SessionAnswers").empty();
    var tbl =  $("<table class='table table-hover'><thead><tr><th>Код варианта ответа</th>"+
                            "<th>Текст варианта ответа</th><th>Является верным ответом</th>"+
                            "<th>Выбран студентом в тесте</th><th>Попал в зачет по ограничению времени</th><th>Дата выбора варианта ответа</th></tr></thead><tbody></tbody></table>");    
     $.ajax({
            type: "POST",
            url: "actions.php?type=2",
            data: { IdSession: $(this).data().IdSession, IdQuestion: $(this).data().IdQuestion},
            success: function(json) 
            { 
              data = $.parseJSON(json);
              $(data).each(function(index,object) {

                var IsCorrect = '', IsSelected = '', AnswerTime ='', PassedByTime = '';
                if (object.IsCorrect == 1)
                  IsCorrect = '<span style="color:green;font-size:20px;" class="glyphicon glyphicon-ok" aria-hidden="true"></span>';

                if (object.IsCorrect == 1 && object.IsSelected == 1)
                  IsSelected = '<span style="color:green;font-size:20px;" class="glyphicon glyphicon-ok" aria-hidden="true"></span>' 
                else if (object.IsSelected == 1) 
                  IsSelected = '<span style="color:red;font-size:20px;" class="glyphicon glyphicon-ok" aria-hidden="true"></span>';

                if (object.AnswerTime != null)
                  AnswerTime = moment(object.AnswerTime).format("HH:MM:SS")

                if (object.PassedByTime == 1)  
                  PassedByTime = '<span style="color:gray;font-size:20px;" class="glyphicon glyphicon-ok" aria-hidden="true"></span>';

                var tr = $('<tr><td>'+object.IdAnswer+'</td><td>'+object.AnswerText+'</td><td>'+IsCorrect+'</td><td>'+IsSelected+'</td><td>'+PassedByTime+'</td><td>'+AnswerTime+'</td></tr>');
                tbl.append(tr);
              })
            }
          });
    $("#SessionAnswers").append(tbl);
    $("#SessionAnswerList").modal('show');
  }

  $.ajax({
            type: "POST",
            url: "actions.php?type=0",
            data: { IdSession: <?php echo ($_GET['IdSession']); ?> },
            success: function(json) 
            {
              var data = $.parseJSON(json);
              var list = $("#SessionInfo");
              list.empty();

              var BallsClass;
              if (data[0].Balls>51) 
                BallsClass = 'list-group-item-success';
              else 
                BallsClass = 'list-group-item-danger';
              var li = $( '<li class="list-group-item list-group-item-info">ФИО студента: '+data[0].Name+'</li>'+
                          '<li class="list-group-item list-group-item-info">Название теста: '+data[0].TestName+'</li>'+
                          '<li class="list-group-item list-group-item-info">Дата тестирования: '+moment(data[0].StartDate).format("DD-MM-YYYY")+'</li>'+
                          '<li class="list-group-item list-group-item-info">Получено баллов: '+data[0].BallsForTest+' из '+data[0].MaxBalls+' возможных</li>'+
                          '<li class="list-group-item '+BallsClass+'">Оценка за тест: '+data[0].Balls+'</li>'
                        );
              list.append(li);
            }
  });
  $.ajax({
            type: "POST",
            url: "actions.php?type=1",
            data: { IdSession: <?php echo ($_GET['IdSession']); ?> },
            success: function(json) 
            {
              var data = $.parseJSON(json);
              var list = $("#mainlist tbody");
              list.empty();

               $.each(data, function(index, object) 
              {
                var thumbs = ''
                if (object.QuestionMaxBalls == object.AnswersBall)
                  thumbs = '<span style="color:green;font-size:20px;" class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>'
                else if (object.AnswersBall <= 0) 
                  thumbs = '<span style="color:red;font-size:20px;" class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span>';
                var img = '';
                if (object.ImagePath != null)
                  img = '<img src="/images/questions/'+object.ImagePath+'"  style="height:75px;">';
                
                tr = $('<tr style="cursor:pointer" data-toggle="tooltip" title="Нажмите для просмотра выбранных вариантов ответов"><td>'+
                        object.TestCaption+'</td><td>'+object.TestText+'</td><td>'+img+'</td><td>'+object.QuestionMaxBalls+'</td><td>'+object.AnswersBall+'</td><td>'+thumbs+'</td></tr>');
                tr.data({"IdSession" : <?php echo ($_GET['IdSession']); ?> , "IdQuestion" : object.IdQuestion, "QuestionText" : object.TestText});
                tr.click(ShowAnswers);
                list.append(tr);
              })               
            }
  });
  </script>

</head>
<body>

<?php require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'site_top.php' ?>

<div class="container">
  <ul class="breadcrumb">
    <li><a href="../../">Домой</a></li>
    <li><a href="../">Результаты тестирования</a></li>
    <li class="active">Подробные сведения о процессе тестирования</li>
  </ul>


  <div>
    <ul class="list-group" id="SessionInfo">
    </ul>
  </div>

  <table class="table table-hover" id="mainlist">
    <thead>
      <tr>
        <th>Название вопроса</th>
        <th>Текст вопроса</th>
        <th>Изображение</th>
        <th>Максимум баллов за вопрос</th>
        <th>Получено баллов за вопрос</th>  
        <th> </th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>

<!-- Modal Session action list -->
  <div class="modal modal-wide fade" id="SessionAnswerList" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Выбранные варианты ответов</h4>
        </div>
        <div class="modal-body" id="SessionAnswers">
        </div>
      </div>      
    </div>
  </div>

</body>
</html>