var uebersicht = {
    url: "../ajax/uebersicht.php?",
	wareninhalt : [],
    init : function(){
        var self=this;
        self.render().done(function(html){
      $('#bestelluebersicht').html(html);
      $('.artikel_entfernen').on('click', self.artikel_entfernen);
      $('#uebersicht_liste_leeren').on('click', self.leeren);
      $('#uebersicht_bestellung_bemerkung').on('keyup', function(){ self.nachricht_aendern(this,self)});
      $('#uebersicht_bestaetigen').on('click', function(e){ e.preventDefault(); self.bestaetigen(this,self)});
    });
    },
    artikel_tpl : " {{#artikel}}\
                    <div class='row-fluid uebersicht_artikel_einzel'>\
                    <div class='span2'>{{name}}<input type='hidden' name='{{art_id}}' id='artikel_{{art_id}}' value='' /></div>\
                    <div class='span2'>{{beschreibung}}</div>\
                    <div class='span2'>{{kategorie}}</div>\
                    <div class='span1'><input class='span12' id='menge_{{art_id}}' type='number' min='0' max='8' step='1' value='1' name='menge_{{art_id}}' /></div>\
                    <div class='span1'>{{&html_preis}}</div>\
					<div class='span3'><input type='text' id='bemerkung_{{art_id}}' name='bemerkung_{{art_id}}' placeholder='Sonderwünsche?' /></div>\
                    <div class='span1'><button class='artikel_entfernen btn-warning pull-right' data-artikel='{{art_id}}'>-</button></div>\
                    </div>\
                    {{/artikel}}\
                    {{^artikel}}\
					<div class='row-fluid'>\
                    <div class='span10 offset1 text-center'>Erst mal Artikel auswählen!</div>\
                    </div>\
                    {{/artikel}}",
    render: function(){
        var self = this, q = $.Deferred(), html, render;
        Mustache.parse(this.artikel_tpl);
        $.getJSON("../ajax/tagesbestellung.php?bestellung=uebersicht", function(data){
            if(!data.success){alert("Fehler! Bitte versuchen sie es später nocheinmal"); return true;}
            $.each(data.artikelarray.artikel, function(i,article){
                            if(article.preis != undefined){
                                article.html_preis = "<span class='artikel_preis'>"+parseFloat(article.preis).formatMoney(2,',','.')+"</span>€";
                            }
							self.wareninhalt.push(article);
                            });
            html = "<div class='container'><form id='bestellung_ubersicht_form'>"
            html+= Mustache.render(self.artikel_tpl, data.artikelarray);
            html+= "</form></div>";
			$('#gesamtpreis_uebersicht').html(data.gesamtPreis.formatMoney(2,',','.'))
            q.resolve(html);
        });
        return q;
    },
    bestaetigen: function(button, self){
		var url= "/ajax/tagesbestellung.php?bestellung=bestaetigen", urldata, message, message_tpl, artikel = [];
		message_tpl = "{{#artikel}}\
							<li>{{menge}}x {{name}} </li>\
						{{/artikel}}"
		$.each(self.wareninhalt, function(i,item){
			var id = item.art_id;
			artikel.push({art_id: id, name: item.name, menge: $('#menge_'+id).val(), bemerkung: $('#bemerkung_'+id).val()});
		});
		message	 = "<div>Die Bestellung wurde gespeichert.</div><ul>";
		message += Mustache.render(message_tpl, {artikel: artikel});
		message += "</ul>";
		urldata = {artikel: JSON.stringify(artikel)};
                $.getJSON(url, urldata, function(data){
			if(data.success == true){
				BootstrapDialog.alert({
					message: message
				});
				location.href = "index.php";
			}
		});
	},
    leeren: function(e){
		e.preventDefault();
			BootstrapDialog.show({
				message: "Wirklich alle Artikel zurücksetzen?",
				closable: false,
				buttons: [
					{
						label: "Ja",
						action: function(){
							var url= "/ajax/artikel.php?artikel=clear";
							$.getJSON(url, function(data){
								if(data !== undefined){location.href = "index.php?view=order";}
							});
						}
					},
					{
						label: "Nein",
						action: function(dialogItself){
							dialogItself.close();
						}
					}
				]
			});
	},
    artikel_entfernen: function(e){
        e.preventDefault();
			$('#triggerling').trigger('remove_from_warenkorb', [$(this).data('artikel')]);
            var preis_item = $(this).closest('.uebersicht_artikel_einzel').find('.artikel_preis'),
            preis = (preis_item.html()).replace(',','.'),
            gesamtPreis = ($('#gesamtpreis_uebersicht').html()).replace(',','.'),
            newGesamtPreis = gesamtPreis-preis;
            $('#gesamtpreis_uebersicht').html(newGesamtPreis.formatMoney(2,',','.'));
            $(this).closest('.uebersicht_artikel_einzel').fadeOut(400);
			$('#warenkorb').trigger('change');
    }
};


