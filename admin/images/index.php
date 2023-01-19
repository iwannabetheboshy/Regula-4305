<?php 
  if (!isset($_SESSION))
    session_start();
	if (!isset($_SESSION['SessionId']))
    die('Access Deny');
  if (!defined("AccessToFile"))
    define("AccessToFile", 1);

  require_once '..' . DIRECTORY_SEPARATOR . 'settings.php';  

  /*Проверка авторизации*/
  if (!( (isset( $_SESSION['isAdmin'])) && ($_SESSION['isAdmin'] == 1) )) {
      header("Location: ../auth.php ");
      die();
  }  

  function DrawAdditionalFields( $config )
  {
      //Рисуем дополнительные поля, если таковые имеются
    $SQL = " SELECT * FROM TrainerImagesAdditionalFieldsDef WHERE IdTrainer = ? ORDER BY IdAdditionalField ASC";
    $result = $config->QueryWithParams($SQL, [$_GET['id']]);     
    if (isset($result) && $result != NULL)
      foreach ($result  as $key => $value) {
        if ($value['FieldType'] == 'VARCHAR(MAX)')
          $InputHtml = '<input type="text" name="ed'.$value["FieldDBName"].'" class="form-control" id="ed'.$value["FieldDBName"].'" value="'.$value["DefaultValue"].'">';
        if ($value['FieldType'] == 'INT')
          $InputHtml = '<input type="number" name="ed'.$value["FieldDBName"].'" class="form-control" id="ed'.$value["FieldDBName"].'" value="'.$value["DefaultValue"].'">';
        if ($value['FieldType'] == 'BIT')
        {
          if ($value["DefaultValue"] == "1") 
            $DefVal = 'checked'; 
          else
            $DefVal = '';
          $InputHtml = '<input type="checkbox" name="ed'.$value["FieldDBName"].'" class="form-control" id="ed'.$value["FieldDBName"].'" '.$DefVal.'>';
        }
        if ($value['FieldType'] == 'DATE')
          $InputHtml = '<input type="date" name="ed'.$value["FieldDBName"].'" class="form-control" id="ed'.$value["FieldDBName"].'" value="'.$value["DefaultValue"].'">';

        echo('<div class="form-group"> <label for="ed'.$value["FieldDBName"].'">'.$value["FieldSystemName"].':</label> '.$InputHtml.' </div>');
      }
    //print_r ($result);
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
      //обновление превьюешек
      function updatePrev() 
      {
        $.ajax({
          type: "POST",
          url: "actions.php?type=1",
          success: function(json) 
          {    
              var data = $.parseJSON(json);
              //console.log(json);
              var modal = $(".filePrev");
              var list = $(".tabPrev");

              modal.empty();
              list.empty();

              $.each(data, function(index, object) 
              {
                  var modalitem = $('<div class="col-sm-2 thumbnail model_item">' + '<a href="#' + '">' +  
                  '<img src="/images/preview/' + object.PreviewPath + '" style="height:75px"></a></div>');
                   
                   modalitem.data( "IdPreview", object.IdPreview);
                   modalitem.on('click', function(event) { 
                        //console.log(event);
                        //console.log(this);

                        $('.filePrev div').removeClass("select_item");
                        //Выделяем обьект
                        $(this).addClass("select_item");
                        //оставляем на месте скролл
                        return false;
                    });

                   modal.append(modalitem);

                   var item = $('<div class="col-sm-2 thumbnail" style="margin: 5px;">' +                    
                   '<a href="#"><span class="glyphicon glyphicon-remove icon-arrow-right" aria-hidden="true"></span></a>' +  
                  '<img src="/images/preview/' + object.PreviewPath + '" style="height:75px"></div>');

                   item.find(".glyphicon-remove").first().on('click', function() 
                   {  
                      //Удаление типа объекта сканирования
                      $('#delTypeImg').modal('show');
                      $('#delTypeImg').find('.btn-primary').one('click', 
                        function() {
                         // console.log(object);
                          $.ajax({
                              type: "POST",
                              data: { IdPreview: object.IdPreview, ImagePath: object.PreviewPath },
                              url: "actions.php?type=4",
                              success: function(json) 
                              {    
                                  item.remove();
                                  $('#delTypeImg').modal('hide');
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
                  '<a href="#" title = "Удалить"><span class="glyphicon glyphicon-remove icon-arrow-right"></span></a>' + 
                  '<a href="#" title = "Редактировать"><span class="glyphicon glyphicon-edit icon-arrow-right" ></span></a>'+
                    '<h4>' + object.Name + '</h4>' +                  
                  // '<a href="/images/preview/' + object.PreviewPath + '">' + 
                  '<img src="/images/preview/' + object.PreviewPath + '" style="height:90px"></a>' +
                  '<a href="/images/scans/' + object.ImagePath + '">' + 
                  '<img src="/images/scans/' + object.ImagePath + '" style="height:200px">' 
                    + '</a></div></div>').data(object);

                   item.find(".glyphicon-remove").first().on('click', function() 
                   {  
                      //Удаление обьекта сканирования
                      $('#delImg').modal('show');
                      $('#delImg').find('.btn-primary').one('click', 
                        function() {
                          $.ajax({
                              type: "POST",
                              data: { IdImage: object.IdImage, ImagePath: object.ImagePath },
                              url: "actions.php?type=2",
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

                   item.find(".glyphicon-edit").first().on("click", function () {
                      //Редактирование объекта
                      data_ = $(this).parent().parent().parent().data();
                      $("#formEdImg")[0].reset();
                      $("#EdImg div").removeClass("select_item")
                      $("#EdImg input").removeAttr("checked");
                      $("#EdImg").modal("show").data(data_);                      
                      
                      // Вставка значений основных параметров
                      $('#EdImg #imagename').val(data_.Name);
                      $("#EdImg").find('img[src$="/'+data_.PreviewPath+'"]').parent().parent().addClass("select_item");

                      // Вставка значения расширеных параметров (при необходимости - редактировать - сделано только для текста, цифр и checkbox)
                      $.ajax({
                        type: "POST",
                        data: { IdTrainer: <?php echo ($_GET['id']); ?> },
                        url: "actions.php?type=6",
                        success: function(json) 
                        {    
                            var data = $.parseJSON(json);
                            $(data).each(function(index, val) {
                              /*checkbox*/
                              if (val.FieldType == "BIT" ) 
                                $('#EdImg #ed'+val.FieldDBName).prop( "checked" , data_[val.FieldDBName]);
                              else
                                $('#EdImg #ed'+val.FieldDBName).val(data_[val.FieldDBName]);
                            
                            
                            });
                        },
                        cache: false
                      });
                   });
                  list.append(item);
              });

          },
          cache: false
        });
    }

    updatePrev();
    updateObj();

    $("#message").hide();

    $('#formAddimg').on('submit', function(e) 
    {
        if ($('#imagename').val().length == 0)
          $('#message').html('Ошибка, указаны не все данные!').fadeIn(300).delay(2000).fadeOut(300);
        else 
        {                         
            var form_data = new FormData($(this)[0]);
            //Добавляем ид типа обьекта сканирования
            form_data.append("IdPreview", $('.select_item').data('IdPreview'));
            form_data.append("IdTrainer", <?php echo ($_GET['id']); ?>);

            $.ajax({
                  type: "POST",
                  url: "actions.php?type=3",
                  data: form_data,
                  success: function(data) 
                  {
                        $('#addImg').modal('hide');
                        updateObj();
                        $("#formAddimg")[0].reset();
                        return false;
                  },
                  cache: false,
                contentType: false,
                processData: false
            });
            
        }
        e.preventDefault();
    });

    $('#formAddPrevImg').on('submit', function(e) 
    {                    
        var form_data = new FormData($(this)[0]);
        $.ajax({
              type: "POST",
              url: "actions.php?type=5",
              data: form_data,
              success: function(data) 
              {
                    $('#addPrevImg').modal('hide');
                    updatePrev();
                    return false;
              },
              cache: false,
            contentType: false,
            processData: false
        });
        e.preventDefault();
    });
    $("#HeaderName").text('Объекты контроля');



    $('#formEdImg').on('submit', function(e) 
    {
        if ($('#formEdImg #imagename').val().length == 0)
          $('#formEdImg #message').html('Ошибка, указаны не все данные!').fadeIn(300).delay(2000).fadeOut(300);
        else 
        {                         
            var form_data = new FormData($(this)[0]);
            //Добавляем ид типа обьекта сканирования
            form_data.append("IdPreview", $('#formEdImg .select_item').data('IdPreview'));
            form_data.append("IdTrainer", <?php echo ($_GET['id']); ?>);
            form_data.append("IdImage", data_.IdImage);
            $.ajax({
                  type: "POST",
                  url: "actions.php?type=7",
                  data: form_data,
                  success: function(data) 
                  {
                        $('#EdImg').modal('hide');
                        updateObj();
                        $("#formEdImg")[0].reset();
                        return false;
                  },
                  cache: false,
                contentType: false,
                processData: false
            });
            
        }
        e.preventDefault();
    });

  });
  </script>

</head>
<body>
<?php require_once '..' . DIRECTORY_SEPARATOR . 'site_top.php' ?>

<div class="container">
  <ul class="breadcrumb">
    <li><a href="../">Домой</a></li>
    <li class="active">Объекты контроля</li>
  </ul>

  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">Обьекты контроля</a></li>
    <li><a data-toggle="tab" href="#menu1">Типы обьектов контроля</a></li>
  </ul>

  <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
        <h3>Обьекты контроля</h3>
        <p>Добавление и удаление обьектов контроля</p>
        <p>Требования: формат файла - png с прозрачностью. Зазоры слева и справа от объекта не менее 25 пикселей. Высота изображения 700 пикселей.</p>

        <div class="row">
          <div class="col-md-1">
            <button type="button" data-toggle="modal" data-target="#addImg" class="btn btn-success">Добавить</button>
          </div>
        </div>
        </br>
        <div class="row imglist">
        </div>
    </div>
    <div id="menu1" class="tab-pane fade">
      <h3>Типы обьектов контроля</h3>
      <p>Добавление и удаление типов обьектов контроля</p>
      <p>Требования: формат файла - png с прозрачностью. Высота изображений не более 75 пикселей.</p>
        <div class="row">
          <div class="col-md-1">
            <button type="button" data-toggle="modal" data-target="#addPrevImg" class="btn btn-success">Добавить</button>
          </div>
        </div>
        </br>
       <div class="row tabPrev" style="margin: 0px;">      
    </div>

  </div>

  <!-- Modal -->
  <div class="modal fade" id="addImg" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Добавить изображение</h4>
        </div>
        <form id="formAddimg">
          <div class="modal-body">          
            <div class="form-group">
              <label for="text">Название:</label>
              <input type="text" name="Name" class="form-control" id="imagename">
            </div>
            <div class="form-group">
              <label for="filePrev ">Тип объекта контроля:</label>
              <div class="row filePrev" style="margin: 0px;">
              </div>
            </div>
             <div class="form-group">
              <label for="imagefile">Сканированное изображение:</label>
              <input type="file" name="imagefile" class="form-control">
            </div>
            <div id="message" class="alert alert-danger">
              <strong>Ошибка!</strong> Указаны не все данные.
            </div>
            <?php 
              DrawAdditionalFields($config);
             ?>          
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Добавить</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
          </div>        
        </form>
      </div>      
    </div>
  </div>


  <!-- Modal -->
  <div class="modal fade" id="addPrevImg" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Добавить изображение</h4>
        </div>
        <div class="modal-body">
          <form id="formAddPrevImg">
            <div class="form-group">
              <input type="file" name="imagefile" class="form-control">
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

  <!-- Modal Удалить объект -->
  <div class="modal fade" id="delImg" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Удалить объект контроля?</h4>
        </div>
        <div class="modal-body">
            <p>Вы действительно хотите удалить этот объект контроля</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Удалить</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Удалить тип объекта -->
  <div class="modal fade" id="delTypeImg" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Удалить тип объекта контроля?</h4>
        </div>
        <div class="modal-body">
            <p>Вы действительно хотите удалить этот тип объекта контроля</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Удалить</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
    </div>
  </div>



  <!-- Modal Edit Dlg-->
  <div class="modal fade" id="EdImg" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Редактирование объекта контроля</h4>
        </div>
        <form id="formEdImg">
          <div class="modal-body">          
            <div class="form-group">
              <label for="text">Название:</label>
              <input type="text" name="Name" class="form-control" id="imagename">
            </div>
            <div class="form-group">
              <label for="filePrev ">Тип объекта контроля:</label>
              <div class="row filePrev" style="margin: 0px;">
            </div>
            </div>
             <div class="form-group">
              <label for="imagefile">Сканированное изображение (только если хотите заменить текущее):</label>
              <input type="file" name="imagefile" class="form-control">
            </div>
            <?php 
              DrawAdditionalFields($config);
             ?>          
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Сохранить</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Отменить</button>
          </div>
        </form>
      </div>      
    </div>
  </div>


</br>
</br>
</br>
</div>

</body>
</html>