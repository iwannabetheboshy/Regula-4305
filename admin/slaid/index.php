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
  <script src="../../js/jquery.min.js"></script>
  <script src="../../js/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
  <script src="../../js/jquery-ui/jquery-ui.js"></script>
  <style>
    .carousel-inner > .item > img,
    .carousel-inner > .item > a > img {
      /*  width: 70%; */
       margin: auto;
       width: auto;
       height: 600px;
    }
    .carousel-caption p {
      font-size: 17px;
      padding: 0px;
      background: #B9121B;
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      border-bottom-right-radius: 10px;
      border-bottom-left-radius: 10px;
    }
  </style>

  <script>
    $(document).ready(function() {  

      var fixHelperModified = function(e, tr) {
          var $originals = tr.children();
          var $helper = tr.clone();
          $helper.children().each(function(index) {
              $(this).width($originals.eq(index).width())
          });
          return $helper;
      },

      updateIndex = function(e, ui) 
      {
          $('td.index', ui.item.parent()).each(function (i) {
              $(this).html(i + 1);
          });

          var Slides = new Array();
          $('tr', ui.item.parent()).each(function( i, val ) 
          {
              Slides.push($(val).data('IdSlide'));
              //console.log($(val).data('IdSlide'));
              //console.log(i + 1);
          });

          $.ajax({
            type: "POST",
            url: "actions.php?type=3",
            data: { IdTrainer: <?php echo ($_GET['id']); ?> , Slides: Slides},
            success: function(json) 
            {    
                //console.log(json);
            },
            cache: false
          });
      };

      $("#start_sort tbody").sortable({
          helper: fixHelperModified,
          stop: updateIndex
      }).disableSelection();

      $("#end_sort tbody").sortable({
          helper: fixHelperModified,
          stop: updateIndex
      }).disableSelection();

      function Tabs(carusel, table, object, index) 
      {
            var indicator = $('<li data-target="#startCarousel" data-slide-to="' + (object.SlidePosition - 1) + '"></li>');
            carusel.find('.carousel-indicators').append(indicator);

            var body = $('<div class="item"><img src="/images/slaid/' + object.ImagePath + '" alt="" width="460" height="345">'+
              //+ '<div class="carousel-caption"><h3>' + object.SlideCaption + '</h3><p>' + object.SlideText + '</p></div>' +
               '<div class="carousel-caption"><p>' + object.SlideText + '</p></div>' +
              '</div>');

            if (object.SlidePosition == 1)
            {  
              body.addClass('active');
              indicator.addClass('active');
            }
            carusel.find('.carousel-inner').append(body);

            var tr = $('<tr><td class="index">' + object.SlidePosition  + '</td><td>' + object.SlideCaption + '</td><td>' + 
              object.SlideText + '</td><td>' + object.ImagePath + '</td><td>' + 
              '<button type="button" data-toggle="modal" class="glyphicon glyphicon-remove btn-link"></button>' + '</td></tr>');

            tr.data("IdSlide", object.IdSlide);
            tr.find(".glyphicon-remove").first().on('click', function() 
              {  
                //Удаление обьекта сканирования
                $('#delSlaid').modal('show');
                $('#delSlaid').find('.btn-primary').one('click', 
                  function() {
                    $.ajax({
                        type: "POST",
                        data: { IdSlide: object.IdSlide, ImagePath: object.ImagePath },
                        url: "actions.php?type=2",
                        success: function(json) 
                        {    
                            tr.remove();
                            indicator.remove();
                            body.remove();
                            table.find('tbody').sortable();

                            /* Перерасчитывается порядок в таблице, можно написать иии получше... */ 
                            var Slides = new Array();
                            $('tr', table.find('tbody')).each(function( i, val ) 
                            {
                                Slides.push($(val).data('IdSlide'));
                                $(val).find('td.index').html(i + 1);
                            });

                            $.ajax({
                              type: "POST",
                              url: "actions.php?type=3",
                              data: { IdTrainer: <?php echo ($_GET['id']); ?> , Slides: Slides},
                              success: function(json) 
                              {    
                                  //console.log(json);
                              },
                              cache: false
                            });

                            $('#delSlaid').modal('hide');
                            return false;
                        },
                        cache: false
                      });
                     // console.log(object.ImagePath + " " + object.IdSlide);
                      return false;
                  });
                return false;
             });

            tr.on('click', function(e) 
            {
               $("input[name='editSlideCaption']").val(object.SlideCaption);
               $("textarea[name='editSlideText']").val(object.SlideText);
               $("input[name='editSlidePosition']").val(object.SlidePosition);
               $("input[name='editIdSlide']").val(object.IdSlide);
               $('#editSlaid').modal('show');
            });

            table.find('tbody').append(tr);
      };

      function getSlaides()
      { //Загрузка слайдов
        $.ajax({
            type: "POST",
            url: "actions.php?type=0",
            data: { IdTrainer: <?php echo ($_GET['id']); ?>},
            success: function(json) 
            {    
                $('#startCarousel li').remove();
                $('#endCarousel li').remove();
               
                $('#startCarousel .carousel-inner').children().remove();
                $('#endCarousel .carousel-inner').children().remove();

                $('#start_sort tbody tr').remove();
                $('#end_sort tbody tr').remove();

                var data = $.parseJSON(json);
                $.each(data, function(index, object) 
                {
                    if (object.IsBegin == 1)
                       Tabs($('#startCarousel'), $('#start_sort'), object, index);
                    else
                       Tabs($('#endCarousel'), $('#end_sort'), object, index); 
                });
            },
            cache: false
        });
      }

      $("#addFormSlaid").on('submit', function(e) 
      {
          /*Примечание: заголовок слайда мы не выводим (в режиме просмотра), поэтому будем считать, что он не обязательный*/ 
          /* ну такое -_- */ 
          /*if ($("input[name='SlideCaption']").val().length == 0)
            $('#message').html('Пожалуйста, укажите заголовок слайда').fadeIn(300).delay(2000).fadeOut(300);
          else*/
          if ($("textarea[name='SlideText']").val().length == 0)
            $('#message').html('Пожалуйста, укажите описание слайда').fadeIn(300).delay(2000).fadeOut(300);
          else
            if ($("input[name='imagefile']").get(0).files.length == 0)
              $('#message').html('Пожалуйста, добавьте изображение для слайда').fadeIn(300).delay(2000).fadeOut(300);
            else 
            {            
                var form_data = new FormData($(this)[0]);
                //Добавляем в запрос нужные поля
                form_data.append("IdTrainer", <?php echo ($_GET['id']); ?>);
                $.ajax({
                      type: "POST",
                      url: "actions.php?type=1",
                      data: form_data,
                      success: function(data) 
                      {
                            $('#addSlaid').modal('hide');
                            $('#addFormSlaid')[0].reset();
                            //Релоадим слайды
                            getSlaides();
                            return false;
                      },
                      cache: false,
                    contentType: false,
                    processData: false
                });
                
            }
          e.preventDefault();
      });

      $("#addSlaid").on('shown.bs.modal', function(e) 
      {
        if (e.relatedTarget.id == 'endAddBtn')
          $("input[name='IsBegin']").val(0);
        else 
          $("input[name='IsBegin']").val(1);
      });

      $('#editFormSlaid').on('submit', function(e) 
      {
          var SlideText = $("textarea[name='editSlideText']").val();
          var SlideCaption = $("input[name='editSlideCaption']").val();
          var SlidePosition = $("input[name='editSlidePosition']").val();          
          var form_data = new FormData($(this)[0]);
          $.ajax({
              type: "POST",
              data: form_data,
              url: "actions.php?type=4",
              success: function(json) 
              {    
                  $('#editSlaid').modal('hide');
                  $('#editFormSlaid')[0].reset();
                   //Релоадим слайды
                  getSlaides();
                  return false;
              },
              cache: false,
              contentType: false,
              processData: false,
            });  
            e.preventDefault();
      });

      getSlaides();
      $("#HeaderName").text('Слайды');
    });
  </script>

</head>
<body>
<?php require_once '..' . DIRECTORY_SEPARATOR . 'site_top.php' ?>

<div class="container">
  <ul class="breadcrumb">
    <li><a href="../">Домой</a></li>
    <li class="active">Слайды</li>
  </ul>

<div class="container">
  <h2>Просмотр</h2>
  <p>Слайды будут отображаться при начале работы с тренажёром:</p>

  <ul class="nav nav-tabs">
    <li class="active"><a href="#start" data-toggle="tab">Начало работы</a></li>
    <li><a href="#end" data-toggle="tab">Конец работы</a></li>
  </ul>
  <br>

  <!-- Tab panes -->
  <div class="tab-content">
    <div class="tab-pane fade in active" id="start">

      <div id="startCarousel" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators"></ol>
        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox"></div>

        <!-- Left and right controls -->
        <a class="left carousel-control" href="#startCarousel" role="button" data-slide="prev">
          <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#startCarousel" role="button" data-slide="next">
          <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </a>
      </div>

      <br/>
      <h2>Редактирование</h2>
      <p>Изменяйте порядок слайдов перетаскиванием мыши, добавляйте слайды, удаляйте и редактируйте информацию</p>

      <button type="button" id="startAddBtn" data-toggle="modal" data-target="#addSlaid" class="btn btn-success">Добавить</button>
      <table id="start_sort" class="table  table-hover">
          <thead>
              <tr><th class="index">Порядок</th><th>Заголовок</th><th>Описание</th><th>Изображение</th><th></th></tr>
          </thead>
          <tbody></tbody>
      </table>

    </div>
    <div class="tab-pane fade" id="end">  
      <div id="endCarousel" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators"></ol>
        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox"></div>
        <!-- Left and right controls -->
        <a class="left carousel-control" href="#endCarousel" role="button" data-slide="prev">
          <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#endCarousel" role="button" data-slide="next">
          <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </a>
      </div>

      <br/>
      <h2>Редактирование</h2>
      <p>Изменяйте порядок слайдов перетаскиванием мыши, добавляйте слайды, удаляйте и редактируйте информацию</p>

      <button type="button" id="endAddBtn" data-toggle="modal" data-target="#addSlaid" class="btn btn-success">Добавить</button>
      <table id="end_sort" class="table  table-hover" title="Kurt Vonnegut novels">
          <thead>
              <tr><th class="index">Порядок</th><th>Заголовок</th><th>Описание</th><th>Изображение</th><th></th></tr>
          </thead>
          <tbody></tbody>
      </table>

    </div>
  </div>
  <br>
  <br>
  
  <!-- Modal Add -->
  <div class="modal fade" id="addSlaid" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Добавить слайд</h4>
        </div>
        <form id="addFormSlaid">
          <div class="modal-body">
            <div class="form-group">
              <label for="SlideCaption">Заголовок</label>
              <input type="text" class="form-control" name="SlideCaption">
            </div>
            <div class="form-group">
              <label for="comment">Описание</label>
              <textarea class="form-control" rows="5" name="SlideText"></textarea>
            </div>
            <div class="form-group">
              <input type="hidden" class="form-control" name="IsBegin">
            </div>
             <div class="form-group">
              <label for="imagefile">Изображение</label>
              <input type="file" class="form-control" name="imagefile">
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
  <div class="modal fade" id="delSlaid" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Удалить слайд?</h4>
        </div>
        <div class="modal-body">
          <p>Вы действительно хотите удалить слайд?</p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Удалить</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
      
    </div>
  </div>

  <!-- Modal Edit -->
  <div class="modal fade" id="editSlaid" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Изменить слайд</h4>
        </div>
          <form id="editFormSlaid">
            <div class="modal-body">
              <div class="form-group">
                <input type="hidden" class="form-control" name="editIdSlide">
              </div>
              <div class="form-group">
                <label for="SlideCaption">Заголовок</label>
                <input type="text" class="form-control" name="editSlideCaption">
              </div>
              <div class="form-group">
                <label for="comment">Описание</label>
                <textarea class="form-control" rows="5" name="editSlideText"></textarea>
              </div>
               <div class="form-group">
                <label for="imagefile">Изображение</label>
                <input type="file" class="form-control" name="editImagefile">
              </div>
              <div id="message" style="display: none;" class="alert alert-danger">
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

</div>

</body>
</html>