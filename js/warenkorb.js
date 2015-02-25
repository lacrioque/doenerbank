var WARENKORB = function(){
    var icon, inhalt, kaufen, tpl_warenkorb, kasse, korb, confirm, show;
    if(sessionStorage.getItem('warenkorb') !== null){
        korb = sessionStorage.getItem('warenkorb').split(',');
    } else { 
        korb = []; 
    }
    
    tpl_warenkorb = "{{#artikel}}<div class='warenkorb_artikel'><p>Name : {{name}} <button class='btn-warning pull-right'>-</button></p><p>Preis: {{&preis}}</p></div>{{/artikel}} {{^artikel}}<div class='warenkorb-artikel leer'>Keinen Hunger?</div>{{/artikel}}";
    kaufen = "<button id='kaufen' class='btn-primary'>Bestellen</button>";
    icon = $('<span class="warenkorb-img"><img src="/img/warenkorb.png" alt="Warenkorb - Icon" /></span>');
     show = function(){
            console.log('show');
            var urldata, url, def = $.Deferred();
            urldata = korb.join('|')
            url= "/ajax/artikel.php?artikel=einige&art_ids=";
            console.log(url+urldata);
            $.getJSON(url+urldata, function(data){
                if(data !== undefined){
                    if(data.success == true){
                        def.resolve(data.artikelarray);
                    } else {
                        def.resolve(false);
                    }
                }
            });
            return def;
        };
        confirm = function(){
            var urldata, url, def = $.Deferred();
            urldata = JSON.stringify({art_ids: this.korb});
            url= "/ajax/tagesbestellung.php?";
            $.getJSON(url+urldata, function(data){
                if(data !== undefined){
                    def.resolve(data.success);
                }
            });
            return def;
        };
    return {
        init: function(){
           $('#warenkorb-icon-container').html(icon);
           $('#warenkorb-icon-container').on('click', function(){
                show().done(function(articles){
                    var artikel = Mustache.render(tpl_warenkorb, articles);
                    $('#warenkorb_vorschau').html(artikel + kaufen);
                    BootstrapDialog.show({
                        message: artikel,
                        buttons: [
                        {
                            label: "Zurück",
                            cssClass: "btn-warning",
                            action: function(dialogItself){dialogItself.close();}
                        }, 
                        {
                            label: "Kaufen",
                            cssClass: "btn-primary",
                            action: function(){
                                confirm().done(
                                        function(bool){
                                    bool ? location.href = 'index.php?view=uebersicht' : null;
                                        }
                                        );
                            }
                        }
                        ]
                    });
                });
            });
        },
        inhalt : function(){
            return korb.length;
        },
        add : function(id){
            korb.push(id);
            sessionStorage.setItem('warenkorb', korb.join(','));
            console.log(korb);
        },
        remove : function(id){
           korb.splice(this.korb.indexOf(id));
           sessionStorage.setItem('warenkorb', korb.join(','));
        }
    };
};