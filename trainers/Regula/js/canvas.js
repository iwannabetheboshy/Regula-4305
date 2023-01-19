function imgProcessing() 
{ 
    //Инициализация
    canvas = document.getElementById("canvas");
    ctx = canvas.getContext('2d');
    //Инициализация текущих размеров
    canvas.width  = window.innerWidth - $('.panel').width();
    canvas.height = window.innerHeight - ($('.header').height() + $('.ui-header').height());

    //Буферная
    var bcanvas = document.getElementById("bufcanvas");
    var bCtx = bcanvas.getContext("2d");

    //Канва для зуммирования
    var zoom = document.getElementById("zoom");
    var zoomCtx = zoom.getContext("2d");
    var zoom_scale = 1.4;
    var zoomX = 0;
    var zoomY = 0;
    var zoomInterval = null;
    var zoomMouseDown = null;

    var roicanvas = document.getElementById("roi");
    var roiCtx = roicanvas.getContext("2d");
    var roiInterval = null;

    //Переменные локальные
    this.zoomMode = 0; //1 лупа 0 выкл
    this.gridMode = false; //Сетка
    this.contrMode = false; //Яркость контраст
    this.reliefMode = false; //Тиснение
    this.roiMode = false; //ROI
    this.negativMode = false;
    this.filtrMode = false; //Размытие и четкость
    this.pcevdoMode = false;
    this.animate = true;

    var gridX = 50;
    var gridY = 50;
    
    ctx.fillStyle = "#ffffff";
    ctx.fillRect(0, 0, canvas.width, canvas.height);
   
    //Глобальное
    this.img = new Image();
    this.img.crossOrigin = 'anonymous'; 

    var self = this;
    this.img.onload = function()
    {
       self.animate = false;

       ctx.fillStyle = "#ffffff";
       ctx.fillRect(0, 0, canvas.width, canvas.height);

       //pos = canvas.width /2 - canvas.height / 2;
       pos = canvas.width /2 - this.width / 2;
       ctx.drawImage(this, pos, 0, this.width, canvas.height);

      /* var gradient = ctx.createRadialGradient(150, 150, 0, 150, 150, 150);
       gradient.addColorStop(0, 'rgba(255,255,255,0)');
       gradient.addColorStop(1, 'rgba(255,255,255,1)');
       ctx.fillStyle = gradient;
       ctx.fillRect(0, 0, 300, 300);*/

       //Скрываем лоайдер
       $.mobile.loading( "hide" );
    }

    //Инициализация изображения 
    var startimg = new Image();
    startimg.crossOrigin = 'anonymous'; 

    startimg.src = "../../images/slaid/61.jpg";
    $(".curfile .field").html("1.jpg"); 

    startimg.onload = function()
    {
       ctx.fillStyle = "#ffffff";
       init(32, this);
    }

    function init(i, image)
    {
        if (!self.animate)
          return false;

        ctx.fillRect(0, 0, canvas.width, canvas.height);   

        i++;
        if (i > 1000)
          i = 0;

        var p = canvas.width /2 - image.width / 4;
        var step = i / 10;

        for(var x = 0; x < image.width; x++)
        {
          var y = 10 * Math.sin(x/24 + step) + canvas.height / 2 - 250;
          ctx.drawImage(image, x * 2, 0, 1, image.height, x + p, y, 1, 500); //canvas.height - 10); //зависимость от 1-ого х влияет на размер 
        }

        window.requestAnimationFrame(function() {
          init(i, image);
        });
    }

    this.resizeCanvas = function()
    {
        console.log(window.innerWidth + " " + $('.panel').width())
        canvas.width  = window.innerWidth - $('.panel').width();
        canvas.height = window.innerHeight - ($('.header').height() + $('.ui-header').height());
        
        //pos = canvas.width /2 - canvas.height / 2;
        pos = canvas.width /2 - self.img.width / 2;
        self.clear();
    }

   this.clear = function()
   {
      ctx.fillStyle = "#ffffff";
      ctx.fillRect(0, 0, canvas.width, canvas.height);

      ctx.drawImage(self.img, pos, 0, self.img.width, canvas.height);
      //ctx.drawImage(this.img, pos, 0, canvas.height, canvas.height);
   }

   this.negativ = function() 
   {      	
  		this.negativMode = !this.negativMode;

      var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      var data = imageData.data;

      for(var i = 0; i < data.length; i += 4) {
        data[i] = 255 - data[i];
        data[i + 1] = 255 - data[i + 1];
        data[i + 2] = 255 - data[i + 2];
      }

      ctx.fillRect(0, 0, canvas.width, canvas.height);
      ctx.putImageData(imageData, 0, 0);
      return this.negativMode;
   }

  this.whiteblack = function() 
  {
      var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      var data = imageData.data;

      if (!black)
      {
          ctxBuf.putImageData(imageData, 0, 0);

          for(var i = 0; i < data.length; i += 4) 
          {
            //var gray = parseInt((data[i] + data[i + 1] + data[i + 2]) / 3);
            // red
            /*data[i] = gray;
            // green
            data[i + 1] = gray;
            // blue
            data[i + 2] = gray;*/

            var r = data[i];
            var g = data[i + 1];
            var b = data[i + 2];
            // The human eye is bad at seeing red and blue, so we de-emphasize them.

            var v = 0.2126*r + 0.7152*g + 0.0722*b;
            data[i] = data[i+1] = data[i+2] = v
          }

          // overwrite original image
          ctx.fillRect(0, 0, canvas.width, canvas.height);
          ctx.putImageData(imageData, 0, 0);
      }
        // clear();

      black = !black;
      return black;
   }

  this.brightness = function (adjustment) 
  {
    var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    var data = imageData.data;
    
    for (var i=0; i< data.length; i+=4) 
    {
        data[i] += adjustment;
        data[i + 1] += adjustment;
        data[i + 2] += adjustment;
    }

    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.putImageData(imageData, 0, 0);
  }

  this.contrast = function(adjustment) 
   {
      var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      var data = imageData.data;

      var factor = (259 * (adjustment + 255)) / (255 * (259 - adjustment));

      for (var i = 0; i < data.length; i += 4) 
      {
          data[i] = factor * (data[i] - 128) + 128;
          data[i + 1] = factor * (data[i + 1] - 128) + 128;
          data[i + 2] = factor * (data[i + 2] - 128) + 128;
      }

      ctx.fillRect(0, 0, canvas.width, canvas.height);
      ctx.putImageData(imageData, 0, 0);
   }

   this.contrBright = function(contrast, brightness) 
   {
      var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      var data = imageData.data;

      contrast = (contrast / 100) + 1;  //convert to decimal & shift range: [0..2]
      var intercept = 128 * (1 - contrast);

      for (var i = 0; i < data.length; i += 4) 
      {
          data[i] = (data[i] * contrast + intercept) + brightness;;
          data[i + 1] = (data[i + 1] * contrast + intercept) + brightness;
          data[i + 2] = (data[i + 2] * contrast + intercept) + brightness;
      }

      ctx.fillRect(0, 0, canvas.width, canvas.height);
      ctx.putImageData(imageData, 0, 0);
   }

   this.grid = function(sizeX, sizeY, init) 
   {
      if (init)
      {
        gridX = sizeX;
        gridY = sizeY;
        this.gridMode = !this.gridMode;
      } else
      { 
        this.clear();

        if (gridX + sizeX > 5)
          gridX = gridX + sizeX;
        
        if (gridY + sizeY > 5)
          gridY = gridY + sizeY;
      }

      if (this.gridMode)
      {
        var p = 0; //padding
        var bw = canvas.width;
        var bh = canvas.height;

        ctx.beginPath();

        for (var x = 0; x <= bw; x += gridX) {
          ctx.moveTo(0.5 + x + p, p);
          ctx.lineTo(0.5 + x + p, bh + p);
        }

        for (var x = 0; x <= bh; x += gridY) {
            ctx.moveTo(p, 0.5 + x + p);
            ctx.lineTo(bw + p, 0.5 + x + p);
        }

        ctx.lineWidth = '2';
        ctx.strokeStyle = "white";
        ctx.stroke();
      } else
        this.clear();

      return {x: gridX, y: gridY};
   }

  this.convolute = function(weights) //Свёрстка
   {
      // Низкочастотная и высокочастоная фильтрация
      var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      var data = imageData.data;

      var side = Math.round(Math.sqrt(weights.length));
      var halfSide = Math.floor(side/2);

      var src = imageData.data;
      var sw = imageData.width;
      var sh = imageData.height;
      // pad output by the convolution matrix
      var w = sw;
      var h = sh;
      var output = ctx.createImageData(w, h);
      var dst = output.data;
      // go through the destination image pixels
      var alphaFac = 1 ? 1 : 0;
      for (var y=0; y<h; y++) {
        for (var x=0; x<w; x++) {
          var sy = y;
          var sx = x;
          var dstOff = (y*w+x)*4;
          // calculate the weighed sum of the source image pixels that
          // fall under the convolution matrix
          var r = 0, g = 0, b = 0, a = 0;
          for (var cy=0; cy<side; cy++) {
            for (var cx=0; cx<side; cx++) {
              var scy = sy + cy - halfSide;
              var scx = sx + cx - halfSide;
              if (scy >= 0 && scy < sh && scx >= 0 && scx < sw) {
                var srcOff = (scy*sw+scx)*4;
                var wt = weights[cy*side+cx];
                r += src[srcOff] * wt;
                g += src[srcOff+1] * wt;
                b += src[srcOff+2] * wt;
                a += src[srcOff+3] * wt;
              }
            }
          }
          dst[dstOff] = r;
          dst[dstOff+1] = g;
          dst[dstOff+2] = b;
          dst[dstOff+3] = a + alphaFac*(255-a);
        }
      }
      
      ctx.putImageData(output, 0, 0);
   }

   this.pcevdocolor = function(mode) 
   {
      if (mode == 0)
      {
        this.pcevdoMode = !this.pcevdoMode;

        if (!this.pcevdoMode)
        {
          this.clear();
          return this.pcevdoMode;
        }
      }

      var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      var data = imageData.data;

      var hslValue = { h: 0, s: 0, l: 0 };
      var rgbValue = { r: 0, g: 0, b: 0 };

      var w = imageData.width;
      var h = imageData.height;

      //Получаем диапозон
      //min, max
      var min = 255, max = 0;

      for (var i=0; i< data.length; i+=4) 
      {
          var gray = (data[i] + data[i+1] + data[i+2]) / 3;

          if (gray < min)
            min = gray;
          
          if (gray > max)
            max = gray;
      }

     // console.log(min + " " + max);

      var k = (h / w);
      var k1 = 0;

      for (var x=0; x < w; x++) 
      {
        for (var y=0; y < h; y++) 
        {
          var dstOff = (y*w + x)*4;
          var gr = (data[dstOff] + data[dstOff+1] + data[dstOff+2]) / 3;

          rgbToHsl(data[dstOff], data[dstOff + 1], data[dstOff + 2], hslValue);

          var k2 = ((gr - min) / (max - min));

          if (y == Math.round(x * k) && y < h / 2)
          {
            //k1 = y / (h / 2);
           // console.log(y / (h / 2));
          }

          hslValue.h = hslValue.h + k2; //+ 0.2 - y/h + 0.2; //  
          hslValue.s = hslValue.s + 0.7;

          hslValue.l = k2;

          //hslValue.l = hslValue.l - (1 - k1);
          
          //if (hslValue.l > 0.9)
            //hslValue.l = 0.7 - k2;

        /*  if (k2 > 0.9)
            hslValue.l = 1 - hslValue.l;*/

          hslToRgb(hslValue.h, hslValue.s, hslValue.l, rgbValue);

          data[dstOff] = rgbValue.r;
          data[dstOff + 1] = rgbValue.g;
          data[dstOff + 2] = rgbValue.b;

        }
      }

      ctx.fillRect(0, 0, canvas.width, canvas.height);
      ctx.putImageData(imageData, 0, 0);

      return this.pcevdoMode;      
   };

   this.enableRoi = function()
   {
      if (this.roiMode)
      {
          $('#roi').hide();
          this.roiMode = false;
          clearInterval(roiInterval);
          return this.roiMode; 
      }

      if (!$('#roi').is('visible'))
      {
        this.roiMode = true;
        //Устанавливаем посередине
        var x = canvas.width / 2 - $('#roi').width() / 2;
        var y = canvas.height / 2 - $('#roi').width() / 2 - 50;

        $('#roi').css('top', y);
        $('#roi').css('left', x);

        roiInterval = setInterval(this.roi, 100);
        $('#roi').show();

        return this.roiMode;
      }
   }

   this.roi = function() 
   {
      roiCtx.fillStyle = "white";
      //roiCtx.fillRect(0,0, roicanvas.width, roicanvas.height);

      var left = parseInt(roicanvas.style.left);
      var top = parseInt(roicanvas.style.top);

      var imageData = ctx.getImageData(left, top, roicanvas.width, roicanvas.height);
      var data = imageData.data;

      var w = imageData.width;
      var h = imageData.height;

      var output = ctx.createImageData(w, h);
      var dst = output.data;

      //Получаем диапозон
      //min, max
      var min = 255, max = 0;

      for (var i=0; i< data.length; i+=4) 
      {
          var gray = (data[i] + data[i+1] + data[i+2]) / 3;

          if (gray < min)
            min = gray;
          
          if (gray > max)
            max = gray;
      }

      var average = ((max - min) / 2) + min;
      //console.log(min + " " + max + " " + average);

      for (var y=0; y < h; y++) 
      {
        for (var x=0; x < w; x++) 
        {
          var dstOff = (y*w + x)*4;
          var gr = (data[dstOff] + data[dstOff+1] + data[dstOff+2]) / 3;

          var k = ((gr - min) / (max - min)) * 255;

          dst[dstOff] = k;
          dst[dstOff+1] = k;
          dst[dstOff+2] = k;
          dst[dstOff+3] = 255;
        }
      }

      roiCtx.putImageData(output, 0, 0);      
   };

   this.filtr = function(mode) 
   {
      if (mode == 0)
      {
        this.filtrMode = !this.filtrMode;
        return this.filtrMode;
      }

      // Низкочастотная и высокочастоная фильтрация
      var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      var data = imageData.data;

      var weights;

      //Низко
      if (mode == 1)
        weights = [ 1/9, 1/9, 1/9,
                  1/9, 1/9, 1/9,
                  1/9, 1/9, 1/9];

      //Высоко
      if (mode == 2)
        weights = [  0, -1,  0,
                  -1,  5, -1,
                   0, -1,  0 ];

      var side = Math.round(Math.sqrt(weights.length));
      var halfSide = Math.floor(side/2);

      var src = imageData.data;
      var sw = imageData.width;
      var sh = imageData.height;
      // pad output by the convolution matrix
      var w = sw;
      var h = sh;
      var output = ctx.createImageData(w, h);
      var dst = output.data;
      // go through the destination image pixels
      var alphaFac = 1 ? 1 : 0;
      for (var y=0; y<h; y++) {
        for (var x=0; x<w; x++) {
          var sy = y;
          var sx = x;
          var dstOff = (y*w+x)*4;
          // calculate the weighed sum of the source image pixels that
          // fall under the convolution matrix
          var r=0, g=0, b=0, a=0;
          for (var cy=0; cy<side; cy++) {
            for (var cx=0; cx<side; cx++) {
              var scy = sy + cy - halfSide;
              var scx = sx + cx - halfSide;
              if (scy >= 0 && scy < sh && scx >= 0 && scx < sw) {
                var srcOff = (scy*sw+scx)*4;
                var wt = weights[cy*side+cx];
                r += src[srcOff] * wt;
                g += src[srcOff+1] * wt;
                b += src[srcOff+2] * wt;
                a += src[srcOff+3] * wt;
              }
            }
          }
          dst[dstOff] = r;
          dst[dstOff+1] = g;
          dst[dstOff+2] = b;
          dst[dstOff+3] = a + alphaFac*(255-a);
        }
      }
      
      ctx.putImageData(output, 0, 0);
      return this.filtrMode;
   }

   this.relief = function () //Тиснение
   {  //Немного модифицированная свертка
      this.reliefMode = !this.reliefMode;

      if (!this.reliefMode)
      {  
        this.clear();
        return this.reliefMode;
      }

      var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      var data = imageData.data;

      var weights = [ 0, 1,  0,
                    1,  0, -1,
                    0, -1,  0 ];

      var side = Math.round(Math.sqrt(weights.length));
      var halfSide = Math.floor(side / 2 );

      var src = imageData.data;
      var sw = imageData.width;
      var sh = imageData.height;
      // pad output by the convolution matrix
      var w = sw;
      var h = sh;
      var output = ctx.createImageData(w, h);
      var dst = output.data;
      // go through the destination image pixels
      var alphaFac = 1 ? 1 : 0;
      for (var y=0; y<h; y++) {
        for (var x=0; x<w; x++) {
          var sy = y;
          var sx = x;
          var dstOff = (y * w + x) * 4;
          // calculate the weighed sum of the source image pixels that
          // fall under the convolution matrix
          var r=0, g=0, b=0, a=0;
          for (var cy=0; cy<side; cy++) {

            for (var cx=0; cx<side; cx++) {

              var scy = sy + cy - halfSide;
              var scx = sx + cx - halfSide;

              if (scy >= 0 && scy < sh && scx >= 0 && scx < sw) 
              {
                var srcOff = (scy*sw+scx)*4;
                var wt = weights[cy*side+cx];


                r += src[srcOff] * wt;
                g += src[srcOff+1] * wt;
                b += src[srcOff+2] * wt;
                a += src[srcOff+3] * wt;
              }
            }
          }
          dst[dstOff] = 128 + r;
          dst[dstOff+1] = 128 + g;
          dst[dstOff+2] = 128 + b;
          dst[dstOff+3] = a + alphaFac*(255-a);
        }
      }
      
      ctx.putImageData(output, 0, 0);

      return this.reliefMode;
   }

   this.roiMove = function (number) 
   {
       //console.log(number);
        if (number.hasClass('up'))
           $('#roi').css('top', (parseInt(roicanvas.style.top) - 10));

        if (number.hasClass('left'))
            roicanvas.style.left = (parseInt(roicanvas.style.left) - 10) + "px";

        if (number.hasClass('right'))
          roicanvas.style.left = (parseInt(roicanvas.style.left) + 10) + "px";

        if (number.hasClass('down')) 
          roicanvas.style.top = (parseInt(roicanvas.style.top) + 10) + "px";

        this.roi();
   }

   this.loupeMove = function (number) 
   {
       //console.log(number);
        if (number.hasClass('zoom'))
        {
          if ((number.hasClass('up') || number.hasClass('right')) && zoom_scale < 5) {
            zoom_scale = zoom_scale + 0.2;
          }

          if ((number.hasClass('down') || number.hasClass('left'))  && zoom_scale > 1) {
            zoom_scale = zoom_scale - 0.2;

            if ((zoomX + canvas.width /zoom_scale) > canvas.width)
              zoomX = canvas.width - (canvas.width /zoom_scale);
            if ((zoomY + canvas.height /zoom_scale) > canvas.height)
              zoomY = canvas.height - (canvas.height /zoom_scale);
          }
        } else
        {
          if (number.hasClass('up') && zoomY > 0)
             zoomY = zoomY - 10;
             //zoom.style.top = (parseInt(zoom.style.top) - 10) + "px";

          if (number.hasClass('left') && zoomX > 0)
             zoomX = zoomX - 10;
             // zoom.style.left = (parseInt(zoom.style.left) - 10) + "px";

          if (number.hasClass('right') && (zoomX + canvas.width /zoom_scale)  < canvas.width)
            zoomX = zoomX + 10;
            //zoom.style.left = (parseInt(zoom.style.left) + 10) + "px";

          if (number.hasClass('down') && (zoomY + canvas.height /zoom_scale) < canvas.height) 
            zoomY = zoomY + 10;
            //zoom.style.top = (parseInt(zoom.style.top) + 10) + "px";
        }
        this.loupePro();
   }

    this.loupePro = function () 
    {
        zoomCtx.fillStyle = "white";
        zoomCtx.fillRect(0,0, zoom.width, zoom.height);

        var zw = canvas.width / zoom_scale;
        var zh = canvas.height / zoom_scale; 

        //коффициенты
        var kh = zoom.height / canvas.height; 
        var kw = zoom.width / canvas.width;

        zoomCtx.drawImage(bcanvas, 0, 0, zoom.width, zoom.height);

        zoomCtx.lineWidth = '1';
        zoomCtx.strokeStyle = "red";

        zoomCtx.strokeRect(zoomX * kh, zoomY * kw, zoom.width / zoom_scale, zoom.height / zoom_scale);

        ctx.drawImage(bcanvas, zoomX, zoomY, zw, zh, 0, 0, canvas.width, canvas.height);
    }

    this.enableLoop = function() 
    {  
      if (this.zoomMode == 1)
      {
          $('#zoom').hide();
          zoom_scale = 1.4;
          this.zoomMode = 0;
          clearInterval(zoomInterval);
          this.clear();
          return this.zoomMode; 
      }

      if (zoom.style.display != "block")
      {
        this.zoomMode = 1;
        //Копируем в буфер
        bcanvas.height = canvas.height;
        bcanvas.width = canvas.width;

        var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        bCtx.putImageData(imageData, 0, 0);

        zoomInterval = setInterval(this.loupePro, 100);
        zoom.style.display = "block";

        return this.zoomMode;
      }
    }

    function rgbToHsl(R, G, B, res) 
    {
      var var_R = (R / 255); //RGB from 0 to 255
      var var_G = (G / 255);
      var var_B = (B / 255);

      var H = 0,
          S = 0,
          L = 0;

      var var_Min = Math.min(var_R, var_G, var_B); //Min. value of RGB
      var var_Max = Math.max(var_R, var_G, var_B); //Max. value of RGB
      var del_Max = var_Max - var_Min; //Delta RGB value

      L = (var_Max + var_Min) / 2;

      if (del_Max == 0) { //This is a gray, no chroma...
          H = 0; //HSL results from 0 to 1
          S = 0;
      } else { //Chromatic data...    
          S = (L < 0.5) ? del_Max / (var_Max + var_Min) : del_Max / (2 - var_Max - var_Min);


          var del_R = (((var_Max - var_R) / 6) + (del_Max / 2)) / del_Max;
          var del_G = (((var_Max - var_G) / 6) + (del_Max / 2)) / del_Max;
          var del_B = (((var_Max - var_B) / 6) + (del_Max / 2)) / del_Max;

          if (var_R == var_Max) {
              H = del_B - del_G;
          } else if (var_G == var_Max) {
              H = (1 / 3) + del_R - del_B;
          } else if (var_B == var_Max) {
              H = (2 / 3) + del_G - del_R;
          }

          if (H < 0) H += 1;
          if (H > 1) H -= 1;
      }
      res.h = H;
      res.s = S;
      res.l = L;
    }

    // source http://www.easyrgb.com/index.php?X=MATH&H=19#text19
    function hslToRgb(H, S, L, res) {
        var R = 0,
            G = 0,
            B = 0;
        var var_1 = 0,
            var_2 = 0;

        if (S == 0) { //HSL from 0 to 1
            R = L * 255; //RGB results from 0 to 255
            G = L * 255;
            B = L * 255;
        } else {
            var_2 = (L < 0.5) ? L * (1 + S) : (L + S) - (S * L);
            var_1 = 2 * L - var_2

            R = 255 * Hue_2_RGB(var_1, var_2, H + (1 / 3));
            G = 255 * Hue_2_RGB(var_1, var_2, H);
            B = 255 * Hue_2_RGB(var_1, var_2, H - (1 / 3));
        }
        res.r = R;
        res.g = G;
        res.b = B;
    }

    function Hue_2_RGB(v1, v2, vH) { //Function Hue_2_RGB

        if (vH < 0) vH += 1;
        if (vH > 1) vH -= 1;
        if ((6 * vH) < 1) return (v1 + (v2 - v1) * 6 * vH);
        if ((2 * vH) < 1) return (v2);
        if ((3 * vH) < 2) return (v1 + (v2 - v1) * ((2 / 3) - vH) * 6);
        return (v1);
    }

    function RGBToHex(r, g, b) {
        r = 0 | r;
        g = 0 | g;
        b = 0 | b;
        var bin = r << 16 | g << 8 | b;
        bin = bin.toString(16);
        while (bin.length < 6) bin = "0" + bin;
        return "#" + bin;
    }

};