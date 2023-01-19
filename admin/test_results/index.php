<?php
  if (!isset($_SESSION))
    session_start();

  if (!( (isset( $_SESSION['isAdmin'])) && ($_SESSION['isAdmin'] == 1) )) {
    header("Location: ../auth.php ");
    die();
  }  

  if (!isset($_SESSION['SessionId']))
    die('Access Deny');  
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Панель Администратора</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../../js/bootstrap-3.3.5-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../js/jquery-ui/jquery-ui.css">
  <link rel="stylesheet" href="../../js/datepicker/bootstrap-datetimepicker.css">
  <link rel="stylesheet" href="../../js/datepicker/bootstrap-daterangepicker-master/daterangepicker.css">
  <script src="../../js/jquery.min.js"></script>
  <script src="../../js/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>
  <script src="../../js/jquery-ui/jquery-ui.js"></script>
  <script src="../../js/datepicker/moment-with-locales.js"></script>
  <script src="../../js/datepicker/bootstrap-datepicker.js"></script>
  <script src="../../js/datepicker/bootstrap-daterangepicker-master/daterangepicker.js"></script>  
  

  <style>
  .modal.modal-wide .modal-dialog {    
    min-width: 750px;
  }
  .navbar-form .form-group {
        margin-bottom:5px
  }
  </style>
  
  <script>
    function ShowFullInfo() {
      $.ajax({
              type: "POST",
              url: "actions.php?type=1",
              success: function(json) 
              { 
                var data = $.parseJSON(json);
                $("#studActions").empty();
                
                var tbl =  $("<table class='table table-hover'></table>");
                if (data!=null && data.length>0) {
                  tbl.append($("<tr><th>Время действия</th><th>Наименование действия</th><th></th></tr>"));
                }

                $(data).each(function(index, element) 
                {
                  var AlThisAct = '';
                  if (element.AlertThisAction == 1) {
                    AlThisAct='<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true" style="color:red;"></span>';
                  }
                  //Коррекетный вывод времени
                  var time = moment(element.ActivitiTime).format("HH:mm"); //DD.MM.YYYY

                  var tr = $("<tr><td>" + time + "</td><td>" + element.ActionName + "</td><td>" + AlThisAct + "</td></tr>");
                  tbl.append(tr);
                });
                
                $('#studActions').append(tbl);

                $('#SessionFullActionList').modal('show');
              }
            });
    }

  function SetFilters() {
    $(".days").parent().append($("<div class='daysNew'></div>"));
    $(".days").addClass("daysOld").removeClass("days");  

    var date1 = $("#datetimepicker1").data('date');
    if (date1=="" || date1 == undefined) 
      date1=null;
    var date2 = $("#datetimepicker2").data('date');
    if (date2=="" || date2 == undefined) 
      date2=null;

    $.ajax({
            type: "POST",
            url: "actions.php?type=0",
            data: { 
                    'StudentName': $("#StudentName").val(),
                    'IdTest': $('#menu').data("tag"),
                    'DateFrom' : JSON.stringify ({ "value": date1 }),
                    'DateTo' : JSON.stringify ({ "value": date2 })
                  },
            dataType: "json",  
            success: function(json) 
            {  
             $("#mainlist tbody").empty();
              if (json != null)
              {                
                var table = $("#mainlist");
                $.each(json, function(index, object)
                  {
                    var tr = $('<tr data-toggle="tooltip" title="Нажмите для просмотра вопросов и ответов в пределах данного сеанса тестирования" style="cursor:pointer" ><td>' + object.Name + '</td><td>' + object.TestName + '</td><td>' + object.Balls + '</td><td>' 
                              +  moment(object.StartDate).format("DD-MM-YYYY HH:mm:ss") + '</td><td>' + moment(object.EndDate).format("DD-MM-YYYY HH:mm:ss")  +'</td><td>'+ object.NumberAnswersForQuestions +'</td><td>'+ object.BallsForTest +'</td><td>'+ object.MaxBalls + '</td></tr>');

                    tr.data({"IdStudentSession" : object.IdSession});
                    tr.on('click', function(e) 
                    {
                        $(location).attr('href', "DetailedResults?IdSession=" + object.IdSession);          
                    });
                    tr.click(ShowFullInfo);
                    table.append(tr);
                  });
                }              
              /*if (json != undefined  && json !='')
              {
                var data = $.parseJSON(json);
                $.each(data, function(index, object) 
                {
                    var h = '<h3>' + object.LoginDate + '</h3>';
                    var table = $('<table class="table table-hover"><thead><tr><th>ID</th><th>Студент</th><th>Время действия</th>' +
                          '<th>Тренажёр</th><th>Последнее действие</th><th></th></tr></thead><tbody></tbody></table>');

                    $.each(object.Sessions, function(index, object)
                    {
                        var AlThisAct = '';
                        if (object.AlertThisAction == 1) {
                          AlThisAct='<span  title="В пределах данной сессии были замечены действия, направленные на взлом системы" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true" style="color:red;"></span>';
                        }

                        //Коррекетный вывод времени
                        var time = moment(object.LastActionTime).format("HH:mm"); //DD.MM.YYYY

                        var tr = $('<tr><td>' + object.IdStudUser + '</td><td>' + object.UserName + '</td><td>' + time + '</td><td>' + object.TrainerName + '</td><td class="col-md-6">' + object.LastActionName +'</td><td>'+ AlThisAct + '</td></tr>');

                        tr.data({"IdStudentSession" : object.IdSession});
                        tr.click(ShowFullInfo);
                        table.append(tr);
                    });

                   $('.daysNew').append(h);
                   $('.daysNew').append(table);
                   //console.log(object);
                });                
              }
              $(".daysOld").remove();
              $(".daysNew").addClass("days").removeClass("daysNew");*/
            },
            cache: false
        });    
  }

  $.ajax({
            type: "POST",
            url: "actions.php?type=2",
            success: function(json) 
            {
              var data = $.parseJSON(json);
              var list = $(".dropdown-menu");

              $.each(data, function(index, object) {
                  var tr = $('<li role="presentation"><a role="menuitem" tabindex="' + object.IdTest +'" href="#">' + object.TestName + '</a></li>');
                  list.append(tr);
              });

              //Выделяем элементы в dropdown
              $(".dropdown-menu li a").click(function()
              {
                $('#menu').data("tag", $(this).context.tabIndex);
                $(".dropdown-toggle:first-child").text($(this).text());
                $(".dropdown-toggle:first-child").val($(this).text());
                SetFilters();
              });
            }
  });

  $(document).ready(function() 
  {
    $("#HeaderName").text('Результаты тестирования');

    var d = d= new Date().setMonth(new Date().getMonth()-1)
    var formatter = new Intl.DateTimeFormat("ru","day.month.year");
    
    $('#datetimepicker1').datetimepicker({format: 'DD.MM.YYYY'}).on("dp.change dp.update",function() {SetFilters();}); 
    $('#datetimepicker2').datetimepicker({format: 'DD.MM.YYYY'}).on("dp.change dp.update",function() {SetFilters();});
    $('#datetimepicker1').val(formatter.format(d));
    $("#datetimepicker1").data('date',formatter.format(d))
    $("#ShowOnlyAlerts").click(SetFilters);

    $("#StudentName").bind("input propertychange", function (evt) {
        // If it's the propertychange event, make sure it's the value that changed.
        if (window.event && event.type == "propertychange" && event.propertyName != "value")
            return;
        // Clear any previously set timer before setting a fresh one
        window.clearTimeout($(this).data("timeout"));
        $(this).data("timeout", setTimeout(function () {
            SetFilters();
        }, 1200));
    });
    SetFilters();
  });

  </script>

</head>
<body>
<?php require_once '..' . DIRECTORY_SEPARATOR . 'site_top.php' ?>

<div class="container">
  <ul class="breadcrumb">
    <li><a href="../">Домой</a></li>
    <li class="active">Результаты тестирования</li>
  </ul>

<div class="container">

<div class="panel panel-info"> 
  <div class="panel-heading"> 
    <h3 class="panel-title">Фильтры
    </h3> 
  </div> 
  <div class="panel-body"> 
    <form class="navbar-form navbar-left" role="search">
      <div class="form-group">
        <div class="input-group">
          <label class="input-group-addon" id="basic-addon1" for="StudentName">Наименование теста</label>
          <div class="dropdown">
            <button class="btn btn-default dropdown-toggle" type="button" id="menu" data-toggle="dropdown">Выберите при необходимости<span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1"></ul>
          </div>
        </div>         
      </div>
      <div class="form-group">
        <div class="input-group">
          <label class="input-group-addon" id="basic-addon1" for="StudentName">ФИО или группа</label>
          <input type="text" class="form-control" placeholder="" aria-describedby="basic-addon1" id="StudentName" />
        </div>         
      </div>
      <div class="form-group ">
        <div class='input-group date' >
          <label class="input-group-addon" id="basic-addon1" for="datetimepicker1">Даты с </label>
          <input type='text' class="form-control" id='datetimepicker1'>
          <label class="input-group-addon" id="calndr1" for="datetimepicker1">
            <span class="glyphicon glyphicon-calendar"></span>
          </label>
          <label class="input-group-addon" id="basic-addon2" for="datetimepicker2"> по </label>
          <input type='text' class="form-control" id='datetimepicker2'/>
          <label class="input-group-addon" id="calndr2" for="datetimepicker2">
            <span class="glyphicon glyphicon-calendar"></span>
          </label>
        </div>
      </div>   
    </form>
  </div> 
</div>

  
  <br/>
  <div class="days"></div>  
</div>

 <!-- Modal Session action list -->
  <div class="modal modal-wide fade" id="SessionFullActionList" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Полный список действий студента в пределах данной сессии</h4>
        </div>
        <div class="modal-body" id="studActions">
        </div>
      </div>      
    </div>
  </div>

  <table class="table table-hover" id="mainlist">
    <thead>
      <tr>
        <th>ФИО студента</th>
        <th>Название теста</th>
        <th>Оценка за тест (шкала 100 баллов)</th>
        <th>Дата начала</th>
        <th>Дата окончания</th>
        <th>Отвечено вопросов</th>
        <th>Получено баллов</th>
        <th>Максимум баллов</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>

</body>
</html>