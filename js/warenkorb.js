var WARENKORB = function(){
    var icon, inhalt, kaufen, getLast, tpl_preis, tpl_warenkorb, kasse, korb, confirm, clear, show;
    if(sessionStorage.getItem('warenkorb') !== null){
        korb = sessionStorage.getItem('warenkorb').split(',');
        if(korb[0] === "" || korb[0] === "null" || korb === "null"){
            korb = [];
            sessionStorage.setItem('warenkorb',null);
        }
    } else { 
        korb = []; 
        sessionStorage.setItem('warenkorb',null);
    }
    
    tpl_warenkorb = "{{#artikel}}\
<div class='warenkorb_artikel'>\
<p>Name : {{name}} <button class='remove-article btn-warning pull-right' data-artikel='{{art_id}}'>-</button></p>\
<p>Preis: {{&html_preis}}</p>\
</div>{{/artikel}} \
{{^artikel}}<div class='warenkorb-artikel leer'>Keinen Hunger?</div>{{/artikel}}";
    tpl_preis = "<p class='row-fluid'><span class='span4 offest6 pull-right clearfix'>Gesamtpreis: {{&preis}} </span></p>";
    kaufen = "<p class='row-fluid'>\
                <label for'bestellung_bemerkung' class='span3'>Anmerkungen zur Bestellung?</label>\
                <textarea class='span6' id='bestellung_bemerkungen'></textarea></p>";
    icon = $('<span class="warenkorb-img"><img src="/img/warenkorb.png" alt="Warenkorb - Icon" /></span>');
	
	getLast = function(){
		var urldata, url, def = $.Deferred();
            url= "/ajax/tagesbestellung.php?bestellung=letzte";
            $.getJSON(url, function(data){
				if(data.success == true){
					if(data.noBest === true){
						def.resolve({noBest: true});
					} else {
                        def.resolve(data.artikelnr, data.gesamtPreis);
					}
				} else {
					def.resolve(false);
				}
			});
			return def;
	};
	
     show = function(){
            var urldata, url, def = $.Deferred();
            urldata = korb.join('|') == "null" ? "" : korb.join('|');
            url= "/ajax/artikel.php?artikel=einige&art_ids=";
            $.getJSON(url+urldata, function(data){
                if(data !== undefined){
                    if(data.success == true){
                        def.resolve(data);
                    } else {
                        def.resolve(false);
                    }
                }
            });
            return def;
        };
        clear = function(){
            var urldata, url, def = $.Deferred();
            url= "/ajax/artikel.php?artikel=clear";
            $.getJSON(url, function(data){
                if(data !== undefined){
                    def.resolve(data.success);
                }
            });
            return def;
        };
        confirm = function(){
            var urldata, url, def = $.Deferred();
            urldata = korb.join('|');
            url= "/ajax/artikel.php?artikel=confirm&korb=";
            $.getJSON(url+urldata, function(data){
                if(data !== undefined){
                    def.resolve(data);
                }
            });
            return def;
        };
    return {
        init: function(){
            var self = this;
           $('#warenkorb-icon-container').html(icon);
           $('#warenkorb').on('change', function(){
               $(this).html("Artikel im Korb: " + korb.length);
           });
           $('#warenkorb-icon-container').on('click', function(){
					show().done(function(data, gesamtPreis){
						if(data.geschlossen === true){
							BootstrapDialog.alert({message: 'Diese Bestellung ist bereits geschlossen.<br>Morgen gibt es eine neue Möglichkeit.'});
						} else {
						var articles = data.artikelarray;
                        $.each(articles.artikel, function(i,article){
                            if(article.preis != undefined){
                                article.html_preis = "<span class='artikel_preis'>"+parseFloat(article.preis).formatMoney(2,',','.')+"</span>€";
                            }
                        });
                    var artikel = Mustache.render(tpl_warenkorb, articles);
                    artikel += "<script>$('#triggerling').trigger('warenkorb_open');</script>";
                    artikel += Mustache.render(tpl_preis, {gesamtPreis: data.gesamtPreis, preis: function(){return "<span id='gesamtPreis_warenkorb' class='preis'>"+parseFloat(this.gesamtPreis).formatMoney(2,',','.')+"</span>€"}})
                    artikel += korb.length > 0 ? kaufen : ""; 
					
                    BootstrapDialog.show({
                        message: artikel,
                        closable: false,
                        buttons: [
                        {
                            label: "Zurück",
                            cssClass: "btn-warning",
                            action: function(dialogItself){
                                $('#triggerling').trigger('artikel_reload');
                                $('#warenkorb').trigger('change');
                                dialogItself.close();
                            }
                        }, 
                        {
                            label: "Warenkorb leeren",
                            cssClass: "btn-inverse",
                            action: function(dialogItself){
                                sessionStorage.setItem('warenkorb', null);
                                korb = [];
                                clear().done(function(data){
                                    var body = dialogItself.getModalBody();
                                    body.find('div').html(data.message);
                                    $('#triggerling').trigger('artikel_reload');
                                    $('#warenkorb').trigger('change');
                                    //location.reload();
                                    location.href= "index.php?view=order";
                                    dialogItself.close();
                                });
                            }
                        },
                        {
                                label:"Das von letztes Mal",
                                cssClass: "btn-warn",
                                action: function(dialogItself){
                                        dialogItself.close();
                                        $('#triggerling').trigger('von_gestern');
                                }
                        },
                        {
                            label: "Kaufen",
                            cssClass: "btn-primary",
                            action: function(dialogItself){
                                confirm().done(
                                        function(data){
                                            if(data.success){
                                                location.href = 'index.php?view=uebersicht';
                                            } else {
                                                $('#triggerling').trigger('artikel_reload');
                                                $('#warenkorb').trigger('change');
                                                dialogItself.close();
                                            }
                            });
                            }
                        }
                        ]
                    });
						}
                });
			});
        },
		vonGestern: function(){
			var self = this;
			getLast().done(function(lastOrder){
				if(lastOrder.noBest === true){
					BootstrapDialog.alert({message: "Deine Erste Bestellung hier. <br> Noch keine Daten vorhanden."});
				} else {
					$.each(lastOrder, function(i,last){
						self.add(last);
					});
					self.refresh_artikel();
				}
			});
		},
        inhalt : function(){
            return korb.length;
        },
        add : function(id){
            korb.push(id.toString());
            sessionStorage.setItem('warenkorb', korb.join(','));
        },
        remove : function(id){
           korb.splice(korb.indexOf(id));
           if(korb === [""]){ 
               sessionStorage.setItem('warenkorb', null);
               korb = [];
           } else {
               sessionStorage.setItem('warenkorb', korb.join(','));
           }
        },
        refresh_artikel: function(){
            for(var i=0, j=korb.length; i<j; i++){
                var article_id = '#artikel_'+korb[i];
                Artikel.artikel_in_warenkorb($(article_id));
            }
        }
    };
};