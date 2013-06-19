(function($) {
    $.fn.LinkManager = function (params) {

        var canvas = $(this);

        var stage = null;
        var imgLayer = new Kinetic.Layer();
        var linkLayer = new Kinetic.Layer();
        var tooltipLayer = new Kinetic.Layer();


        var obj = $.parseJSON(params.link);
        var imgSrc = params.imageSource;

        if (typeof(obj) != 'undefined' && imgSrc != "") {
            $(window).load(function() {
                InitializeCanvas();
            });
        }

        function InitializeCanvas() {

            var bkgImage = new Image();
            bkgImage.src = imgSrc;
            bkgImage.onload =  function () {
                stage = new Kinetic.Stage({ container: canvas.attr('id'),
                                            width: bkgImage.width,
                                            height: bkgImage.height });
//                    imgLayer = new Kinetic.Layer();
//                    linkLayer = new Kinetic.Layer();
                var img = new Kinetic.Image({ x: 0,
                                              y: 0,
                                              image: bkgImage,
                                              width: bkgImage.width,
                                              height:bkgImage.height,
                                              id: 'bkg' });

                imgLayer.add(img);

                for (var i=0; i < obj.length; i++) {
                    PlacePosition(i, obj[i]);
                }

                stage.add(imgLayer);
                stage.add(linkLayer);
                stage.add(tooltipLayer);
                stage.draw();

                CreateToolTip();
                if ($(window).outerHeight() < ($('.header-container').height()+$('.main-container').height()))
                    setTimeout(function() {$('.main-container').ScrollTo();}, 500);
            };
        }

        function CreateToolTip() {
             var tooltip = new Kinetic.Label({
                x: 0,
                y: 0,
                id: 'myToolTip',
                opacity: 0.75,                    
              });

              tooltip.add(new Kinetic.Tag({
                fill: 'black',
                pointerDirection: 'down',
                pointerWidth: 10,
                pointerHeight: 20,
                lineJoin: 'round',
                shadowColor: 'black',
                shadowBlur: 10,
                shadowOffset: 10,
                shadowOpacity: 0.5,
                cornerRadius: 10
              }));

              tooltip.add(new Kinetic.Text({
                id: 'Description',  
                fontFamily: 'Calibri',
                fontSize: 14,
                padding: 5,
                fill: 'white'//,
//                    width: 300,
//                    height: 150
              }));
              tooltipLayer.add(tooltip);
        }

        function ShowToolTip(posX, posY, objId) {

            var toolTip = stage.get('#myToolTip')[0];
            toolTip.setX(posX);
            toolTip.setY(posY);
            var child= toolTip.getChildren() || [];
            for (var i=0; i < child.length; i++) {
                switch (child[i].getId()) {
                    case 'Description':
                        child[i].setText("("+obj[objId].pos+") "+obj[objId].sku.toUpperCase() + "\n\n" + obj[objId].name);
                        break;
                }

            }
            tooltipLayer.setVisible(true);
            tooltipLayer.draw();
            $('#'+obj[objId].sku).ScrollTo({'noFinal': true}).addClass('selected');
            canvas.css( 'cursor', 'pointer' );
//                var imageObj = new Image();
//
//                imageObj.onload = function() {
//                  tooltipLayer.add(new Kinetic.Image({ x: 0,
//                                                  y: 0,
//                                                  image: imageObj,
//                                                  width: imageObj.width,
//                                                  height:imageObj.height,
//                                                  id: 'bkg' }));
//                    tooltipLayer.setVisible(true);
//                    tooltipLayer.draw();
//                };
//                imageObj.src = 'http://www.html5canvastutorials.com/demos/assets/darth-vader.jpg';                
        }

        function HideToolTip(objId) {
            tooltipLayer.setVisible(false);
            tooltipLayer.draw();
            canvas.css( 'cursor', 'default' );
            $('#'+obj[objId].sku).removeClass('selected');
        }

        function AddQty(objId) {
            $('#super_group_'+obj[objId].id).attr('value', parseInt($('#super_group_'+obj[objId].id).attr('value')) + 1);
            if (!$('#'+obj[objId].sku).hasClass('has-qty')) {
                $('#'+obj[objId].sku).addClass('has-qty');
            }
        }

        function PlacePosition(index, objPosition) {

            var Position = new Kinetic.Group({
                id: objPosition.id,
                draggable: false,
                positionText: objPosition.pos, 
                sku: objPosition.sku,
                name: objPosition.name,
                objId: index
            });
            var PosCircle = new Kinetic.Circle({
                x: objPosition.x,
                y: objPosition.y,
                radius: 12,
                fill: '#EB9999',
                stroke: 'black',
                objId: index
            });
            var PosText = new Kinetic.Text({
                 x: objPosition.x-10,
                 y: objPosition.y-7,
                 text: objPosition.pos, 
                 fill: 'balck',
                 fontSize: 14,
                 width: 20,
                 fontStyle: 'bold',
                 align: 'center',
            });

            Position.add(PosCircle);
            Position.add(PosText);
            linkLayer.add(Position);

            Position.on('mouseenter', function(e) {
                var child= this.getChildren();
                ShowToolTip(e.layerX, e.layerY, child[0].attrs.objId) 
            });

            Position.on('click', function(e) {
                var child= this.getChildren();
                AddQty(child[0].attrs.objId) 
            });

            Position.on('mouseover', function(e) {
                var child= this.getChildren();
                child[0].setFill('#0096d9');
                linkLayer.draw();
                console.log(e);
            });

            Position.on('mouseout', function(e) {
                var child= this.getChildren();
                child[0].setFill('#EB9999');
                HideToolTip(child[0].attrs.objId);
                linkLayer.draw();                    
                console.log(e);
            });

       }
   }
})(jQuery);
