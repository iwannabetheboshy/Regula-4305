 //Переменные
var canvas, ctx;
var canvasBuf, ctxBuf; //Canvas Buffer - буфер для преобразований
var stopStep = 0;

 function toggePanel() 
 {
    $('#btnpanel').hide();
    $('#dialog').dialog('open');
 }

 function powerOff() 
 {
   /* if (State == 4)
    {*/
      //State = 5;
      $('.main').hide();
      $('.loader').fadeIn(500);

      setTimeout(function() 
      { 
          $('#panel-responsive-page1').css('background', '#000');
          $('.loader').fadeOut(1000);
          //State = 6;  //Тут выход из винды с тайм аутом
          $('.panel').removeClass('panel_power').addClass('panel_key2');
          //$('#nav-panel').trainerSidePanel("stopTrainer");          
      } , 4000);
   /* }*/    
 };

  function reset(st)
 {
    State = st;
    if (State != 0)
    {
      var res = setInterval(function() 
      {
        if ($('.loader').is(":visible"))
          $('.loader').hide();
        
        if ($('.main').is(":visible"))
          $('.main').hide();

        if ($('.main').is(":visible") == false)
          $('#info').fadeIn(1000);

        if ($('.loader').is(":visible") == false && $('.main').is(":visible") == false && $('#info').is(":visible"))
          clearInterval(res);
      }, 100);
      
      $('#info').find('.ui-icon-home').show();
      $('#info').find('.ui-icon-refresh').show();
      $('#info').find('.ui-icon-carat-d').show();

      // if - ы - switch - и
      switch(st) {
          case 5:
            $('#info').find('.ui-bar').addClass('success');
            $('#info').find('.ui-bar h3').html("Работа завершена корректно.");
            $('#info').find('.ui-body p').html("Работа с комплексом была корректно завершена.");
            break;
          case 4:
            $('#info').find('.ui-bar').addClass('warning');
            $('#info').find('.ui-bar h3').html("Работа завершена некорректно!");
            $('#info').find('.ui-body p').html("Произведено некорректное отключение комплекса. Была нажата кнопка аварийного отключения!");
            break;
          default:
            break;
        } 

      $('.ui-icon-check').hide();
      $('#menubtn').hide();
    }
 }

  function powerWin() {
    //Начинается веселье    
    //if (State == 3 || State == 6)
    {
      $('.panel').removeClass('panel_key2').addClass('panel_power'); 

      $('.loader').fadeIn(1000);
      $('#info').fadeOut(500);

      $('#dialog').dialog('close');
      $('#nav-panel').panel("close");
      $('#btnpanel').hide();
      //$("#nav-panel").trainerSidePanel("startTrainer1");
      
      setTimeout(function() 
      { 
        $('.loader').fadeOut(1000);
        $('.main').fadeIn(1000);

        //$('#dialog').dialog('close');
        $('#nav-panel').panel("open");
        //$('#btnpanel').hide();
        toggePanel();
        //State = 4;
        //Передаю когда можно передавать багаж
        //$("#nav-panel").trainerSidePanel("startTrainer2");
      } , 4000);
    }
 }

$( document ).ready(function() 
{
   var trainer = new imgProcessing();
   //Resize
   window.addEventListener('resize', trainer.resizeCanvas, false);

   var menu = "#nav";
   var position = {my: "left top", at: "left bottom"};
  
   $(menu).menu({  
      position: position,
     // icons: {submenu: " ui-icon-circle-arrow-e"},
      blur: function() {
        $(this).menu("option", "position", position);
        },
      focus: function(e, ui) {
        
        if ($(menu).get(0) !== $(ui).get(0).item.parent().get(0)) {
          $(this).menu("option", "position", {my: "left top", at: "right top"});
          }
      }
    });

   $('.ui-menu-icon.ui-icon.ui-icon-carat-1-e').remove();

  var progressTimer,
      progressbar = $( "#progressbar" ),
      progressLabel = $( ".progress-label" ),
      dialogButtons = [{
      text: "Stop",
      click: closeDownload
    }],
      dialog = $( "#dialog_start" ).dialog({
      autoOpen: false,
      width: 400,
      closeOnEscape: false,
      resizable: false,
      buttons: dialogButtons,
      open: function() {
        progressTimer = setTimeout( progress, 2000 );
        Trainer.RadOn();
      },
      beforeClose: function() {
      }
    });

  progressbar.progressbar({
    value: false,
    max: 100,
    change: function() {
      progressLabel.text( "Идет процесс регистрации: " + progressbar.progressbar( "value" ) + "%" );
    },
    complete: function(event) {

      progressLabel.text( "Регистрация завершена!" );
      dialog.dialog( "option", "buttons", [{
        text: "Закрыть",
        click: closeDownload
      }]);
      $(".ui-dialog button").last().trigger( "focus" );

      if (Trainer.GetCurrentImage()!= null)  {
          trainer.img.src = "../../images/scans/" + Trainer.GetCurrentImage();
          $(".curfile .field").html(Trainer.GetCurrentImage()); 
      }
      Trainer.RadOff();
    }
  });

  function progress() {
    var val = progressbar.progressbar( "value" ) || 0;
    progressbar.progressbar( "value", val + 1/*Math.round( Math.random() * 3 ) */);
    if ( val <= 99 ) {
      progressTimer = setTimeout( progress, 70 );
    }
  }

  function closeDownload() {
    clearTimeout( progressTimer );
    dialog.dialog( "option", "buttons", dialogButtons )
          .dialog( "close" );
    progressbar.progressbar( "value", false );
    progressLabel.text( "Рентгеновский преобразователь включен" );
    Trainer.RadOff();
  }

  $( "#slider1" ).slider({
      min: -50,
      step: 5,
      max: 50,
      disabled: true
    });

  $( "#contrSlider, #brigtSlider" ).slider({
      min: -100,
      step: 2,
      max: 100,
      disabled: true
    });

  $('#img1, #img2, #img3, #img4').on("click", function () 
  {
     if ($(this).css('background-image') != "none")
     {
        var bg = $(this).css('background-image');
        bg = bg.replace('url(','').replace(')','').replace(/\"/gi, "");
        trainer.img.src = bg;
        $(this).css('background-image', "");
     } else
       $(this).css('background-image', "url('" + canvas.toDataURL("image/png") + "')");
  });
  
  $( "#dialog-images" ).dialog({
    resizable: false,
    autoOpen: false,
    height: 280,
    width: 600,
    modal: false
  });

  $('.negativ').on('click', function() 
  {
    LogStudentActions(125);
      if (trainer.negativ())
      {
        $('.status').html("Негатив");
        $('.mobiDick .negativ').append("<div class='on'>On</div>");
        $('.negativ').find('span').addClass('ui-icon ui-icon-check');
      } else
      {
        $('.status').html("");
        $('.mobiDick .negativ').find(".on").remove();
        $('.negativ').find('span').removeClass('ui-icon ui-icon-check');
      }
  });

  $('.relief').on('click', function() 
  {
      if (trainer.relief())
      {
        $('.status').html("Рельеф");
        $('.mobiDick .relief').append("<div class='on'>On</div>");
        $('.relief').find('span').addClass('ui-icon ui-icon-check');
      } else
      {
        $('.status').html("");
        $('.mobiDick .relief').find(".on").remove();
        $('.relief').find('span').removeClass('ui-icon ui-icon-check');
      }
      LogStudentActions(133);
  });

  $('.loupe').on('click', function() 
  {
    LogStudentActions(129);
      if (trainer.enableLoop())
      {
        $('.sliders').hide();
        $('.curfile').hide();
        $('.zoom_win').show();

        $('.status').html("Линза");
        $('.mobiDick .loupe').append("<div class='on'>On</div>");
        $('.loupe').find('span').addClass('ui-icon ui-icon-check');
      } else
      {
        $('.zoom_win').hide();
        $('.curfile').show();
        $('.sliders').show();

        $('.status').html("");
        $('.mobiDick .loupe').find(".on").remove();
        $('.loupe').find('span').removeClass('ui-icon ui-icon-check');
      }
  });

  $('.filtr').on('click', function() 
  {
      if (trainer.filtr(0))
      {
        $('.status').html("Фильтр");
        $('.mobiDick .filtr').append("<div class='on'>On</div>");
        $('.filtr').find('span').addClass('ui-icon ui-icon-check');
      } else
      {
        $('.status').html("");
        $('.mobiDick .filtr').find(".on").remove();
        $('.filtr').find('span').removeClass('ui-icon ui-icon-check');
      }
      LogStudentActions(132);
  });

  $('.grid').on('click', function() 
  {    
      if (!trainer.gridMode)
      {
        $('.status').html("Сетка (100X100)");
        $('.mobiDick .grid').append("<div class='on'>On</div>");
        $('.grid').find('span').addClass('ui-icon ui-icon-check');
        trainer.grid(100, 100, true);
      } else
      {
        trainer.grid(100, 100, true);
        $('.status').html("");
        $('.mobiDick .grid').find(".on").remove();
        $('.grid').find('span').removeClass('ui-icon ui-icon-check');
      }
      LogStudentActions(131);
  }); 

  $('.contrast').on('click', function() 
  {      
      if (!trainer.contrMode)
      {
        $('.status').html("Яркость-Контраст");
        trainer.contrMode = true;
        $('.mobiDick .contrast').append("<div class='on'>On</div>");
        $('.contrast').find('span').addClass('ui-icon ui-icon-check');
      } else
      {
        trainer.contrMode = false;
        $('.status').html("");
        $('.mobiDick .contrast').find(".on").remove();
        $('.contrast').find('span').removeClass('ui-icon ui-icon-check');
      }
      LogStudentActions(127);
  });  

  $('.psevdo').on('click', function() 
  {
      if (trainer.pcevdocolor(0))
      {
        $('.status').html("Псевдоцвет");
        $('.mobiDick .psevdo').append("<div class='on'>On</div>");
        $('.psevdo').find('span').addClass('ui-icon ui-icon-check');
      } else
      {
        $('.status').html("");
        $('.mobiDick .psevdo').find(".on").remove();
        $('.psevdo').find('span').removeClass('ui-icon ui-icon-check');
      }
      LogStudentActions(134);
  });  

  $('.roi').on('click', function() 
  {      
      if (trainer.enableRoi())
      {
        $('.status').html("Область интереса (ROI)");
        $('.mobiDick .roi').append("<div class='on'>On</div>");
        $('.roi').find('span').addClass('ui-icon ui-icon-check');
      } else
      {
        $('.status').html("");
        $('.mobiDick .roi').find(".on").remove();
        $('.roi').find('span').removeClass('ui-icon ui-icon-check');
      }
      LogStudentActions(126);
  });  

  $(".okey").on("click", function() 
  {
    LogStudentActions(140);
  })

  $(".cancel").on("click", function() 
  {
    $('#nav li ul li span').removeClass('ui-icon ui-icon-check');

    if ($('#dialog-images').dialog('isOpen'))
      $('#dialog-images').dialog('close');

    $( "#slider1" ).slider( "option", "value", 0 );
    $( "#brigtSlider" ).slider( "option", "value", 0 );
    $( "#contrSlider" ).slider( "option", "value", 0 );

    $('.status').html("");
    $('.mobiDick').find(".on").remove();

    trainer.gridMode = 0;
    trainer.contrMode = false;
    trainer.negativMode = false;
    trainer.reliefMode = false;
    trainer.filtrMode = false;
    trainer.pcevdoMode = false;

    if (trainer.zoomMode)
    {  
      trainer.enableLoop();
      $('.zoom_win').hide();
      $('.curfile').show();
      $('.sliders').show();
    }

    if (trainer.roiMode)
      trainer.enableRoi();

    trainer.clear();
    LogStudentActions(120);
  });

  $('.quality').on('click', function() {

    if ($('.quality_text').html() == "Обычное качество")
    {
      $('.quality_text').html("Улучшеное качество");
      LogStudentActions(136);
    }
    else
    {
      $('.quality_text').html("Обычное качество");
      LogStudentActions(135);
    }    
  });

  $('.quality1').on('click', function() {
      $('.quality_text').html("Обычное качество");
  });

  $('.quality2').on('click', function() {
      $('.quality_text').html("Улучшеное качество");
  });

  $('.open').on('click', function() 
  {
     $('.status').html("Загрузка файла");
     openImage();
     LogStudentActions(138);
  });

  $(".up, .down, .right, .left")
   .on("click", function(e) 
   {
      if ($('#dialog-images').dialog('isOpen'))
      {
          var index = $("#menu_images .selected").index();
          $( "#menu_images .selected" ).removeClass('selected');

          if ($(this).hasClass("two"))
            $($("#menu_images").children().get(index + 1)).addClass("selected");

          if ($(this).hasClass("eight"))
            $($("#menu_images").children().get(index - 1)).addClass("selected");
      }
   });

   $(".delete").on("click", function(e) {
      LogStudentActions(139);
   });

  //Отключаем правый клик на панели
  /* $(".panel").on("contextmenu",function(e){
       return false;
  }); */

   $(".save").on("click", function() 
   {
      $.ajax({
        type: "POST",
        url: "actions.php?type=1",
        data: { 
           type: 1,
           img_data: canvas.toDataURL("image/png")
        },
        cache: false,
        async: false,
        dataType: "json"
      }).done(function(result) {
          //console.log(result);
          $('.status').html("Записано: " + result.IdSavedImage);
      });

      LogStudentActions(121);
   });

   function setContrBright(type, value)
   {
      trainer.clear();      
      var contrast = $("#contrSlider").slider( "value" );
      trainer.contrBright(contrast, $("#brigtSlider").slider( "value" ));

      type.slider( "value", type.slider( "value" ) + value);
   }

   $(".up, .down, .right, .left").on("mousedown", 
   function(e) 
   {
      var self = this;
      if (trainer.zoomMode == 1)
      {
        if (e.ctrlKey)
        {   //создаю копию обьекта чтобы передать в функцию
             var zoom = $(this).clone();
             zoom.addClass("zoom");
             trainer.loupeMove(zoom);
        } else
          trainer.zoomMouseDown = setInterval(function() { trainer.loupeMove($(self)) }, 100);
      } else
        if (trainer.gridMode)
        {
            var res;
            if ($(self).hasClass("up"))
                res = trainer.grid(0, 5, false);
            if ($(self).hasClass("down"))
                res = trainer.grid(0, -5, false);
            if ($(self).hasClass("left"))
                res = trainer.grid(-5, 0, false);
            if ($(self).hasClass("right"))
                res = trainer.grid(5, 0, false);
            $('.status').html("Сетка (" + res.x + "X" + res.y + ")");
        } else
          if (trainer.contrMode) 
          {
              if ($(self).hasClass("up"))
              {  
                trainer.btnInterval = setInterval(function() {
                  setContrBright($( "#contrSlider"), 10);
                }, 10);
              }
              if ($(self).hasClass("down"))
              {  
                trainer.btnInterval = setInterval(function() {
                  setContrBright($( "#contrSlider"), -10);
                }, 10);
              }

              if ($(self).hasClass("left"))
              {
                trainer.btnInterval = setInterval(function() {
                  setContrBright($( "#brigtSlider"), -10);
                }, 10);
              }
              if ($(self).hasClass("right")) {
                trainer.btnInterval = setInterval(function() {
                  setContrBright($( "#brigtSlider"), 10);
                }, 10);
              }
          } else
            if (trainer.roiMode)
            {  
               if (e.ctrlKey)
               {
                  if ($(self).hasClass("up"))
                    $('#roi').height($('#roi').height() - 5);

                  if ($(self).hasClass("down"))
                    $('#roi').height($('#roi').height() + 5);

                  if ($(self).hasClass("left"))
                    $('#roi').width($('#roi').width() - 5);

                  if ($(self).hasClass("right"))
                    $('#roi').width($('#roi').width() + 5);
               
               } else
                trainer.zoomMouseDown = setInterval(function() { trainer.roiMove($(self)) }, 100);
            } else
              if (trainer.pcevdoMode)
                trainer.pcevdocolor(1);
              else
                if (trainer.filtrMode)
                {
                  if ($(self).hasClass("left") || $(self).hasClass("down"))
                  {
                    if ($("#slider1").slider( "value" ) < 0)
                    {
                      trainer.clear();
                      trainer.filtr(1);
                      $("#slider1").slider( "value", $("#slider1").slider( "value" ) - 5);
                    }
                  }

                  if ($(self).hasClass("right") || $(self).hasClass("up"))
                  {
                     if ($("#slider1").slider( "value" ) > 0)
                     {
                        trainer.clear();
                        trainer.filtr(2);
                        $("#slider1").slider( "value", $("#slider1").slider( "value" ) + 5);
                     }
                  }
                }
      LogStudentClassActions($(self));                
   });

   $(".up, .down, .right, .left").on("mouseup mouseleave", 
   function(e) 
   {
      if (!e.ctrlKey)
        clearInterval(trainer.zoomMouseDown);

      if (trainer.contrMode)
        clearInterval(trainer.btnInterval);
      LogStudentClassActions($(self));
   });

   function openImage() 
   {
      $.ajax({
        type: "POST",
        url: "actions.php",
        data: { type: 3, IdTrainer: 3 },
        cache: false,
        async: false,
        dataType: "json",
        success: function (data) 
        {
            $( "#menu_images" ).children().remove();
            if (data != undefined)
            {
              $.each(data, function(index, object) {

                  var time = moment(object.SaveTime);

                  //class="ui-li-thumb"
                  
                  //var tr = $('<li><a href="#">'+ '<h2>000000' + object.ImagePath + ', ' + time.format("DD-MMMM-YYYY HH:mm:ss") + '</h2></a></li>');
                  //'<img src="../../images/StudentImages/' + object.ImagePath + '" style="width: 100px; height: 100px;">'

                  var tr = $('<li><div>000000' + object.ImagePath + ', ' + time.format("DD-MMMM-YYYY HH:mm:ss") + '</div></li>');

                  
                  tr.data('ImagePath', object.ImagePath);
                  tr.on('click', function() 
                  {
                      $( "#menu_images .selected" ).removeClass('selected');
                      $(this).addClass('selected');
                  });

                  tr.on('dblclick', function() 
                  {
                      $('#dialog-images').dialog('close');
                      //Просто отображение загрузчи
                      var $this = $( this ),
                        theme = $this.jqmData( "theme" ) || $.mobile.loader.prototype.options.theme,
                        msgText = $this.jqmData( "msgtext" ) || $.mobile.loader.prototype.options.text,
                        textVisible = $this.jqmData( "textvisible" ) || $.mobile.loader.prototype.options.textVisible,
                        textonly = !!$this.jqmData( "textonly" );
                        html = $this.jqmData( "html" ) || "";

                      $.mobile.loading( "show", {
                              text: msgText,
                              textVisible: textVisible,
                              theme: theme,
                              textonly: textonly,
                              html: html
                      });
                      //Главное изображение
                      trainer.img.src = "../../images/StudentImages/" + object.ImagePath;
                  });

                  $( "#menu_images" ).append(tr);   
              });
            }
        }
      });

      $( "#menu_images" ).menu();
      $( "#menu_images" ).menu("refresh");
      $('#dialog-images').dialog('open');
      
   };

   $(".start").on("click", function() 
   {
      if (State == 3)
      {
        LogStudentActions(122);
        if (Trainer.GetCurrentImage()!= null)  {
          $("#dialog_start").dialog( "open" );
        }
      }
   });

   $(".stop").on("click", function() 
   {      
      LogStudentActions(124);
      closeDownload();
      stopStep++;

      //State = 9;
      if (stopStep > 2)
      {
        NotebookState = 5;
        Trainer.powerOff();
        /*reset(State);       Делается в функции выше, когда надо*/
      }
   });

   $(".stop").on("mouseleave", 
   function(e) 
   {
      stopStep = 0;
   });

   $('.poweron').on('click', function() 
   {
      powerWin();
   });

   $('.off').on('click', function() 
   {
      powerOff();
      setTimeout(function() {
          //State = 9;
          reset(State);
      },3500);
   });

   $('#info').find('.ui-icon-home').on('click', function()
   {
      window.location.replace("/vfrta/");
   });
  
   $('#info').find('.ui-icon-refresh').on('click', function() {
      location.reload();
   });

  //Включение
  $('.main').hide();
  $('#btnpanel').hide();
});