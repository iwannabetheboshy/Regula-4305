<?php 
  if (!isset($_SESSION))
      session_start();
  if ((!isset($_SESSION['SystemSessionId']))  || ($_SESSION['IdTrainer'] != 6))
  {
    header("Location: ../../ ");
    die();
  }
?>
<html>
  <head>
    	<title>VFRTA</title>
	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
      <link rel="stylesheet" href="../../js/jquery-ui/jquery-ui-theme.css">
	    <link rel="stylesheet" href="../../js/css/themes/default/jquery.mobile-1.4.5.min.css">
      <link rel="stylesheet" href="css/style.css">
      <link rel="stylesheet" href="css/panel.css">
      <link rel="stylesheet" href="css/menu.css">
      <link rel="stylesheet" href="css/sidePanel.css">
      <!-- JS -->
	    <script src="../../js/jquery-ui/external/jquery/jquery.js"></script>
	    <script src="../../js/jquery.mobile-1.4.5.min.js"></script> 
  		<script src="../../js/jquery-ui/jquery-ui.js"></script>
      <script src="../../js/FileSaver.js"></script>
      <script src="../../js/moment.js"></script>
      <script src="js/SidePanel.js"></script>
      <script src="js/main.js"></script>  
      <script src="js/canvas.js"></script>  
  </head>

  <body selectstart="return false" onmousedown="return false">			
		<div data-role="page" class="my-page" id="panel-responsive-page1" data-title="VFRTA - <?= $_SESSION['MachineName'] ?>" data-url="panel-responsive-page1">
			<div data-role="header">
				<h1><?= $_SESSION['MachineName'] ?></h1>
						<a href="#nav-panel" id="menubtn" data-icon="bars" data-iconpos="notext">Menu</a>
						<!-- <a href="#nav-panel2" data-icon="gear" class="ui-btn-right" data-iconpos="notext">Пульт управления</a>  -->
			</div><!-- /header -->

      <div class="ui-corner-all custom-corners" id="info">
          <br>
          <div class="ui-bar ui-bar-a"><h3>Добро пожаловать</h3></div>
          <div class="ui-body ui-body-a"><p>В симулятор работы комплекса: <?= $_SESSION['MachineName'] ?></p>
              <a href="#nav-panel" class="ui-btn ui-btn-inline ui-mini ui-icon-check ui-btn-icon-left">Начать работу</a>
              <a href="#" class="ui-btn ui-btn-inline ui-mini ui-icon-home ui-btn-icon-left" style="display: none;">Вернуться в главное меню</a>
              <a href="#" class="ui-btn ui-btn-inline ui-mini ui-icon-refresh ui-btn-icon-left" style="display: none;">Попробовать еще раз</a>
              <a href="../../tests/" class="ui-btn ui-btn-inline ui-mini ui-icon-carat-d ui-btn-icon-left" style="display: none;" data-ajax="false">Перейти в систему тестирования</a>
          </div>
      </div>

      <div class="loader" style=""></div>
			<div role="main" class="main">
          <div class="header">
            <ul id="nav">
            <li><a href="#">Файл</a>
                <ul>
                  <li class="open" ><span class="step"></span><a href="#">Открыть...</a></li>
                  <li class="save"><span class="step"></span><a href="#">Сохранить</a></li>
                  <li><span class="step"></span><a href="#">Сохранить как...</a></li>
                  <li>-</li>
                  <li><span class="step"></span><a href="#">Удалить</a></li>
                  <li>-</li>
                  <li><span class="step"></span><a href="#">Быстрый просмотр назад</a></li>
                  <li><span class="step"></span><a href="#">Просмотр назад</a></li>
                  <li><span class="step"></span><a href="#">Просмотр вперёд</a></li>
                  <li><span class="step"></span><a href="#">Быстрый просмотр вперёд</a></li>
                  <li>-</li>
                  <li><span class="step"></span><a href="#">Выбор директории</a></li>
                  <li>-</li>
                  <li><span class="step"></span><a href="#">Печать</a></li>
                  <li><span class="step"></span><a href="#">Параметры печати...</a></li>
                  <li>-</li>
                  <li><span class="step"></span><a href="#">Выход</a></li>
                  <li class="off"><span class="step"></span><a href="#">Завершить работу</a></li>
                </ul>
            </li>
            <li>
              <a href="#">Настройки</a>
                <ul>
                  <li class="quality1"><span class="step"></span><a href="#">Режим Обычное качество</a></li>
                  <li class="quality2"><span class="step"></span><a href="#">Режим Улучшенное качество</a></li>
                  <li>-</li>
                  <li><span class="step"></span><a href="#">Параметры процесса регистрации</a></li>
                  <li><span class="step"></span><a href="#">Параметры обработки изображения</a></li>
                  <li><span class="step"></span><a href="#">Параметры окна отображения</a></li>
                  <li>-</li>
                  <li><span class="step"></span><a href="#">Параметры устройства видеоввода</a></li>
                  <li><span class="step"></span><a href="#">Параметры устройства аудиоввода</a></li>
                </ul>
            </li>
              <li>
                <a href="#">Функции</a>
                <ul>
                  <li class="start">
                      <span class="step"></span>
                      <a href="#">Start</a></li>
                  <li class="stop">
                      <span class="step"></span>
                      <a href="#" >Stop</a></li>
                  <li>-</li>
                  <li class="psevdo">
                      <span class="step"></span>
                      <a href="#">Псевдоцвет</a>
                  </li>
                  <li class="relief">
                      <span class="step"></span>
                      <a href="#">Рельеф</a>
                  </li>
                  <li class="filtr">
                      <span class="step"></span>
                      <a href="#">Фильтр</a>
                  </li>
                  <li class="grid">
                      <span class="step"></span>
                      <a href="#">Сетка</a>
                  </li>
                  <li class="negativ">
                      <span class="step"></span>
                      <a href="#">Негатив</a>
                  </li>
                  <li class="loupe">
                      <span class="step"></span>
                      <a href="#">Линза</a>
                  </li>
                  <li class="roi">
                      <span class="step"></span>
                      <a href="#">ROI</a>
                  </li>
                  <li class="contrast">
                      <span class="step"></span>
                      <a href="#">Яркость-Контраст</a>
                  </li>
                </ul>
              </li>
              <li><a href="#">Справка</a>
                <ul>
                  <li>
                      <span class="step"></span>
                      <a href="#">Справка</a>
                  </li>
                  <li>-</li>
                  <li>
                      <span class="step"></span>
                      <a href="#">О программе</a>
                  </li>
                  <li>-</li>
                  <li>
                      <span class="step"></span>
                      <a href="#">Параметры изображения</a>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
          <div class="canvas">
      		  <canvas id='canvas'>Обновите браузер</canvas>
            <canvas id='bufcanvas'>Обновите браузер</canvas>
            <canvas id="roi" width="150" height="120" style="position:absolute; top:0; left:0; display:none"></canvas>
          </div>
          <div class="panel" selectstart="return false" onmousedown="return false">
              <p>Режим процесса регистрации</p>
              <div class="field quality_text">Обычное качество</div>
              <h4>Альбом</h4>
              <div class="ui-grid-a">
                  <div class="ui-block-a">
                      <a href="#" id="img1" class="albom ui-btn btn ui-corner-all">Image 1</a>
                  </div>
                  <div class="ui-block-b"><div class="button-wrap"><button id="img2" class="albom ui-btn btn ui-corner-all">Image 2</button></div></div>
              </div>

              <div class="ui-grid-a">
                  <div class="ui-block-a"><a href="#" id="img3" class="albom ui-btn btn ui-corner-all">Image 3</a></div>
                  <div class="ui-block-b"><div class="button-wrap"><button id="img4" class="albom ui-btn btn ui-corner-all">Image 4</button></div></div>
              </div>
              <a href="#" class="ui-btn ui-corner-all ui-mini">Image Calculator</a>
             <!-- <canvas id="zoom" width="500" height="300" style="position:absolute; top:0; left:0; display:none"></canvas> -->
              
              <div class="zoom_win" style="display: none;">
                <canvas id="zoom" height="160" style="top: 239px;left: 600px; display: none;"></canvas>
                <p>Нажав и удержива нажатой кнопку Ctrl, c помощью стрелок можно изменить Zoom-фактор</p>
              </div>

              <div class="sliders">
                <h4>Фильтр</h4>
                <div id="slider1"></div>
                <h4>Яркость</h4>
                <div id="brigtSlider"></div>
                <h4>Контраст</h4>
                <div id="contrSlider"></div>
              </div>
              
              <div class="curfile">
                <h4>Текущий файл:</h4>
                <div class="field"></div>
              </div>

              <h4>Активная функция:</h4>
              <div class="field status"></div>
              <div class="mobiDick">
                  <div class="top">
                    <div class="pbtn start"></div>
                    <div class="pbtn stop"></div>
                  </div>

                  <div class="middle1">
                    <div class="mbtn quality"></div>
                    <div class="mbtn save"></div>
                    <div class="mbtn open"></div>
                    <div class="mbtn delete"></div>
                  </div>

                  <div class="middle2">
                    <div class="mbtn psevdo"></div>
                    <div class="mbtn relief"></div>
                    <div class="mbtn filtr"></div>
                    <div class="mbtn grid"></div>
                  </div>

                  <div class="middle3">
                    <div class="mbtn negativ"></div>
                    <div class="mbtn loupe">
                    </div>

                    <div class="mbtn roi"></div>
                    <div class="mbtn contrast"></div>
                  </div>

                  <div class="bottom1">
                    <div class="left"></div>
                    <div class="updown">
                      <div class="up"></div>
                      <div class="down"></div>
                    </div>
                    <div class="right"></div>
                  </div>

                  <div class="bottom2">
                    <div class="pbtn okey"></div>
                    <div class="pbtn cancel"></div>
                  </div>
              </div>
          </div>
    	</div>

      <div id="dialog-images" title="Загрузить изображение">
        <ul data-role="listview" id="menu_images" >
          <li class="ui-state-disabled"><div>Specials (n/a)</div></li>
        </ul>
      </div>
	  
	  

      <div id="dialog_start" title="Внимание, излучение!">
        <div class="body">
          <img src="../../images/Sh240/rad.jpg" width="72" height="72" class="radiation">
          <div class="progress-label">Рентгеновский преобразователь включен</div>
        </div>
        <div id="progressbar"></div>
      </div>

		  <div data-role="panel" data-display="overlay" selectstart="return false" onmousedown="return false" data-dismissible="false" data-swipe-close="false" data-theme="a" id="nav-panel">
        <ul data-role="listview">
		   			<li data-icon="back"><a href="#" data-rel="close">Закрыть</a></li>
		   	</ul>
		  </div>
	</div> 

  </body>
</html>
