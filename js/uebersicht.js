var uebersicht = {
    url: "../ajax/uebersicht.php?",
    init : function(){
        var self=this;
        self.render().done(function(html){
      $('#bestelluebersicht').html(html);
      $('.artikel_entfernen').on('click', self.artikel_entfernen);
      $('#uebersicht_liste_leeren').on('click', self.leeren);
      $('#uebersicht_bestaetigen').on('click',function(e){
          e.preventDefault();
          self.bestaetigen();
      });
    });
    },
    artikel_tpl : " {{#artikel}}\
                    <div class='row'>\
                    <div class='span2'>{{name}}</div>\
                    <div class='span2'>{{beschreibung}}</div>\
                    <div class='span2'>{{kategorie}}</div>\
                    <div class='span1'><input class='span1' id='menge_{{art_id}}' type='number' min='0' max='8' step='1' value='1' name='menge_{{art_id}}' /></div>\
                    <div class='span1'>{{&html_preis}}</div>\
                    <div class='span1'><button class='remove-article btn-warning pull-right' data-artikel='{{art_id}}'>-</button></div>\
                    </div>\
                    {{/artikel}}",
    render: function(){
        var self = this, q = $.Deferred(), html, render;
        Mustache.parse(this.artikel_tpl);
        $.getJSON("../ajax/tagesbestellung.php?bestellung=uebersicht", function(data){
            console.log(data);
            if(!data.success){alert("Fehler! Bitte versuchen sie es später nocheinmal"); return true;}
            $.each(data.artikelarray.artikel, function(i,article){
                            if(article.preis != undefined){
                                article.html_preis = "<span class='artikel_preis'>"+parseFloat(article.preis).formatMoney(2,',','.')+"</span>€";
                            }
                            });
            html = "<div class='container'><form>"
            html+= Mustache.render(self.artikel_tpl, data.artikelarray);
            html+= "</form></div>";
            q.resolve(html);
        });
        return q;
    },
    bestaetigen: function(){},
    leeren: function(){},
    nachricht_aendern: function(){},
    artikel_entfernen: function(){
        
    }
};


