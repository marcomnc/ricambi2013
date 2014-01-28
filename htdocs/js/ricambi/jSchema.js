(function($) {

    $.fn.AdminCanvasManagerGrid = function(objFlat, imgUrl, imgDelete, Grid) {
        
        var canvas = $(this);
        var imgSrc = imgUrl;
        var delSrc = imgDelete;
        var idGrid = Grid;
        if (objFlat!="") {
            objLink = objFlat.evalJSON();
        }

        
        if (imgSrc != "") {
            $(window).load(function() {
                InitializeCanvas();
            });
        }
        
        function DecorateGrid(idGrid) {
            if ($('#'+idGrid+' span.counter').length > 0) {
                
                $('#'+idGrid+' span.counter').each(function() {
                    $(this).setAttribute('rel',0);
                });
                
                for(var id in objLink) {
                    if (typeof(objLink[id].state) == 'undefined' || objLink[id].state != 'delete') {
                        var idLink = objLink[id].linkid;
                        $('#'+idGrid+' tr:not(.headings)').each(function() {
                            if ($(this).hasClass('link_id_'+idLink)) {
                                $(this).addClassName('assigned');
                                $('#count_'+idLink).attr('rel' ,parseInt($('#count_'+idLink).attr('rel'))+1);
                            }
                        });
                    }
                }            
                RefrehCounter();
            }
        };
            
            
        function RefrehCounter() {
            $('span.counter').each(function() {
                $(this).text($(el).attr('rel'));
            });
        };
        
        
        function InitializeCanvas() {
            canvas.append($('<img>', {'id' : 'loading-canvas', 'src':"/js/ricambi/css/images/loading.gif"})
                                                               .css({'position': 'absolute',
                                                                     'top': '20%',
                                                                     'left' : '50%'}));

            var bkgImage = new Image();
            bkgImage.src = imgSrc;
            bkgImage.onload = function () {
                canvas.append($('<img>', {'id' : 'machine', 'src':bkgImage.src})
                                                               .css({'position': 'absolute',
                                                                     'display': 'none',
                                                                     'top': '0px',
                                                                     'left' : '0px',
                                                                     'display': 'none'}));
                $('#machine').show();
                $('#loading-canvas').hide().remove();
                                
                for (var i=0; i < link.length; i++) {
                    //PlacePosition(i, obj[i]);
                }
                
                DecorateGrid(idGrid);
            }
        }

    }

}) (jQuery);

(function($) {
        
        $.fn.LinkManager = function(params) {
          var canvas = $(this);
          
            var obj = $.parseJSON(params.link);
            var imgSrc = params.imageSource;
            
            var inputOpentip = [];

            if (typeof(obj) != 'undefined' && imgSrc != "") {
                $(window).load(function() {
                    InitializeCanvas();
                });
            }
            
            
            function InitializeCanvas() {
                canvas.append($('<img>', {'id' : 'loading-canvas', 'src':"/js/ricambi/css/images/loading.gif"})
                                                                   .css({'position': 'absolute',
                                                                         'top': '20%',
                                                                         'left' : '50%'}));
                                                                     
                var bkgImage = new Image();
                bkgImage.src = imgSrc;
                bkgImage.onload = function () {
                    canvas.append($('<img>', {'id' : 'machine', 'src':bkgImage.src})
                                                                   .css({'position': 'absolute',
                                                                         'display': 'none',
                                                                         'top': '0px',
                                                                         'left' : '0px',
                                                                         'display': 'none'}));
                    $('#machine').show();
                    $('#loading-canvas').hide().remove();
                    for (var i=0; i < obj.length; i++) {
                        PlacePosition(i, obj[i]);
                    }
                    
                }
                
                if ($(window).outerHeight() < ($('.header-container').height()+$('.main-container').height()))
                    setTimeout(function() {$('.main-container').ScrollTo();}, 500);

            }
            
            function PlacePosition(index, objPosition) {
                var pos = $('<div>' , {'id': 'position' + index, 'class' : 'positioner', 'rel': objPosition.id, 'unselectable': 'on',
                                       'data-ot': 'The content'});
                pos.css({'position': 'absolute', 'top': (objPosition.y-8)+'px','left': (objPosition.x-8)+'px' });                 

                //inputOpentip[index] = new Opentip(pos, objPosition.name, objPosition.sku, {'style': 'glass'});
                var html = (typeof(objPosition.popup) === 'undefined' || objPosition.popup === null) ? "" : decodeURIComponent(escape(window.atob( objPosition.popup )));
                if (html)
                    inputOpentip[index] = new Opentip(pos, html, "", {'style': 'glass'});
                else
                    inputOpentip[index] = new Opentip(pos, objPosition.name, objPosition.sku, {'style': 'glass'});

                pos.on('click', function(e) {
                    AddQty($(this).attr('id').toString().replace('position','')); 
                });

                pos.on('mouseover', function(e) {
                    $('.positioner[rel="' + $(this).attr('rel')  + '"]').addClass('selected');
                    try {
                        $('.option_group_'+objPosition.sku).addClass('selected')
                        $('.option_group_'+objPosition.sku).ScrollTo({'noFinal': true});                    
                    } catch( e) {}
                    inputOpentip[index].show();
                });

                pos.on('mouseleave', function(e) {
                    $('.positioner[rel="' + $(this).attr('rel')  + '"]').removeClass('selected');
                    inputOpentip[index].hide();
                    $('.option_group_'+objPosition.sku).removeClass('selected');
                });

                canvas.append(pos.append('<div>'+objPosition.pos+'</div>'));
                
            }
            
            
            function AddQty(objId) {
                $('#super_group_'+obj[objId].id).attr('value', parseInt($('#super_group_'+obj[objId].id).attr('value')) + 1);
                if (!$('#'+obj[objId].sku).hasClass('has-qty')) {
                    $('#'+obj[objId].sku).addClass('has-qty');
                }
            }


            $('.input-text.qty').change(function(evt) {
               if ($(this).val() > 0) {
                   $(this).parent().parent().addClass('has-qty');               
               } else {
                   $(this).parent().parent().removeClass('has-qty')
               }
           });

        };
        
    }) (jQuery);
    

