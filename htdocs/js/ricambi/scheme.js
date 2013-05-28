/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *  
 *
 * @category    
 * @package        
 * @copyright   Copyright (c) 2013 Mps Sistemi (http://www.mps-sistemi.it)
 * @author      MPS Sistemi S.a.s - Marco Mancinelli <marco.mancinelli@mps-sistemi.it>
 *
 */

var AdminCanvasManagerGrid = new Class.create();

AdminCanvasManagerGrid.prototype = {
    initialize: function(objFlat, imgUrl, container, imgDelete) {
        this.imgUrl = imgUrl;
        this.imgDelete = imgDelete;
        this.idGrid = null;
        this.link = null;
        if (objFlat!="") {
            this.link=objFlat.evalJSON();
        }
        this.stage = new Kinetic.Stage({
                    container: container,
                    width: 544,
                    height: 794,
                    id: 'gcanvas'
            });
        this.layer = new Kinetic.Layer();
        this.layerDrag = new Kinetic.Layer();
        this.layerDragRect = null;
        this.layerGroup = new Kinetic.Layer();
        this.layerDelete = new Kinetic.Layer();
        
        this.myCanvas = $('grouped-canvas').down(container);

        this.trInitializeImage = this.InitializeImage.bindAsEventListener(this);
        this.trInitializeCanvas = this.InitializeCanvas.bindAsEventListener(this);
        this.trLayerAddImageDelete = this.LayerAddImageDelete.bindAsEventListener(this);
        this.trRecalculateObject = this.RecalculateObject.bindAsEventListener(this);

        //Drag Manager
        this.startAt={x:0,y:0};
        this.trDragPosition = this.DragPosition.bind(this); // Drag di un pallino
        this.trDragOnLayer = this.DragOnLayer.bind(this);
        this.trDragManagerStart = this.DragManagerStart.bind(this);
        this.trDragManagerEnd = this.DragManagerEnd.bind(this);
        this.isExternalDragged = false; // Indica se sto trascinando dall'esterno
        this.PositionDragged = {};

        this.isEvidence = false;
        this.trEvidenceRow = this.EvidenceRow.bind(this);

        this.image = new Image();
        this.image.onload = this.trInitializeCanvas;

        Event.observe(window, 'load', this.trInitializeImage);
        this.DragManager();
    },
    DecorateGrid: function(idGrid) {
        this.idGrid = idGrid;
        $$('#'+this.idGrid+' span.counter').each(function(el) {
            $(el).setAttribute('rel',0);
        });
        for(var id in this.link) {
            if (typeof(this.link[id].state) == 'undefined' || this.link[id].state != 'delete') {
                var idLink = this.link[id].linkid;
                $$('#'+this.idGrid+' tr:not(.headings)').each(function(el) {
                    if ($(el).hasClassName('link_id_'+idLink)) {
                        $(el).addClassName('assigned');
                        $('count_'+idLink).setAttribute('rel',parseInt($('count_'+idLink).getAttribute('rel'))+1);
                    }
                });
            }
        }            
        this.RefrehCounter();
    },
    RefrehCounter: function() {
        $$('span.counter').each(function(el) {
            $(el).update($(el).getAttribute('rel'));
        });
    },
    InitializeImage: function() {

        this.image.src = this.imgUrl;
    },
    InitializeCanvas: function() {
        var image = new Kinetic.Image({
                                x: 0,
                                y: 0,
                                image: this.image,
                                width: this.image.width,
                                height:this.image.height,
                                id: 'bkg'
                            });
        image.setListening(false);
        imgDel = new Image();
        imgDel.onload = this.trLayerAddImageDelete;
        imgDel.src = this.imgDelete;
        this.layer.add(image);
        for(var id=0; id<this.link.length;id++) {
            this.PlacePosition(this.link[id].id_link, this.link[id].x, this.link[id].y, this.link[id].linkid, this.link[id].pos);
        }
        //Posiziono il drag
        this.layerDragRect = new Kinetic.Rect({
                                    x: 0,
                                    y: 0,
                                    id: 'DragLayer',
                                    width: image.getWidth(),
                                    height: image.getHeight(),
                                    fill: 'black',
                                    stroke: 'green',
                                    strokeWidth: 4,
                                    opacity: 0
                                });
        this.layerDrag.add(this.layerDragRect);

        this.stage.add(this.layerGroup);
        this.stage.add(this.layerDrag);
        this.stage.add(this.layer);
        this.layer.setZIndex(0);
        this.layerGroup.setZIndex(10);
        this.layerDragRect.setZIndex(5);

        this.stage.draw();
    },  
    PlacePosition: function(id, x, y, linkid, pos) {

        var Position = new Kinetic.Group({
            id: id,
            draggable: true,
            linkid: linkid,
            positionText: pos,
            state: (id=='new')?'create':'update'
        });
        var PosCircle = new Kinetic.Circle({
            x: x,
            y: y,
            radius: 10,
            fill: 'red',
            opacity: 0.50,
            linkid: linkid
        });
        var PosText = new Kinetic.Text({
             x: pos.length>1?(x-7):(x-5),
             y: y-6,
             text: pos, 
             fill: 'balck',
             fontSize: 14,
             fontStyle: 'bold',
             align: 'center',
             linkid: linkid
        });

        Position.add(PosCircle);
        Position.add(PosText);
        this.layerGroup.add(Position);

        //var layer = this.layer;
        //var stage = this.stage;

        //Position.on('mouseenter', this.EvidenceRow);
        var evidence = this.EvidenceRow;
        Position.on('mouseout.position'+linkid, function(ev) {
            evidence(linkid,false);
            ev.cancelBubble=true;
        });
        Position.on('mouseenter.position'+linkid, function(ev) {
            evidence(linkid,true);
            ev.cancelBubble=true;
        });

        Position.on('dragstart', function(e) {
            this.setAttrs({origCircleX: PosCircle.getPosition().x,
                           origCircleY: PosCircle.getPosition().y});
            PosCircle.setAttrs({
                fill: 'green',
                opacity: 0.20
            }); 
        });
        
        Position.on('dragend', this.trDragPosition);
        Position.setZIndex(10);
        this.layerGroup.draw();
    },
    DragPosition: function(e) {
        var position = e.dragEndNode;
        var circle = position.children[0];
        circle.setAttrs({
            fill: 'red',
            opacity: 0.50
        });
        var ReturnAnim = new Kinetic.Tween({
            node: position,
            x: 0, 
            y: 0, 
            duration: 1,
            onFinish: function() {
                circle.setPosition({x:parseInt(position.attrs.origCircleX), y:parseInt(position.attrs.origCircleY)});
                position.fire('mouseout');
            }
         });

        var usrPosition=this.stage.getPointerPosition();

        if (this.stage.get('#DeleteBox')[0].intersects(circle.getAbsolutePosition())) {
            if (confirm('Cancello il posizionamento?')) {
                position.setAttrs({state: 'delete'});
                position.setVisible(false);
                this.layerGroup.draw();
                this.RecalculateObject();        
            } else {
                ReturnAnim.play();
                position.fire('mouseout');                
            }
            return;
        }
        if (typeof(usrPosition)=='undefined') {
            position.fire('mouseout');
        }
        this.layerGroup.draw();
        this.RecalculateObject();        

    },
    RecalculateObject: function() {
        var arr=[];
        this.layerGroup.children.each( function(pos) {                
            var obj={};
            obj.id_link = pos.attrs.id;
            obj.linkid = pos.attrs.linkid;
            obj.pos = pos.attrs.positionText;
            for (var child in pos.children) {
                if (pos.children[child].shapeType=='Circle') {
                    var linkPos = pos.children[child].getAbsolutePosition();
                    obj.x = linkPos.x;
                    obj.y = linkPos.y;
                }
            }
            obj.state = pos.attrs.state;
            arr.push(obj);
        });
        this.link = arr;
        this.DecorateGrid(this.idGrid);
        $('associated-scheme').setAttribute('value', Base64.encode(Object.toJSON(arr)));
        if (varienElementMethods) {
            varienElementMethods.setHasChanges($('associated-scheme'), null);
        }
    },
    EvidenceRow: function(linkid, yes) {
        var cls='tr.position_list_table.link_id_'+linkid;

        $$(cls).each(function(el) {
            if (yes) $(el).addClassName('evidence');
            else $(el).removeClassName('evidence');
        });
    },
    LayerAddImageDelete: function() {
        var bkg = this.stage.get('#bkg')[0];
        var img = new Kinetic.Image({
                        x: bkg.getWidth() - imgDel.width,
                        y: 0,
                        id: 'DeleteBox',
                        image: imgDel,
                        width: imgDel.width,
                        height:imgDel.height,
                        draggable: false
                    });
        this.layerDelete.add(img);
        this.stage.add(this.layerDelete);
        this.layerDelete.draw();
        this.layerDelete.setZIndex(1);
    },
    DragManager: function() {
        for(var addTo=0;addTo<$$('.position-add-to').length;addTo++){
            new Draggable($$('.position-add-to')[addTo],{ scroll: window, gost: true, revert:true,   
                                                          onStart: this.trDragManagerStart,
                                                          onEnd: this.trDragManagerEnd,
                                                          onDrag: this.trDragOnLayer
                                                      });
        }
    },
    DragManagerStart: function(sender, event) {
        this.layerDragRect.setZIndex(30);
        this.PositionDragged = {
            id: 'new',
            x: 0,
            y: 0,
            linkid: sender.handle.id.replace('link_id_', ''),
            pos: $(sender.handle.id).readAttribute('rel')
        };

    },
    DragManagerEnd: function(sender, event) {
        this.layerDragRect.setZIndex(5);
        if (this.isExternalDragged && this.PositionDragged != null) {
            this.PlacePosition(this.PositionDragged.id, (event.pageX - this.stage.content.offsetLeft), (event.pageY - this.stage.content.offsetTop), this.PositionDragged.linkid, this.PositionDragged.pos );
            this.layerDragRect.setOpacity(0);
            this.layerDrag.draw();
            this.RecalculateObject();
        }
        this.isExternalDragged = false;
        this.PalcePostion = null;
    },
    DragOnLayer: function(sender, event) {    
        var obj = this.stage.getIntersection({x:event.pageX - this.stage.content.offsetLeft, y:event.pageY - this.stage.content.offsetTop})
        if (!this.isExternalDragged) {
            if (obj != null && obj.shape.attrs.id == 'DragLayer') {
                this.layerDragRect.setOpacity(0.5);
                this.layerDrag.draw();
                this.isExternalDragged = true;
            }
        } else {
            if (obj == null) {
                this.layerDragRect.setOpacity(0);
                this.layerDrag.draw();
                this.isExternalDragged = false;
            }
        }
    }
};