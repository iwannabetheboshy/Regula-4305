<?php
if (!isset($_SESSION))
	session_start();
	//$_SESSION['SessionId']= session_id();
/*Проверка авторизации*/
if (!( (isset( $_SESSION['isAdmin'])) && ($_SESSION['isAdmin'] == 1) )) {
  header("Location: ../auth.php ");
  die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Панель Администратора</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../../js/bootstrap-3.3.5-dist/css/bootstrap.min.css">
  <script src="../../js/jquery.min.js"></script>
  <script src="../../js/jquery-ui/external/jquery/jquery.js"></script>
  <script src="../../js/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
  
  <script>
  $(document).ready(function() {

    function updateAdm()
    {
      $.ajax({
          type: "POST",
          url: "actions.php?type=0",
          success: function(json) 
          {    
              var data = $.parseJSON(json);
              var list = $(".table");
              list.find('tbody tr').remove();

              $.each(data, function(index, object) 
              {
                  var tr = $('<tr class="admItem"><td>' + object.IdAdmin + '</td><td>' + object.UserName + '</td><td>********</td><td>' + 
                    '<button type="button" data-toggle="modal" data-target="#deleteAdm" class="glyphicon glyphicon-remove btn-link"></button>' + '</td></tr>');
                  list.append(tr);

                  tr.on('click', function(e) 
                  {
                      $("input[name='IdAdmin']").val(object.IdAdmin);
                      $('#editPass').modal('show');                
                  });

                  tr.find(".glyphicon-remove").first().on('click', function() 
                  {  
                    //Удаление пароля
                    $('#deleteAdm').modal('show');
                    $('#deleteAdm').find('.btn-primary').one('click', 
                      function() {
                        $.ajax({
                            type: "POST",
                            data: { IdAdmin: object.IdAdmin },
                            url: "actions.php?type=3",
                            success: function(json) 
                            {    
                                tr.remove();
                                $('#deleteAdm').modal('hide');
                                return false;
                            },
                            cache: false
                          });
                          return false;
                      });
                    return false;
                 });

              });
          },
          cache: false,
          contentType: false,
          processData: false
      });
    }

    $('#editPassForm').on('submit', function(event) 
    {
        var pwd = $("input[name='editpwd']").val();
        var pwd2 = $("input[name='editpwd2']").val();
        var IdAdmin = $("input[name='IdAdmin']");

        if (pwd.length < 6 || pwd2.length < 6)
            $('#editmessage').html('Минимальная длина пароля 6 символов.').fadeIn(300).delay(2000).fadeOut(300);
        else
        if (pwd != pwd2)
            $('#editmessage').html('Пароли должны совпадать!').fadeIn(300).delay(2000).fadeOut(300);
        else
        {
            var form_data = new FormData($(this)[0]);
            $.ajax({
              type: "POST",
              data: form_data, //{ IdAdmin: IdAdmin, pwd: pwd },
              url: "actions.php?type=2",
              success: function(json) 
              {    
                  $('#editPass').modal('hide');
                  $('#editPassForm')[0].reset();
                  return false;
              },
              cache: false,
              contentType: false,
              processData: false
            });
         }
         event.preventDefault(); 
    });
    
    $('#formAddAdmin').on('submit', function(e) 
    {
        if ($("input[name='login']").val().length == 0)
          $('#message').html('Пожалуйста, укажите логин администратора!').fadeIn(300).delay(2000).fadeOut(300);
        else
        if ($("input[name='pwd']").val().length == 0 || $("input[name='pwd2']").val().length == 0)
          $('#message').html('Пожалуйста, укажите пароль администратора!').fadeIn(300).delay(2000).fadeOut(300);
        else
          if ($("input[name='pwd']").val() != $("input[name='pwd2']").val())
            $('#message').html('Пароли должны совпадать!').fadeIn(300).delay(2000).fadeOut(300);
        else 
        {                         
            var form_data = new FormData($(this)[0]);
            $.ajax({
                  type: "POST",
                  url: "actions.php?type=1",
                  data: form_data,
                  success: function(data) 
                  {
                        $('#addAdmin').modal('hide');
                        updateAdm();
                        return false;
                  },
                  cache: false,
                contentType: false,
                processData: false
            });
            
        }
        e.preventDefault();
    });

    updateAdm();
    $("#HeaderName").text('Администраторы');
  });

  </script>

</head>
<body>

<?php require_once '..' . DIRECTORY_SEPARATOR . 'site_top.php' ?>

<div class="container">
  <ul class="breadcrumb">
    <li><a href="../">Домой</a></li>
    <li class="active">Администраторы</li>
  </ul>

  <div class="row">
    <div class="col-md-1">
      <button type="button" data-toggle="modal" data-target="#addAdmin" class="btn btn-success">Добавить</button>
    </div>
  </div>
  <br/>

  <!-- Modal Add -->
  <div class="modal fade" id="addAdmin" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Добавить нового администратора</h4>
        </div>
        <div class="modal-body">
          <form id="formAddAdmin">
          <div class="form-group">
            <label for="login">Логин:</label>
            <input type="login" class="form-control" name="login">
          </div>
          <div class="form-group">
            <label for="pwd">Пароль:</label>
            <input type="password" class="form-control" name="pwd">
          </div>
           <div class="form-group">
            <label for="pwd">Подтверждение пароля:</label>
            <input type="password" class="form-control" name="pwd2">
          </div>
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
  <div class="modal fade" id="deleteAdm" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Удаление администратора</h4>
        </div>
        <div class="modal-body">
          <p>Вы действительно хотите удалить администратора?</p> 
          <p>Данное действие необратимо!</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Удалить</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Edit -->
  <div class="modal fade" id="editPass" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Изменить пароль</h4>
        </div>
        <div class="modal-body">
          <form id="editPassForm">
            <div class="form-group">
              <input type="hidden" class="form-control" name="IdAdmin">
            </div>
            <div class="form-group">
              <label for="editpwd">Пароль:</label>
              <input type="password" class="form-control" name="editpwd">
            </div>
             <div class="form-group">
              <label for="editpwd2">Подтверждение пароля:</label>
              <input type="password" class="form-control" name="editpwd2">
            </div>    
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
        <th>ID</th>
        <th>Логин</th>
        <th>Пароль</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>
</div>

</body>
</html>