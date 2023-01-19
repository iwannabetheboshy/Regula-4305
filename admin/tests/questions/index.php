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
  <link rel="stylesheet" href="../style.css">
  <script src="../../../js/jquery.min.js"></script>
  <script src="../../../js/jquery-ui/external/jquery/jquery.js"></script>
  <script src="../../../js/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
  
  <script>
  $(document).ready(function() {

    function update()
    {
      $.ajax({
          type: "POST",
          url: "actions.php?type=0",
          data: { IdTest: <?php echo ($_GET['test']); ?> },
          success: function(json) 
          {    
              var data = $.parseJSON(json);
              var list = $(".table");
              list.find('tbody tr').remove();

              $.each(data, function(index, object) 
              {
                  var tr = '<tr class="admItem" style="cursor:pointer"><td>' + object.TestCaption + '</td><td>';

                  if (object.ImagePath != null)
                    tr = tr + '<img src="/images/questions/' + object.ImagePath + '" style="height:75px">';

                  tr = tr + '</td><td>' + object.TestText + '</td><td>' + 
                      object.AnswerCount + '</td><td>' + object.CorrectAnswerCount + '</td><td>' + object.QuestionBalls + '</td><td>';

                  var check = "";
                  if (object.MultipleAnswersAllow) check = "checked=''";

                  tr = tr + '<div class="checkbox"><label><input type="checkbox" disabled ' + check + '"></label></div></td><td>';

                  if (object.SkipQuestion) check = "checked=''";
                    else check = "";
                  tr = tr + '<div class="checkbox"><label><input type="checkbox" disabled ' + check + '"></label></div></td><td>' 
                  + '<button type="button" data-toggle="modal" data-target="#edit" class="glyphicon glyphicon-pencil btn-link"></button>' + '</td>'; 

                  tr = tr + '<td>' + '<button type="button" data-toggle="modal" data-target="#delete" class="glyphicon glyphicon-remove btn-link"></button>' + '</td>' + '</tr>';

                  tr = $(tr);
                  list.append(tr);

                  tr.on('click', function(e) 
                  {
                      $(location).attr('href', "answers?question=" + object.IdQuestion + '&test=<?php echo ($_GET['test']); ?>');                  
                  });

                  tr.find(".glyphicon-pencil").first().on('click', function() 
                  { 
                    $("input[name='IdQuestion']").val(object.IdQuestion);
                    $("input[name='TestCaption']").val(object.TestCaption);
                    $("textarea[name='TestText']").val(object.TestText);
                    $("input[name='QuestionBalls']").val(object.QuestionBalls);

                    $("input[name='MultipleAnswersAllow']").prop('checked', object.MultipleAnswersAllow);
                    $("input[name='SkipQuestion']").prop('checked', object.SkipQuestion);

                    $('#editQuest').modal('show');

                    return false;
                  });

                  tr.find(".glyphicon-remove").first().on('click', function() 
                  {  
                    //Удаление типа объекта сканирования
                    $('#delete').modal('show');
                    $('#delete').find('.btn-primary').one('click', 
                      function() {
                        $.ajax({
                            type: "POST",
                            data: { IdQuestion: object.IdQuestion, ImagePath: object.ImagePath },
                            url: "actions.php?type=3",
                            success: function(json) 
                            {    
                                tr.remove();
                                $('#delete').modal('hide');
                                return false;
                            },
                            cache: false
                          });
                      });
                    return false;
                  }); 

              });
          },
          cache: false
      });
    }

    $('#editForm').on('submit', function(event) 
    {
        var form_data = new FormData($(this)[0]);
        $.ajax({
          type: "POST",
          data: form_data,
          url: "actions.php?type=2",
          success: function(json) 
          {    
              $('#editQuest').modal('hide');
              $('#editForm')[0].reset();
              update();
              return false;
          },
          cache: false,
          contentType: false,
          processData: false
        });

        event.preventDefault(); 
    });
    
    $('#formAdd').on('submit', function(e) 
    {                     
        var form_data = new FormData($(this)[0]);
        form_data.append("IdTest", <?php echo ($_GET['test']); ?>);

        $.ajax({
              type: "POST",
              url: "actions.php?type=1",
              data: form_data,
              success: function(data) 
              {
                    $('#add').modal('hide');
                    $('#formAdd')[0].reset();
                    update();
                    return false;
              },
              cache: false,
            contentType: false,
            processData: false
        });
              
        e.preventDefault();
    });

    update();
    $("#HeaderName").text('Вопросы');
  });

  </script>

</head>
<body>

<?php require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'site_top.php' ?>

<div class="container">
  <ul class="breadcrumb">
    <li><a href="../../">Домой</a></li>
    <li><a href="../">Тесты</a></li>
    <li class="active">Вопросы</li>
  </ul>

  <div class="row">
    <div class="col-md-1">
      <button type="button" data-toggle="modal" data-target="#add" class="btn btn-success">Добавить</button>
    </div>
  </div>
  <br/>

  <!-- Modal Add -->
  <div class="modal fade" id="add" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Добавить новый вопрос</h4>
        </div>
        <div class="modal-body">
          <form id="formAdd">
            <div class="form-group">
              <label for="editpwd">Заголовок</label>
              <input type="text" class="form-control" name="TestCaptionAdd">
            </div>
             <div class="form-group">
              <label for="editpwd2">Описание</label>
              <textarea type="text" class="form-control" name="TestTextAdd"></textarea>
            </div>
            <div class="form-group">
              <input type="file" name="ImagePath" class="form-control">
            </div>
            <div class="form-group">
              <label for="editpwd">Баллов за вопрос</label>
              <input type="number" class="form-control" name="QuestionBallsAdd">
            </div>
            <div class="form-group"> 
              <div class="col-sm-4">
                <div class="checkbox">
                  <label><input type="checkbox" name="SkipQuestionAdd">Скрытый вопрос</label>
                </div>
              </div>
              <div class="checkbox col-sm-8">   
                  <label><input type="checkbox" name="MultipleAnswersAllowAdd">Разрешить несколько ответов</label>
              </div> 
            </div>
            </br>
            <div id="message" class="alert alert-danger" style="display: none;">
              <strong>Ошибка!</strong> Указаны не все данные.
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Добавить</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
        </form>
      </div>
      
    </div>
  </div>

    <!-- Modal Delete -->
  <div class="modal fade" id="delete" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Удаление вопроса</h4>
        </div>
        <div class="modal-body">
          <p>Вы действительно хотите удалить вопрос?</p> 
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Удалить</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Edit -->
  <div class="modal fade" id="editQuest" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Изменить вопрос</h4>
        </div>
        <form id="editForm">
        <div class="modal-body">
            <input type="hidden" class="form-control" name="IdQuestion">
            <div class="form-group">
              <label for="editpwd">Заголовок</label>
              <input type="text" class="form-control" name="TestCaption">
            </div>
             <div class="form-group">
              <label for="editpwd2">Описание</label>
              <textarea type="text" class="form-control" name="TestText"></textarea>
            </div>
            <div class="form-group">
              <label for="editpwd">Баллов за вопрос</label>
              <input type="number" class="form-control" name="QuestionBalls">
            </div>
            <div class="form-group">
              <input type="file" name="ImagePath" class="form-control">
            </div>
            <div class="form-group"> 
              <div class="col-sm-4">
                <div class="checkbox">
                  <label><input type="checkbox" name="SkipQuestion">Скрытый вопрос</label>
                </div>
              </div>
              <div class="checkbox col-sm-8">   
                  <label><input type="checkbox" name="MultipleAnswersAllow">Разрешить несколько ответов</label>
              </div> 
            </div>
            </br>

            <div id="editmessage" class="alert alert-danger" style="display: none;">
                <strong>Ошибка!</strong> Указаны не все данные.
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Изменить</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
        </form>
      </div>
      
    </div>
  </div>

  <table class="table table-hover">
    <thead>
      <tr>
        <th>Название</th>
        <th>Изображение</th>
        <th>Описание</th>
        <th>Вариантов ответа</th>
        <th>Правильных ответов</th>
        <th>Баллы</th>
        <th>Несколько ответов</th>
        <th>Скрыть</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>

</body>
</html>