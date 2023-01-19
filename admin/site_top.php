<div class="jumbotron text-center">
  <h1 id="HeaderName">Панель Администратора</h1>
  <p>Российская таможенная академия</p>
  <style type="text/css">
  #trainerName { 
              background-color: #fff;
              border: 1px solid transparent;
              border-radius: 4px;
              -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
              box-shadow: 0 1px 1px rgba(0,0,0,.05);
              max-width: 200px;
              margin: auto;
              display: none;
              margin-bottom: 0px;
            }
  </style>

  <div id="trainerName">
      <p></p>
  </div>

  <script type="text/javascript">
      if (window.location.pathname != '/admin/')
      {
        if (sessionStorage.trainerName !== undefined)
        {
          $('#trainerName').show();
          $('#trainerName').html(sessionStorage.trainerName);
        }
      }
  </script>
 
  <div class="col-md-2">
      <a href ="/" class = "btn btn-default" >
      <span class = "glyphicon glyphicon-flash"></span> Переход в систему тренажёров</a>
  </div>

  <div class="col-md-2 col-md-offset-7" style="padding: 6px; text-align: right;">Вошёл как " <?=$_SESSION['UserName']?> "</div> 

  <div class="col-md-1">
      <a href = "/admin/auth.php?logout" class="logout btn btn-default" >
      <span class="glyphicon glyphicon-log-out"></span> Выйти</a>
  </div>  
</div>