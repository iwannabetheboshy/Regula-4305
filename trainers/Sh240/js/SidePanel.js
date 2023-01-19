/*В данном тренажере некорректно использовать только 1 переменную состояний, т.к. в таком случае состояний получается чрезмерно много. Поэтому используется 4 переменных*/
var State = 0; /*общее состояние :
    0 - выключен, 
    1 - включен без возможности снятия новых рентгеновских снимков,
    3 - включено в полнофункциональном режиме
    4 - аварийное отключение
    5 - корректное выключение*/
var NotebookState = 0; /*состояния ноутбука:
    0 - упакован
    1 - чемодан
    2 - чемодан открыт
    3 - открыт ноутбук
    4 - включен ноутбук
    5 - выключен ноутбук*/
var ReseiverState = 0 /* состояние приемника:
    0 - упакован
    1 - распакован
    2 - установлен на штатив*/
var SenderState = 0 /*состояние излучателя:
    0 - упакован
    1 - распакован
    2 - установлен на штатив
    3 - вставлен ключ для включения
    4 - повернут ключ - готов к работе
    5 - повернут ключ при выключении
    6 - извлечен ключ для выключения
    примечание: из 6 можно снова перейти в 3*/
/*
порядок корректного включения:
1) распаковка
2) установка на штативы
3) открываем чемодан + устанавливаем провода
4) включаем ноутбук
5) вставляем ключ в излучатель и поворачиваем его
6) комплекс готов к работе

порядок корректного выключения (строгий): 
1) достать ключ из излучателя
2) выключить ноутбук
3) комплекс корректно выключен

*/

function LogStudentActions (IdAction, IdImage = null, ImagePath = null) {
            $.ajax({
                type: "POST",
                url: "actions.php",
                data: {
                    type: 4,
                    "data": JSON.stringify({"IdAction": IdAction,
                    "IdImage": IdImage, 
                    "ImagePath": ImagePath})
                },
                cache: false,
                async: true,
                dataType: "json"
            });
        }

function LogStudentClassActions ( obj) {
    if ($(obj).hasClass("left") )
        LogStudentActions(143); 
    else if ($(obj).hasClass("right") )
        LogStudentActions(144);
    else if ($(obj).hasClass("up") )
        LogStudentActions(142);
    else if ($(obj).hasClass("down") )
        LogStudentActions(141);
    console.log(obj);
}        

function bindDragIvent(obj) {
    $(obj).mousedown(function(e) {
        var dragElement = e.target;
        var coords, shiftX, shiftY;
        startDrag(e.clientX, e.clientY);
        document.onmousemove = function(e) {
            moveAt(e.clientX, e.clientY);
        };
        dragElement.onmouseup = function() {
            finishDrag();
        };
        document.mouseleave = function() {
            finishDrag();
            console.log(123);
        };
        // -------------------------
        function startDrag(clientX, clientY) {
            shiftX = clientX - dragElement.getBoundingClientRect().left;
            shiftY = clientY - dragElement.getBoundingClientRect().top;
            $(dragElement).zIndex(2000);
            //dragElement.style.position = 'fixed';
            //document.body.appendChild(dragElement);   
            moveAt(clientX, clientY);
        }
        ;function finishDrag() {
            // конец переноса, перейти от fixed к absolute-координатам
            dragElement.style.top = parseInt(dragElement.style.top) + pageYOffset + 'px';
            //    dragElement.style.position = 'absolute';
            document.onmousemove = null ;
            dragElement.onmouseup = null ;

            $(dragElement).zIndex(1002);
            //$(dragElement).data().parent.checkInTrainer($(dragElement));
            $(dragElement).parent().append(dragElement);
        }
        function moveAt(clientX, clientY) {
            //if (State == 4)
            {
                // новые координаты
                var newX = clientX - shiftX;
                var newY = clientY - shiftY;
                // ------- обработаем вынос за нижнюю границу окна ------
                // новая нижняя граница элемента
                var newBottom = newY + dragElement.offsetHeight;
                // ------- обработаем вынос за верхнюю границу окна ------
                if (newY < 45)
                    newY = Math.max(newY, 45);
                if (newY > ($(dragElement).parent().height() + dragElement.offsetHeight))
                    newY = ($(dragElement).parent().height() - dragElement.offsetHeight);
                // зажать в границах экрана по горизонтали
                if (newX < 0)
                    newX = 0;
                if (newX > $('#nav-panel').width() - dragElement.offsetWidth) {
                    newX = $('#nav-panel').width() - dragElement.offsetWidth;
                }
                dragElement.style.left = newX + 'px';
                dragElement.style.top = newY + 'px';
            }
        }
        // отменим действие по умолчанию на mousedown (выделение текста, оно лишнее)
        return false;
    });
}

function CheckPowerOn() {
    if (NotebookState == 4)
        State = 1;
    if (NotebookState == 4 && ReseiverState==2 && SenderState==4)
        State = 3;
}

function CheckPowerOff() {
    if (NotebookState == 5 && (SenderState == 6 || SenderState == 1 || SenderState == 2)) 
    {
        State = 5;
        $('#nav-panel').panel("close");
        reset(State);
    }
}


function LuggageImg(ImagePath,IdImage) {
    this.ImagePath = ImagePath;
    this.IdImage = IdImage;
}


function SidePanel(parent) {   
    this.parent = parent;
    this.CurrentImage = null;
    this.CurrentImbObj = null;

    this.ClosedBumblebee = $('<img id="ClosedBumblebee" class="PnlClosedBumblebee" src="..\\..\\images\\Sh240\\BumbleBeeCasesPacked.png" title="Нажмите, чтобы распаковать ШМЕЛЬ-240ТВ">').
                                    appendTo(parent).data({"parent": this}).click(function() { $(this).data().parent.unpack(); LogStudentActions(105); State = 0; NotebookState = 1; ReseiverState = 1; SenderState = 1; });

    /*Ноутбук*/                                    
    this.BumblebeeNotebookCaseClosed = $('<img id="BumbleBeeNotebookCaseClosed"  src="..\\..\\images\\Sh240\\BumbleBeeNotebookCaseClosed.png" title="Нажмите, открыть чемодан с ноутбуком">').
                                    appendTo(parent).hide().data({"parent": this}).click(function(){ $(this).data().parent.openCase(); LogStudentActions(106); NotebookState = 2;});
    this.BumblebeeNotebookClosedCaseOpen = $('<img id="BumblebeeNotebookClosedCaseOpen" class="notebook" src="..\\..\\images\\Sh240\\BumbleBeeCaseOpen.png" title="Нажмите, открыть ноутбук">').
                                    appendTo(parent).hide().data({"parent": this}).click(function(){ $(this).data().parent.openNotebook(); LogStudentActions(107); NotebookState = 3;});
    this.BumbleBeeNoteBookOpen = $('<img id="BumbleBeeNoteBookOpen" class="notebook" src="..\\..\\images\\Sh240\\BumbleBeeNoteBookOpen.png" >').
                                    appendTo(parent).hide().data({"parent": this}).click(function(){ $(this).data().parent.openNotebook();});
    this.BumbleBeeWindowsLoading = $('<img id="BumbleBeeWindowsLoading" class="notebook" src="..\\..\\images\\Sh240\\BumbleBeeWindowsLoading.png" >').
                                    appendTo(parent).hide().data({"parent": this});//.click(function(){ $(this).data().parent.openNotebook();});
    this.BumbleBeeMobyDickRunning = $('<img id="BumbleBeeMobyDickRunning" class="notebook" src="..\\..\\images\\Sh240\\BumbleBeeMobyDickRunning.png" style="cursor:auto;">').
                                    appendTo(parent).hide().data({"parent": this});//.click(function(){ $(this).data().parent.openNotebook();});                                    

    /*Штативы излучатель и приемник*/    
    this.wires = $('<img id="wires" src="..\\..\\images\\Sh240\\wire.png" style="position:absolute;z-index:-1;top:0px;"> ').appendTo(parent).data({"parent": this}).hide();
    this.reseiver_unpacked = $('<img id="reseiver_unpacked" class="DragalebleElement" src="..\\..\\images\\Sh240\\reseiver_unpacked.png" title="Установите рентгенооптический преобразователь на штатив">').
                                    appendTo(parent).hide().data({"parent": this}).mouseup(function() { $(this).data().parent.receiverMouseUp() });
    this.sender_unpacked = $('<img id="sender_unpacked" class="DragalebleElement" src="..\\..\\images\\Sh240\\sender_unpacked.png" title="Установите ренгеновский аппарат на штатив">').
                                    appendTo(parent).hide().data({"parent": this}).mouseup(function() { $(this).data().parent.senderMouseUp() });
    this.receiver_tripod = $('<img id="receiver_tripod" src="..\\..\\images\\Sh240\\receiver_tripod.png" title="Поместите сюда рентгенооптический преобразователь">').
                                    appendTo(parent).data({"parent": this}).hide();
    this.sender_tripod = $('<img id="sender_tripod" src="..\\..\\images\\Sh240\\sender_tripod.png" title="Поместите сюда ренгеновский аппарат">').
                                    appendTo(parent).data({"parent": this}).hide();
    this.reseiver_assembled = $('<img id="reseiver_assembled"  src="..\\..\\images\\Sh240\\reseiver_assembled.png">').
                                    appendTo(parent).data({"parent": this}).hide();
    this.sender_assembled_rad = $('<img id="sender_assembled_rad"  src="..\\..\\images\\Sh240\\sender_assembled_rad.png">').
                                    appendTo(parent).data({"parent": this}).hide();                                    
    this.sender_assembled = $('<img id="sender_assembled"  src="..\\..\\images\\Sh240\\sender_assembled.png" >').
                                    appendTo(parent).data({"parent": this}).hide();    
                                    
    /*Кнопки на ноутбуке*/
    this.NotebookPowerPanel = $('<div id="NotebookShowPowerPanel" style="width: 65px;height: 65px;top: 317px;left: 110px;position: absolute;"> </div>').
                                    appendTo(parent).data({"parent": this}).mousemove(function() { $(this).data().parent.showPowerButton(); }).hide();
    this.NotebookPowerBtnPanel = $('<div id="NotebookPowerBtnPanel" class = "buttonOreol" style="top:310px;left:105px;"></div>').
                                    appendTo(parent).data({"parent": this}).mouseleave(function() { $(this).data().parent.hidePowerButton(); }).hide();
    this.NotebookBtnPowerOn = $('<img id="NotebookPowerButton" src="..\\..\\images\\Sh240\\notebookPowerButton.JPG" title="Кнопка включения ноутбука">').
                                    appendTo(this.NotebookPowerBtnPanel).data({"parent": this}).click( function () { $(this).data().parent.powerOn(); LogStudentActions(110); NotebookState = 4; CheckPowerOn();} );
    this.NotebookBtnPowerOff = $('<img id="NotebookPowerOffButton" src="..\\..\\images\\Sh240\\notebookPowerButtonOff.JPG" title="Кнопка выключения ноутбука">').
                                    appendTo(this.NotebookPowerBtnPanel).data({"parent": this}).click(function() { $(this).data().parent.powerOff(); LogStudentActions(112);}).hide();
    this.EmergencyShowPanel = $('<div id="EmergencyShowBtn" style="width: 29px;height: 40px;top: 335px;left: 190px;position: absolute"> </div>').
                                    appendTo(parent).data({"parent": this}).mousemove(function() { $(this).data().parent.showEmergencyBtn(); }).hide();
    this.EmergencyPnl = $('<div class = "buttonOreol" id="EmergencyPnlBtn" style="width: 65px;height: 65px;top: 317px;left: 167px;position: absolute;"></div>').
                                    appendTo(parent).data({"parent": this}).mouseleave(function() { $(this).data().parent.hideEmergencyBtn(); }).hide();
    this.EmergencyBtn = $('<img src="..\\..\\images\\Sh240\\EmergencyPowerBtn.jpg" title="Кнопка аварийного выключения">').
                                    appendTo(this.EmergencyPnl).data({"parent":this}).click(function() { $(this).data().parent.EmergencyShutdown();  LogStudentActions(113);});
    bindDragIvent(this.sender_unpacked);
    bindDragIvent(this.reseiver_unpacked);                                    

    
    /*Ключ на излучателе*/
    this.PnlShowKeyBtn = $('<div id="PnlShowKeyBtn" style="width: 80px;height: 80px;top: 104px;left: 281px;position: absolute;"> </div>').
                                    appendTo(parent).data({"parent": this}).mousemove(function() { $(this).data().parent.showKeyBtn(); }).hide();
    this.PnlKeyBtns = $('<div id="PnlKeyBtns" class="buttonBigOreol" style="width: 80px;height: 80px;top: 104px;left: 281px;position: absolute;"> </div>').
                                    appendTo(parent).data({"parent": this}).mouseleave(function() { $(this).data().parent.hideKeyBtn(); }).hide();                                    
    this.NoKeyBtn = $('<img id="NoKeyBtn" src="..\\..\\images\\Sh240\\NoKeyBtn.JPG" title="Вставьте ключ включения электропитания">').
                                    appendTo(this.PnlKeyBtns).data({"parent": this}).click( function () { $(this).data().parent.InsertKey(); LogStudentActions(114);} );
    this.KeyInsertedBtn = $('<img id="KeyInsertedBtn" src="..\\..\\images\\Sh240\\InsertedKeyBtn.jpg" title="Поверните ключ">').
                                    appendTo(this.PnlKeyBtns).data({"parent": this}).click(function() { $(this).data().parent.RotateKey(); LogStudentActions(115); }).hide();
    this.KeyRotatedBtn = $('<img id="KeyRotatedBtn" src="..\\..\\images\\Sh240\\RotatedKeyBtn.jpg" title="Ключ повернут">').
                                    appendTo(this.PnlKeyBtns).data({"parent": this}).click(function() { $(this).data().parent.RotateKeyBack(); }).hide();                                    
    

    /*Методы*/
    this.RadOn = function() {
        this.sender_assembled_rad.show();
        this.sender_assembled.hide();
    }
    this.RadOff = function() {        
        this.sender_assembled.show();
        this.sender_assembled_rad.hide();
    }


    this.GetCurrentImage = function () {
        return this.CurrentImage;
    }
    this.HideSidePanel = function() {
        $('#nav-panel').panel("close");
        $('#btnpanel').hide(); 
    }
    this.powerOff = function() {
        this.BumbleBeeNoteBookOpen.show();
        this.NotebookBtnPowerOn.show();
        this.BumbleBeeMobyDickRunning.hide();
        this.NotebookBtnPowerOff.hide();
        NotebookState = 5;
        this.HideSidePanel();       
        powerOff();
        setTimeout(function() {            
            CheckPowerOff();
            if(State != 5)
                $('#nav-panel').panel("open");
        },3500);
    }
    this.powerOn = function() {
        this.BumbleBeeWindowsLoading.show();
        this.NotebookBtnPowerOff.show();
        this.NotebookBtnPowerOn.hide();
        this.BumbleBeeNoteBookOpen.hide();
        powerWin();
        var PowerReady = function(e) {
            $(e.BumbleBeeMobyDickRunning).show();
            $(e.BumbleBeeWindowsLoading).hide();
            $(e.EmergencyShowPanel).show();
            $(e.EmergencyShowPanel).data().parent.spaunLuggage(10);
        }
        setTimeout(PowerReady,4300,this);
    }

    /*Методы по ключу*/
    this.showKeyBtn = function() {
        this.PnlKeyBtns.show();
    }
    this.hideKeyBtn = function() {
        this.PnlKeyBtns.hide();
    }
    this.InsertKey = function() {
        this.KeyInsertedBtn.show();
        this.NoKeyBtn.hide();
        this.KeyRotatedBtn.hide();
        SenderState = 3;
    }
    this.RotateKey = function() {
        if (SenderState == 3) 
        {
            this.KeyRotatedBtn.show();
            this.NoKeyBtn.hide();
            this.KeyInsertedBtn.hide();
            SenderState = 4;
            CheckPowerOn();
        } 
        else
        {
            this.EjectKey();
        }
    }
    this.RotateKeyBack = function() {
        //this.HideSidePanel();        
        this.KeyInsertedBtn.show();
        this.KeyRotatedBtn.hide();
        SenderState = 5;
        if (State == 3)
            State = 1;
        //reset(State);
    }
    this.EjectKey = function () {
        this.NoKeyBtn.show();
        SenderState = 6;
        CheckPowerOff();
    }
    /*Багаж*/
    /*Получаем информацию о всех багажах  из БД*/
    this.getLuggageInfo = function() {
        Luggage = null ;
        $.ajax({
            type: "POST",
            url: "actions.php",
            data: {
                type: 0
            },
            cache: false,
            async: false,
            dataType: "json",
            success: function(data) {
                Luggage = data;
                if (data != null) {
                    Luggage.forEach(function(elem, index, array) {
                        elem.used = 0;
                    });
                }
            }
        });
    }
    /*Ищем багаж, который еще не выводился на экран*/
    this.findNotUsedLugg = function() {
        if (Luggage != undefined ) {
            for (i = 0; i < Luggage.length; i++) {
                if (Luggage[i].used == 0)
                    return ( Luggage[i]) ;
            }
            /*Результат не найден, обнуляем все биты использования и возвращаем результат*/
            for (i = 0; i < Luggage.length; i++) {
                Luggage[i].used = 0;
            }
            return ( Luggage[0]) ;
        } else
            return ( null ) ;
    }
    /*Генерируем багаж*/
    this.spaunLuggage = function(count) {
        var i = 0;            
        while ((Luggage != undefined) && (i < count)) {
            posx = Math.random() * (250);
            posy = Math.max(400, Math.random() * ($("html").height() - 150 - 400) + 400);
            var l = this.findNotUsedLugg();
            l.used = 1;
            var img1 = $("<img id='Luggage" + l.IdImage + i +Math.floor(Math.random()*1000000)+ "' src='..\\..\\images\\preview\\" + l.PreviewPath + "' class = 'Luggage' style='cursor: move;height:75px;position:absolute;z-index:1002;top:" + posy + "px;left:" + posx + "px; '>").
                        data($.extend(l, {
                        "parent": this,
                        "realImageId": l.IdImage
            })).disableSelection();
            img1.appendTo(this.parent).hide().fadeIn(500);
            img1.mouseup(function () {
                if ( ($(this).position().left>=87) && ($(this).position().left+$(this).width() )<=310  &&
                     $(this).position().top>=45 && ($(this).position().top +$(this).height() )<=265 )
                {
                    $(this).unbind("mousedown");
                    $(this).animate({
                            left: (190-($(this).width()/2))+"px",
                            top: (118-($(this).height()/2))+"px"
                        }, 600);
                    if ($(this).data().parent.CurrentImbObj != null)
                        $($(this).data().parent.CurrentImbObj).remove();
                    $(this).data().parent.CurrentImbObj = this;
                    $(this).data().parent.CurrentImage = $(this).data().ImagePath;
                    $(this).unbind("mouseup mouseover mouseleave");
                    $(this).data().parent.spaunLuggage(1);
                    LogStudentActions(116);
                }

            });
            bindDragIvent(img1);
            i++;
        }
    }


    this.EmergencyShutdown = function() {
        State = 4;
        $('#nav-panel').panel("close");
        reset(State);
    }
    this.hideEmergencyBtn = function () {
        this.EmergencyPnl.hide();
    }
    this.showEmergencyBtn = function() {
        this.EmergencyPnl.show();
    }

    this.hidePowerButton = function () {
        this.NotebookPowerBtnPanel.hide();        
    }
    this.showPowerButton = function() {
        this.NotebookPowerBtnPanel.show();
    }

    this.CheckShowWire = function() {
        if (this.reseiver_assembled.is(":visible") && this.sender_assembled.is(":visible") && (this.BumblebeeNotebookClosedCaseOpen.is(":visible") || 
                    this.BumbleBeeNoteBookOpen.is(":visible") || this.BumbleBeeWindowsLoading.is(":visible") || this.BumbleBeeMobyDickRunning.is(":visible")))
            $(this.wires).fadeIn(500);
    }

    this.receiverMouseUp = function() {
        if (this.checkReceiverInPlace()) {
            this.reseiver_unpacked.animate({
                            left: "59px",
                            top: "70px"
                        }, 600);
            setTimeout(function()   {
                $(this.reseiver_assembled).show();
                $(this.reseiver_unpacked).hide();
                $(this.receiver_tripod).hide();
                $(this.PnlShowKeyBtn).show();                
                $(this.PnlShowKeyBtn).data().parent.CheckShowWire();
                LogStudentActions(108);
            },1200);            
            ReseiverState = 2;
            CheckPowerOn();//на случай если все остальное уже включено
        }        
    }

    this.senderMouseUp = function() {
        if (this.checkSenderInPlace()) {
            this.sender_unpacked.animate({
                            left: "281px",
                            top: "105px"
                        }, 600);
            setTimeout(function()   {
                $(this.sender_assembled).show(); 
                $(this.sender_unpacked).hide();
                $(this.sender_tripod).hide();
                $(this.PnlShowKeyBtn).data().parent.CheckShowWire();
                LogStudentActions(109);
            } , 1200); 
            SenderState = 2;           
        }
    }

    this.checkSenderInPlace = function() {
        if ((this.sender_unpacked.position().left>210 && this.sender_unpacked.position().left<350)  &&
            (this.sender_unpacked.position().top>70 && this.sender_unpacked.position().top<285))
        return true 
        else return false;
    }

    this.checkReceiverInPlace = function() {
        if ((this.reseiver_unpacked.position().left>-10 && this.reseiver_unpacked.position().left<110)  &&
            (this.reseiver_unpacked.position().top>40 && this.reseiver_unpacked.position().top<285))
        return true 
        else return false;
    }

    this.unpack = function()  {
        this.ClosedBumblebee.hide();
        this.BumblebeeNotebookCaseClosed.show();
        this.reseiver_unpacked.show();
        this.sender_unpacked.show();
        this.receiver_tripod.show();
        this.sender_tripod.show();        
    }

    this.openCase = function() {
        this.BumblebeeNotebookClosedCaseOpen.show();
        this.BumblebeeNotebookCaseClosed.hide();        
        this.CheckShowWire();
    }

    this.openNotebook = function () {
        this.BumbleBeeNoteBookOpen.show();
        this.BumblebeeNotebookClosedCaseOpen.hide();
        this.NotebookPowerPanel.show();        
    }
}

/*
{ powerWin();})
    }*/











(function($) {
    $.widget("ui.trainerSidePanel", {
        options: {
            LuggageCount: 10,
            TrainerType: 3,
            MTrainer: null ,
            TFront: null , /*перед, выключен*/
            Tback: null , /*back*/
            TFrontPOn: null , /*включена кнопка*/
            TFrontFullPowerOn: null, /*включена кнопка и горят зеленые диоды*/
            TFrontRad: null , /*Перед радиационное излучение активно*/
            TBtnPower: null ,
            TBtnPwrOff: null , /*кнопка выключить (то  есть горящая)*/
            TBtnPwrOn: null,   /*Кнопка включить (то есть не горящая)*/
            isTrainerStarted: 0, /*Тренажер полностью включен*/
            TPnlEmergencyShutdown: null,
            TBtnEmergencyShutdown: null,
            pnlEmergency:null,
            LuggageQueueLeft: [],
            LuggageQueueRight: [],
            LuggageOnLine: null,
            SomethingMovingOnRibbon:0
        },
        _create: function() {
            if (this.options.TrainerType == 3) {

               // this.options.MTrainer = $('<div style="position: relative;top: 0px;padding-left:30px;background=red;color = black;background: #b1b1b1;color: black;cursor: pointer;height: 300px;text-align: center;vertical-align: middle;">big red button</div>').
                 //                       appendTo(this.element).data({"parent": this}).click(function() { powerWin(); });
               /* this.options.TFront = $("<img id='TrainerMain'  src='..\\..\\images\\6070\\6070Front.png' style='width:304px;height:228px;z-index:1003;position:absolute;'>").
                                        appendTo(this.options.MTrainer).data({"parent": this}).mousemove(this.showPowerButton);
                this.options.TFrontPOn = $("<img id='TrainerMainPowerOn'  src='..\\..\\images\\6070\\6070FrontPowerButtonOnly.png' style='width:304px;height:228px;z-index:1003;position:absolute;'>").
                                            appendTo(this.options.MTrainer).hide().data({"parent": this}).mousemove(this.showPowerButton);
                this.options.TFrontFullPowerOn = $("<img id='TrainerMainFullPowerOn'  src='..\\..\\images\\6070\\6070FrontPowerOn.png' style='width:304px;height:228px;z-index:1003;position:absolute;'>").
                                                    appendTo(this.options.MTrainer).hide().data({"parent": this}).mousemove(this.showPowerButton);
                this.options.TFrontRad = $("<img id='TrainerMainRad'  src='..\\..\\images\\6070\\6070FrontRadOn.png' style='width:304px;height:228px;z-index:1003;position:absolute;'>").
                                            appendTo(this.options.MTrainer).hide().data({"parent": this}).mousemove(this.showPowerButton);
                this.options.Tback = $("<img id='TrainerBack'  src='..\\..\\images\\6070\\6070back.png' style='width:304px;height:228px;z-index:1001;position:absolute;'>").
                                        appendTo(this.options.MTrainer).data({"parent": this});
                this.options.TBtnPower = $("<div id='popup' style='width:90px;height:90px;border-radius:90px;border:2px solid black;z-index:10000;overflow:hidden;position:absolute;left:163px;top:117px;'></div>").
                                            appendTo(this.options.MTrainer).hide().data({"parent": this}).mouseleave(function() {   $(this).hide(); });
                this.options.TBtnPwrOn = $("<img src='..\\..\\images\\6070\\BtnPowerOn.jpg' style='width:90px;height:90px;cursor: pointer;'>").
                                            appendTo(this.options.TBtnPower).data({"parent": this}).click(this.powerOn);
                this.options.TBtnPwrOff = $("<img src='..\\..\\images\\6070\\BtnPowerOff.jpg' style='width:90px;height:90px;cursor: pointer;'>").
                                            appendTo(this.options.TBtnPower).data({"parent": this}).hide().click(this.powerOff);

                this.options.pnlEmergency = $("<div style='width:20px;height:20px;left: 110px;top: 48px;position: absolute;z-index: 2000;'></div>").
                                                appendTo(this.options.MTrainer).data({"parent": this}).mousemove(this.showEmergency);
                this.options.TPnlEmergencyShutdown = $("<div id='popup2' style='width:90px;height:90px;border-radius:90px;border:2px solid black;z-index:10000;overflow:hidden;position:absolute;left:75px;top:12px;'></div>").   
                                                        appendTo(this.options.MTrainer).hide().data({"parent": this}).mouseleave(function() { $(this).hide();  });
                this.options.TBtnEmergencyShutdown = $("<img src='..\\..\\images\\6070\\BtnEmergencyShutdown.jpg' style='width:90px;height:90px;cursor: pointer;'>").
                                                        appendTo(this.options.TPnlEmergencyShutdown).data({"parent": this}).click(this.EmergencyPwrOff);                                                        
  */

                this.getLuggageInfo();
                this.spaunLuggage(this.options.LuggageCount);
            }
        },
        leftSideCheck: function (el) {
            var result = false;
            if (
                    (($(el).width()+$(el).position().left)>=0) &&
                    (($(el).width()+$(el).position().left)<=220) &&
                    ($(el).position().top>=80) &&
                    ($(el).position().top<=320)
                ) {
                result = true;
            }
            return (result);
        },
        rightSideCheck: function (el) {
            var result = false;
            if (
                    (($(el).width()+$(el).position().left) >=221) &&
                    ($(el).position().left<=365) &&
                    ($(el).position().top>=80) &&
                    ($(el).position().top<=340)
               ) {
                result = true;
            }
            return(result);
        },
        showEmergency: function() {
            var self = null ;
            if (this.tagName == "DIV") {
                self = $(this).data().parent;
            } else {
                self = this;
            }
            self.options.TPnlEmergencyShutdown.show();
        },
        stopTrainer: function () {
            this.options.isTrainerStarted = 0;
            $(this.options.TFrontPOn).show();
            $(this.options.TFront).hide();
            $(this.options.TFrontFullPowerOn).hide();            
        },
        startTrainer1: function() {
            $(this.options.TFrontFullPowerOn).show();
            $(this.options.TFrontPOn).hide();
            $(this.options.TFront).hide();
        },
        startTrainer2: function() {
            this.options.isTrainerStarted = 1;
        },
        EmergencyPwrOff: function() {
            LogStudentActions(58);
            var self = null ;
            if (this.tagName == "IMG") {
                self = $(this).data().parent;
            } else {
                self = this;
            }
            State = 4;
            self.clearLuggage();
            self.hultAll();
        },
        stopLuggage: function() {
            $("#nav-panel img.Luggage").each(function(i) {
                $(this).stop(true);
                $(this).data().isMoving = 0;
                $(this).data().parent.options.SomethingMovingOnRibbon=0;
            });
        },
        clearLuggage: function() {
          $("#nav-panel img.Luggage").each(function(i) {
                $(this).remove();
            });  
        },
        hultAll: function() {
            /*полное выключение всего и вся*/      
            this.stopLuggage();
            $(this.options.TBtnPwrOn).fadeIn(0);
            $(this.options.TBtnPwrOff).fadeOut(0);
            $(this.options.TFrontPOn).hide();
            $(this.options.TFrontRad).hide();
            $(this.options.TFrontFullPowerOn).hide();
            $(this.options.TFront).show();            
            reset(State);
        },
        powerOn: function() {
            LogStudentActions(61);
            var self = null ;
            if (this.tagName == "IMG") {
                self = $(this).data().parent;
            } else {
                self = this;
            }
            $(self.options.TBtnPwrOff).fadeIn(0);
            $(self.options.TBtnPwrOn).fadeOut(0);
            $(self.options.TFront).hide();
            $(self.options.TFrontPOn).show();
            State = 1;
            toggePanel();
        },
        showPowerButton: function(e) {
            var self = null ;
            if (this.tagName == "IMG") {
                self = $(this).data().parent;
            } else {
                self = this;
            }
            var x = e.pageX - this.offsetLeft;
            var y = e.pageY - this.offsetTop;
            if (x >= 176 && x <= 190 && y >= 272 && y <= 285) {
                self.options.TBtnPower.show();
            }
        },
        powerOff: function() {
            LogStudentActions(62);
            var self = null ;
            if (this.tagName == "IMG") {
                self = $(this).data().parent;
            } else {
                self = this;
            }
/*
            if (State ==1) {
                State = 0;
            } else if (State==8) {
                State = 9;
            } else {
                State = 12;
            }    */
            self.options.isTrainerStarted = 0;   

            self.hultAll();     
        },
        animateRad: function() {
            $(this.options.TFront).hide();
            $(this.options.TFrontRad).fadeIn(0).delay(2100).fadeOut(0);
            $(this.options.TFrontPOn).fadeOut(0).delay(2000).fadeIn(0);
        },
        /*Получаем информацию о всех багажах  из БД*/
        getLuggageInfo: function() {
            Luggage = null ;
            $.ajax({
                type: "POST",
                url: "actions.php",
                data: {
                    type: 0,
                    IdTrainer: this.options.TrainerType
                },
                cache: false,
                async: false,
                dataType: "json",
                success: function(data) {
                    Luggage = data;
                    if (data != null) {
                        Luggage.forEach(function(elem, index, array) {
                            elem.used = 0;
                        });
                    }
                }
            });
        },
        /*Ищем багаж, который еще не выводился на экран*/
        findNotUsedLugg: function() {
            if (Luggage != undefined ) {
                for (i = 0; i < Luggage.length; i++) {
                    if (Luggage[i].used == 0)
                        return ( Luggage[i]) ;
                }
                /*Результат не найден, обнуляем все биты использования и возвращаем результат*/
                for (i = 0; i < Luggage.length; i++) {
                    Luggage[i].used = 0;
                }
                return ( Luggage[0]) ;
            } else
                return ( null ) ;
        },
        /*Генерируем багаж*/
        spaunLuggage: function(count) {
           /* var i = 0;            
            while ((Luggage != undefined) && (i < count)) {
                posx = Math.random() * (250);
                posy = Math.max(400, Math.random() * ($("html").height() - 150 - 400) + 400);
                var l = this.findNotUsedLugg();
                l.used = 1;
                var img1 = $("<img id='Luggage" + l.IdImage + i +Math.floor(Math.random()*1000000)+ "' src='..\\..\\images\\preview\\" + l.PreviewPath + "' class = 'Luggage' style='height:75px;position:absolute;z-index:1002;top:" + posy + "px;left:" + posx + "px; '>").
                            data($.extend(l, {
                    "parent": this,
                    "postedToRibbon": 0,
                    "isMoving": 0,
                    "realImageId": l.IdImage
                })).disableSelection();
                img1.appendTo(this.element).hide().fadeIn(500);
                bindDragIvent(img1);
                i++;
            }*/
        },
        startImgMovement: function(Luggage) {
            //Состояние ленты  //0 - стоит 1 - вправо 2 - влево
            //console.log('startImgMovement');
            if ((move>0)  && ($(Luggage).data().isMoving ==0 ))
            {
                $(Luggage).appendTo($(Luggage.parent()));
                var t = 7000;
                var t1 = 3500;
                this.options.LuggageOnLine = Luggage;                

                if (move == 1) {
                    /*вправо*/  
                    /*линия детекторов для данного багажа*/
                    var RadX = 215-($(Luggage).width());
                    var RadY = (((215-($(Luggage).width()))/215)*68)+167;

                    $(Luggage).data().isMoving = 1; 
                    this.options.SomethingMovingOnRibbon=1;
                    t = Math.round((270-$(Luggage).position().left)/228*7000);
                    t1 = Math.round((RadX-$(Luggage).position().left)/114*3500);
                    //console.log("RadX="+RadX+";RadY="+RadY+";t="+t+";t1="+t1+";t-t1="+(t-t1));
                    if ($(Luggage).position().left>=RadX) {
                        $(Luggage).animate({
                            left: "270px",
                            top: "167px"
                        }, t).fadeOut(2000, function() {
                            $(this).data().parent.options.LuggageOnLine = null;
                            $(this).data().parent.spaunLuggage(1);
                            $(this).data().parent.options.SomethingMovingOnRibbon=0;
                            $(this).data().parent.startMoveSideRibbon();
                            $(this).remove();
                        });
                    } else {
                        $(Luggage).animate({
                            left: RadX+"px",
                            top: RadY+"px"
                        }, t1, function() {
                            $(this).data().parent.animateRad();
                            startMoveRibbon($(Luggage).data().ImagePath);
                            $(Luggage).animate({
                                left: "270px",
                                top: "167px"
                            }, t-t1).fadeOut(2000, function() {
                                $(this).data().parent.options.LuggageOnLine = null;
                                $(this).data().parent.spaunLuggage(1);
                                $(this).data().parent.options.SomethingMovingOnRibbon=0;
                                $(this).data().parent.startMoveSideRibbon();
                                $(this).remove();
                            });                            
                        }).animate({left: RadX+10+"px"},300);
                    }
                } else if (move == 2) {
                    /*влево*/
                    /*линия детекторов для данного багажа*/
                    var RadX = 215;
                    var RadY = 180;

                    $(Luggage).data().isMoving = 1; 
                    this.options.SomethingMovingOnRibbon=1;
                    t = Math.round(($(Luggage).position().left-16)/228*7000);
                    t1 = Math.round(($(Luggage).position().left-RadX)/114*3500);
                    //console.log("RadX="+RadX+";RadY="+RadY+";t="+t+";t1="+t1+";t-t1="+(t-t1));
                    if ($(Luggage).position().left<=RadX) {
                        $(Luggage).animate({
                            left: "42px",
                            top: "235px"
                        }, t).fadeOut(2000, function() {
                            $(this).data().parent.options.LuggageOnLine = null;
                            $(this).data().parent.spaunLuggage(1);
                            $(this).data().parent.options.SomethingMovingOnRibbon=0;
                            $(this).data().parent.startMoveSideRibbon();
                            $(this).remove();
                        });
                    } else {
                        $(Luggage).animate({
                            left: RadX+"px",
                            top: RadY+"px"
                        }, t1, function() {
                            $(this).data().parent.animateRad();
                            startMoveRibbon($(Luggage).data().ImagePath);
                            $(Luggage).animate({
                                left: "42px",
                                top: "235px"
                            }, t-t1).fadeOut(2000, function() {
                                $(this).data().parent.options.LuggageOnLine = null;
                                $(this).data().parent.spaunLuggage(1);
                                $(this).data().parent.options.SomethingMovingOnRibbon=0;
                                $(this).data().parent.startMoveSideRibbon();
                                $(this).remove();
                            });                            
                        }).animate({left: RadX-10+"px"},300);
                    }
                   

                    /*
                    $(Luggage).data().isMoving = 1;
                    this.options.SomethingMovingOnRibbon=1;
                    t = Math.round(($(Luggage).position().left-16)/288*7000);
                    startMoveRibbon($(Luggage).data().ImagePath);
                    $(Luggage).animate({
                        left: "16px",
                        top: "137px"
                    }, t).fadeOut(2000, function() {
                        $(this).data().parent.options.LuggageOnLine = null;
                        $(this).data().parent.spaunLuggage(1);   
                        $(this).data().parent.options.SomethingMovingOnRibbon=0;
                        $(this).data().parent.startMoveSideRibbon();    
                        $(this).remove();
                    });*/
                }
                /*if (Luggage.position().left==127)
                        Trainer.animateRad();*/
            }
        },
        startMoveSideRibbon: function() {
            if (move>0 && this.options.SomethingMovingOnRibbon==0) {
                if (move == 1) {
                    if (this.options.LuggageQueueRight.length>0) {
                        /*Очистили ленту справа*/
                        this.spaunLuggage(this.options.LuggageQueueRight.length);
                        this.options.LuggageQueueRight.forEach(function(element, index, array) {$(element).remove();});
                        this.options.LuggageQueueRight = [];
                        LogStudentActions (63);
                    }
                    if ((this.options.LuggageOnLine == null || this.options.LuggageOnLine === undefined) && (this.options.LuggageQueueLeft.length>0)) {
                        this.startImgMovement(this.options.LuggageQueueLeft.shift());
                    } else if (!(this.options.LuggageOnLine == null || this.options.LuggageOnLine === undefined)){
                        this.startImgMovement(this.options.LuggageOnLine);
                    }                    
                }
                if (move == 2) {
                    if (this.options.LuggageQueueLeft.length>0) {
                        /*Очистили ленту слева*/
                        this.spaunLuggage(this.options.LuggageQueueLeft.length);
                        this.options.LuggageQueueLeft.forEach(function(element, index, array) { $(element).remove();});
                        this.options.LuggageQueueLeft = [];
                        LogStudentActions (64);
                    }
                    if ((this.options.LuggageOnLine == null || this.options.LuggageOnLine === undefined) && (this.options.LuggageQueueRight.length>0)) {
                        this.startImgMovement(this.options.LuggageQueueRight.shift());
                    } else if (!(this.options.LuggageOnLine == null || this.options.LuggageOnLine === undefined)){
                        this.startImgMovement(this.options.LuggageOnLine);
                    }
                }
               /* setTimeout(7000, function() {
                                                    this.startMoveSideRibbon();
                                                });*/
            }
        },
        checkInTrainer: function(Luggage) {
            if (this.leftSideCheck(Luggage))
            {
                $(Luggage).offset({
                    top: 235,
                    left: 42
                });
                this.options.LuggageQueueLeft.push(Luggage);
                $(Luggage).data().postedToRibbon =1;
                $(Luggage).unbind("mousedown");
                LogStudentActions (65,$(Luggage).data().realImageId);
            } else if (this.rightSideCheck(Luggage)) {
                $(Luggage).offset({
                    top: 167,
                    left: 270
                });
                this.options.LuggageQueueRight.push(Luggage);
                $(Luggage).data().postedToRibbon =1;
                $(Luggage).unbind("mousedown");
                LogStudentActions (66,$(Luggage).data().realImageId);
            }
            this.startMoveSideRibbon();

            /*if ($(Luggage).position().left >= 50 && $(Luggage).position().left <= 300 && $(Luggage).position().top >= 100 && $(Luggage).position().top <= 280 && this.options.isTrainerStarted ==1) {
                $(Luggage).offset({
                    top: 182,
                    left: 244
                });
                if ($(Luggage).data().postedToRibbon == 0) {
                    addImgRibbon($(Luggage).data().ImagePath);
                    $(Luggage).data().postedToRibbon = 1;
                }
                if (move != 0) {
                    var Trainer = this;
                    $(Luggage).animate({
                        left: "16px",
                        top: "137px"
                    }, 7000).fadeOut(2000, function() {
                        $(this).remove();
                    });
                    setTimeout(function() {
                        Trainer.animateRad();
                    }, 3500);
                    startMoveRibbon($(Luggage).data().ImagePath);
                    this.spaunLuggage(1);
                }
            }*/
        }
    });
})(jQuery);
$(function() {
    /*wid = $('#nav-panel').trainerSidePanel({
        LuggageCount: 10,
        TrainerType: 3
    });    */


    /*classes*/
    Trainer = new SidePanel($("#nav-panel"));
    Trainer.getLuggageInfo();
});
function CheckLuggage() {
    $("#nav-panel img.Luggage").first().data().parent.startMoveSideRibbon();
}

