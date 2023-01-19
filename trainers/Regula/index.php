<?php
	header('Content-type: text/html; charset=utf-8');
	define("AccessToFile",1);
	if (!isset($_SESSION))
    	session_start();
	$_SESSION['SessionId']= session_id();

	if (!isset($_SESSION['SystemSessionId'])) 
	{
		header("Location: ../../ ");
		die();
	}
    
	if (!defined("AccessToFile"))
	{
		die("Access Deny");
	}

	ini_set('display_errors',1);
	error_reporting(E_ALL);

	require_once '..' . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR .'admin' . DIRECTORY_SEPARATOR  . 'settings.php';
    $SQL = "exec [sp_sel_Slides] @IdTrainer = ?";     
    
    $result = $config->QueryWithParams($SQL, [$_SESSION["IdTrainer"]]);

    $_SESSION['SlaidPassed'] = 1;
    
    if (!isset($_GET['isBegin']))
    	$isBegin = 1;
    else
    	$isBegin = $_GET['isBegin'];

    if ($result == null)
    	header("Location: trainer.php");
    //print_r($result[0]);
    //echo json_encode($result);  
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Инструктаж</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="../../js/bootstrap-3.3.5-dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../js/jquery-ui/jquery-ui.css">
  <link rel="stylesheet" href="css/slides.css">

  <script src="../../js/jquery.min.js"></script>
  <script src="../../js/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>

  <script type="text/javascript">
  	$(document).ready(function() {
		var $item = $('.carousel .item'); 
		var $wHeight = $(window).height();
		$item.eq(0).addClass('active');
		$item.height($wHeight); 
		$item.addClass('full-screen');

		$('.carousel img').each(function() {
		  var $src = $(this).attr('src');
		  var $color = $(this).attr('data-color');
		  $(this).parent().css({
		    'background-image' : 'url(' + $src + ')'
		   /* 'background-color' : $color*/
		  });
		  $(this).remove();
		});

		$(window).on('resize', function (){
		  $wHeight = $(window).height();
		  $item.height($wHeight);
		});

		$('.carousel').carousel({
		  interval: false,
		  pause: true
		});

		$('.carousel').on('slide.bs.carousel', function(e){
		    
		    var slideFrom = $(this).find('.active').index();
		    var slideTo = $(e.relatedTarget).index();

		  	if (slideTo == $('.carousel').find('.item').length - 1) {
		  		$('.skipbtn a').text("Продолжить");
		  		$('.skipbtn a').addClass('btn-success').fadeOut(400).fadeIn(400).fadeOut(400).fadeIn(400);
		  		$(".right.carousel-control").hide();
		  	}  else {
		  		$(".right.carousel-control").show();
		  	}
		    //console.log(slideFrom+' => '+ slideTo);
		    if (slideTo == 0) {
		    	$(".left.carousel-control").hide();
		    } else {
		    	$(".left.carousel-control").show();
			}

		});

		$('.skipbtn a').on('click', function() 
		{
			if (<?=$isBegin ?> == 1)
				window.location.href = "trainer.php";
			else
				window.location.href = "../../";
		});
	$(".left.carousel-control").hide();
	});
  </script>
</head>

<body>

<div id="mycarousel" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <?php foreach ($result as $slaid) { ?>
  		<li data-target="#mycarousel" data-slide-to="<?=$slaid['SlidePosition']-1 ?>" class="<?php if ($slaid['SlidePosition'] == 1) echo 'active';  ?>"></li>
  	<?php } ?>
  </ol>
  <div class="carousel-inner" role="listbox">
  	<?php foreach ($result as $slaid) { ?>
    <div class="item">
        <img src="../../images/slaid/<?= $slaid['ImagePath'] ?>"  data-color="lightblue" alt="First Image">
        <div class="carousel-caption">
            <p><?=$slaid['SlideText'] ?></p>
            <!--h3><?=$slaid['SlideCaption'] ?></h3>
            <p><?=$slaid['SlideText'] ?></p-->
        </div>
    </div>
    <?php } ?>
  </div>

  <div class="skipbtn">
  	<a class="btn btn-default">Пропустить</a>
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#mycarousel" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Предыдущий слайд</span>
  </a>
  <a class="right carousel-control" href="#mycarousel" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Следующий слайд</span>
  </a>
</div>

</body>
</html>