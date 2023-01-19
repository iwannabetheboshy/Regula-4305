<?php 
  if (!isset($_SESSION))
    session_start();
	if (!isset($_SESSION['SessionId']))
    die('Access Deny');

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
  <link rel="stylesheet" href="../../js/jquery-ui/jquery-ui.css">
  <style>
    .icon-arrow-right {
      float: right;
      margin-top: 2px;
      margin-right: 1px;
    }
    .thumbnail
    {
      border: 2px solid #ddd;
    }
    .thumbnail h4
    {
      text-align: center; 
      height: 18px; 
      white-space: nowrap;
      text-overflow: ellipsis; 
      overflow: hidden;
    }
    .thumbnail p
    {
      text-align: center;
    }
    .select_item {
        border: 2px solid #168cec;
        border-style: outset;
        margin: 5px;
    }
    .model_item
    {
      margin: 5px;
    }
  </style>
  <script src="../../js/jquery.min.js"></script>
  <script src="../../js/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
  
  <script>
  $(document).ready(function() 
  {
    $("#HeaderName").text('Изображения студентов');
      
    //обновление обьектов сканирования
    function updateObj() 
    {
        $.ajax({
          type: "POST",
          url: "actions.php?type=0",
          data: { IdTrainer: <?php echo ($_GET['id']); ?>},
          success: function(json) 
          {    
              var data = $.parseJSON(json);
              //console.log(json);
              var list = $(".imglist");
              list.empty();

              $.each(data, function(index, object) 
              {
                  var item = $('<div class="col-md-3"><div class="thumbnail">' + 
                  '<a href="#"><span class="glyphicon glyphicon-remove icon-arrow-right"></span></a>' + '<h4>' + object.SaveTime + '</h4>' +
                  '<p>' + object.StudentName + '</p></a>' +
                  '<a href="/images/StudentImages/' + object.ImagePath + '">' + 
                  '<img src="/images/StudentImages/' + object.ImagePath + '" style="height:200px">' 
                    + '</a></div></div>');

                   item.find(".glyphicon-remove").first().on('click', function() 
                   {  
                      //Удаление обьекта сканирования
                      $('#delImg').modal('show');
                      $('#delImg').find('.btn-primary').one('click', 
                        function() {
                          $.ajax({
                              type: "POST",
                              data: { IdSavedImage: object.IdSavedImage, ImagePath: object.ImagePath },
                              url: "actions.php?type=1",
                              success: function(json) 
                              {    
                                  item.remove();
                                  $('#delImg').modal('hide');
                                  return false;
                              },
                              cache: false
                            });
                        });
                      return false;
                   });
                  list.append(item);
              });

          },
          cache: false
        });
    }

    updateObj();

    $("#message").hide();
  });

  </script>

</head>
<body>
<?php require_once '..' . DIRECTORY_SEPARATOR . 'site_top.php' ?>

<div class="container">
  <ul class="breadcrumb">
    <li><a href="../">Домой</a></li>
    <li class="active">Изображения студентов</li>
  </ul>

  <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
        <h3>Изображения студентов</h3>
        <p>Просмотр и удаление сохранённых изображений студентов</p>
        </br>
        <div class="row imglist"></div>
    </div>
  </div>

  <!-- Modal Удалить объект -->
  <div class="modal fade" id="delImg" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Удалить изображение?</h4>
        </div>
        <div class="modal-body">
            <p>Вы действительно хотите удалить это изображение?</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Удалить</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
    </div>
  </div>

</br>
</br>
</br>
</div>

</body>
</html>