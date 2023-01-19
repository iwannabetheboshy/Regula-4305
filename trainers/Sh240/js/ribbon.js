if ( !window.requestAnimationFrame ) {

  window.requestAnimationFrame = ( function() {

    return window.webkitRequestAnimationFrame ||
    window.mozRequestAnimationFrame ||
    window.oRequestAnimationFrame ||
    window.msRequestAnimationFrame ||
    function( /* function FrameRequestCallback */ callback, /* DOMElement Element */ element ) {

      window.setTimeout( callback, 1000 / 60 );

    };

  } )();

}

/* Класс для движения изображений на ленте */
function ScanObj(imgPath) 
{  
  this.x = 0;
  this.imgPath = imgPath;

  this.img = new Image();
  this.img.crossOrigin = 'anonymous';
  this.img.src = imgPath;

  var self = this;
  this.img.onload = function(e)
  {
     var center = canvas.width / 2 - this.width / 2;
     self.x = center;

     /*canvasBuf.width = this.width;
     canvasBuf.height = this.height;

     ctxBuf.drawImage(this, 0, 0);

     var imageData = ctxBuf.getImageData(0, 0, this.width, this.height);
     var data = imageData.data;

      for (var i = 0; i < data.length; i += 4) 
      {
          if (data[i] > 250 && data[i + 1] > 250 && data[i + 2] > 250)
          {  
            data[i + 3] = 0;
            //data[i] = 0;
            //data[i + 1] = 1;
            //data[i + 2] = 2;
          }
      }

     ctxBuf.putImageData(imageData, 0, 0);

     self.img.data = imageData;*/

    // var img = document.createElement("img");
     //img.src = canvasBuf.toDataURL("image/png");

     //var dataURL = canvasBuf.toDataURL('image/png');
     //self.img = img;

     if (ribbon.length == 1)
        init(canvas.width, center, this);
     else
     {  //Тут можно посчитать на сколько далеко выводить изображения, при массовой загрузке
        if (move == 2)
        {
          nextRibbon(canvas.width, center, this);
        } else
        if (move == 1)
        {
          prevRibbon(-this.width, center, this);
        }

        $(".radiation").attr("src", "../../images/6040/rad_active.jpg"); 
        $('.powerrad').addClass('powerrad-active');
        $('.column.state').html("Сканирование");

        setTimeout(function() { 
          $(".radiation").attr("src", "../../images/6040/rad.jpg");
          $('.powerrad').removeClass('powerrad-active');
          $('.column.state').html("Жду Багаж"); 
        } , 2500);

        $('.number').html(ribbon.length);
     }
  }

  this.next = function(i, value) 
  {
     var result = false;
     if (i < (this.x + this.img.width))
     {
        this.x += value;
        result = true;
     }

     /*ctx.strokeStyle = '#f00';
     ctx.lineWidth = 2;*/    

     ctx.drawImage(this.img, this.x, 0, this.img.width, canvas.height);
     //ctx.strokeRect(this.x, 0, this.img.width, canvas.height);
     return result;
  };

  this.prev = function(i, value, imgwidth) 
  {
    var result = false;
    if (i > this.x - imgwidth)
    {   
      this.x += value; 
      result = true;
    }

    /*ctx.strokeStyle = '#f00';
    ctx.lineWidth = 2;*/    

    ctx.drawImage(this.img, this.x, 0, this.img.width, canvas.height);
    //ctx.strokeRect(this.x, 0, this.img.width, canvas.height);

    return result;
  };

  this.move = function(value) 
  {
     this.x += value; 

     /*ctx.strokeStyle = '#f00';
     ctx.lineWidth = 2;*/    

     if (this.x > - this.img.width) //Уменьшаем загрузку цп
     {   
        ctx.drawImage(this.img, this.x, 0, this.img.width, canvas.height);
        //ctx.strokeRect(this.x, 0, this.img.width, canvas.height);
     }
  }

  this.start = function() 
  {
     var center = canvas.width / 2 - self.img.width / 2;

     setTimeout(function() {
          nextRibbon(canvas.width, center, self.img);
       }, 3000);
  }
};

//Функции для работы с лентой
function startMoveRibbon(imgPath)
{
  loadmode = 0;
  ribbon.push(new ScanObj("../../images/scans/" + imgPath));
  //ribbon[ribbon.length-1].start();
}

function init(i, center, image)
{
    //console.log(i + ' ' + (canvas.width / 2 - image.width / 2));
    if (!(i < center))
    {
      ctx.fillRect(0, 0, canvas.width, canvas.height);

      /*ctx.strokeStyle = '#f00';  // some color/style
      ctx.lineWidth = 2;*/    

      var height = canvas.height;
      //if (image.height < height)
        //height = image.height;

      //var y = canvas.height / 2 - height / 2;

      ctx.drawImage(image, i, 0, image.width, height);
      //ctx.strokeRect(i, 0, image.width, canvas.height);
      i = i - speed;

      window.requestAnimationFrame(function() {
          init(i, center, image);
      });
    }
}

function nextRibbon(i, center, image) //Лента движется вправо
{   //Задача сдвинуть изображения и довести своё до центра 
    if (!(i < center) && move == 2)
    {
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      var moved = ribbon[ribbon.length-2].next(i, -speed);

      //Если двигаем предпоследнее то, двигаем все остальные
       if (ribbon.length > 2)
       {
          for (var j = ribbon.length-3; j > -1; j--)
          {
             if (moved)
              ribbon[j].move(-speed);
             else
              ribbon[j].move(0);
          }
       }

      /*ctx.strokeStyle = '#f00';  // some color/style
      ctx.lineWidth = 2;*/

      ctx.drawImage(image, i, 0, image.width, canvas.height);
      //ctx.strokeRect(i, 0, image.width, canvas.height);

     /* ctx.strokeStyle = "#F00";
      ctx.font = "italic 30pt Arial";
      ctx.strokeText("i: " + i, 1700, 100);*/

      i = i - speed;
    
      window.requestAnimationFrame(function() {
          nextRibbon(i, center, image);
      });
    }
}

function prevRibbon(i, center, image) //Лента движется влево
{   //Задача сдвинуть изображения и довести своё до центра 
    console.log(i + " " + center);

    if ((i < center) && move == 1)
    {
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      var moved = ribbon[ribbon.length-2].prev(i, speed, image.width);

      //Если двигаем предпоследнее то, двигаем все остальные
       if (ribbon.length > 2)
       {
          for (var j = ribbon.length-3; j > -1; j--)
          {
             if (moved)
              ribbon[j].move(speed);
             else
              ribbon[j].move(0);
          }
       }

      /*ctx.strokeStyle = '#f00';  // some color/style
      ctx.lineWidth = 2;*/

      ctx.drawImage(image, i, 0, image.width, canvas.height);
      //ctx.strokeRect(i, 0, image.width, canvas.height);

      i = i + speed;
    
      window.requestAnimationFrame(function() {
          prevRibbon(i, center, image);
      });
    }
}

function moveRibbon(value) //Лента движется
{ 
  if (move == 2 || move == 1)
  {
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    for (var i = ribbon.length-1; i > -1; i--) 
    {
        ribbon[i].move(value);
    }
  
    window.requestAnimationFrame(function() {
        moveRibbon(value);
    });
  }
}

function reloadRibbon()
{ 
  ctx.fillRect(0, 0, canvas.width, canvas.height);
  for (var i = ribbon.length-1; i > -1; i--)  
  {
      ribbon[i].move(0);
  }
}