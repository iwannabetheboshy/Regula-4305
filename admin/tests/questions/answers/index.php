<?php
if (!isset($_SESSION))
	session_start();
	//$_SESSION['SessionId']= session_id();
/*Проверка авторизации*/
if (!( (isset( $_SESSION['isAdmin'])) && ($_SESSION['isAdmin'] == 1) )) {
  header("Location: ../../../auth.php ");
  die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Панель Администратора</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../../../../js/bootstrap-3.3.5-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../style.css">
  <script src="../../../../js/jquery.min.js"></script>
  <script src="../../../../js/jquery-ui/external/jquery/jquery.js"></script>
  <script src="../../../../js/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
  
  <script>
  $(document).ready(function() {

    function update()
    {
      $.ajax({
          type: "POST",
          url: "actions.php?type=0",
          data: { IdQuestion: <?php echo ($_GET['question']); ?> },
          success: function(json) 
          {    
              var data = $.parseJSON(json);
              var list = $(".table");
              list.find('tbody tr').remove();

              $.each(data, function(index, object) 
              {
                  $('.question').html('<h4 style="float: right;">'+ object.TestText + '</h4>');

                  var tr = '<tr class="admItem"><td>' + object.AnswerText + '</td>';

                  var check = "";
                  if (object.IsCorrect) check = "checked=''";

                  tr = tr + '<td><div class="checkbox"><label><input type="checkbox" disabled ' + check + '"></label></div></td>';
                  tr = tr + '<td>' + '<button type="button" data-toggle="modal" data-target="#edit" class="glyphicon glyphicon-pencil btn-link"></button>' + '</td>'; 
                  tr = tr + '<td>' + '<button type="button" data-toggle="modal" data-target="#delete" class="glyphicon glyphicon-remove btn-link"></button>' + '</td>' + '</tr>';

                  tr = $(tr);
                  list.append(tr);

                  tr.find(".glyphicon-pencil").first().on('click', function() 
                  { 
                    $("input[name='IdQuestion']").val(object.IdQuestion);
                    $("input[name='IdAnswer']").val(object.IdAnswer);
                    $("input[name='AnswerText']").val(object.AnswerText);
                    $("input[name='IsCorrect']").prop('checked', object.IsCorrect);
                    $('#editQuest').modal('show');
                  }); 

                  tr.find(".glyphicon-remove").first().on('click', function() 
                  {  
                    //Удаление типа объекта сканирования
                    $('#delete').modal('show');
                    $('#delete').find('.btn-primary').one('click', 
                      function() {
                        $.ajax({
                            type: "POST",
                            data: { IdAnswer: object.IdAnswer },
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
        form_data.append("addIdQuestion", <?php echo ($_GET['question']); ?>);

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
    $("#HeaderName").text('Ответы');
  });

  </script>

</head>
<body>

<?php require_once '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'site_top.php' ?>

<div class="container">
  <ul class="breadcrumb">
    <li><a href="../../../">Домой</a></li>
    <li><a href="../../">Тесты</a></li>
    <li><a href="../?test=<?php echo ($_GET['test']); ?>">Вопросы</a></li>
    <li class="active">Ответы</li>
  </ul>

  <div class="row">
    <div class="col-md-1">
      <button type="button" data-toggle="modal" data-target="#add" class="btn btn-success">Добавить</button>
    </div>

    <div class="question col-md-offset-1 col-md-10">
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
          <h4 class="modal-title">Добавить новый ответ</h4>
        </div>
        <div class="modal-body">
          <form id="formAdd">
            <div class="form-group">
              <label for="editpwd">Текст ответа</label>
              <input type="text" class="form-control" name="addAnswerText">
            </div>
            <!-- <div class="form-group">
              <label>Изображение</label>
              <input type="file" class="form-control" name="addImagefile"></input>
            </div> -->
            <div class="form-group"> 
              <div class="col-sm-4">
                <div class="checkbox">
                  <label><input type="checkbox" name="addIsCorrect">Правильный ответ</label>
                </div>
              </div>
            </div>
            </br>
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
          <h4 class="modal-title">Удаление ответа</h4>
        </div>
        <div class="modal-body">
          <p>Вы действительно хотите удалить ответ?</p> 
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
          <h4 class="modal-title">Изменить ответ</h4>
        </div>
        <form id="editForm">
        <div class="modal-body">
            <input type="hidden" class="form-control" name="IdQuestion">
            <input type="hidden" class="form-control" name="IdAnswer">
            <div class="form-group">
              <label for="editpwd">Текст ответа</label>
              <input type="text" class="form-control" name="AnswerText">
            </div>
           <!--   <div class="form-group">
              <label>Изображение</label>
              <input type="file" class="form-control" name="imagefile"></input>
            </div>  -->
            <div class="form-group"> 
              <div class="col-sm-4">
                <div class="checkbox">
                  <label><input type="checkbox" name="IsCorrect">Правильный ответ</label>
                </div>
              </div>
            </div>
            </br>
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
        <th>Текст ответа</th>
       <!--  <th>Изображение</th>  -->
        <th>Правильный ответ</th>
        <th></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>

</body>
</html>