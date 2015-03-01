var Artikel = {
    object : {},
    render: function(jObject){
        this.object = jObject;
        var self = this, 
            html = "", out, kategorien, container;
    
            Mustache.parse(this.tpl);
            Mustache.parse(this.tpl_kategorie);
    
        this.load().done(function(artikel){
            if(artikel == false || artikel == ""){
                jObject.html(self.tpl_noartikel);
            } else {
            kategorien = artikel.kategorien;
            delete(artikel.kategorien);
            $.each(kategorien, function(i,kategorie){
                html += "<div class='container-fluid'>"
                html += Mustache.render(self.tpl_kategorie, {kategorie: kategorie});
                html += '<div class="row" >'
                for (var i = 0, l = artikel[kategorie].length, kat_art = artikel[kategorie], eArtikel; i<l; i++){
                    eArtikel = kat_art[i];
                    html += Mustache.render(self.tpl, eArtikel);
                }
                html += '</div>';
                html += '</div>';
            });
            jObject.html(html);
            
            $('#triggerling').trigger('artikel_geladen');
        }
        });
    },
    load: function(){
        var artQ = $.Deferred(), art = {kategorien : []}, self = this;
        $.ajax({
            url: self.url,
            dataType: 'json',
            success: function(data, status, jqXHR){
                $.each(data, function(row,article){
                    article.preis = "<span class='art_preis'>" + parseFloat(article.preis).formatMoney(2,',','.') + "€</span>";
                    art[article.kategorie] = art[article.kategorie] || [];
                    art[article.kategorie].push(article);
                    art.kategorien.indexOf(article.kategorie) === -1 ?  art.kategorien.push(article.kategorie) : null;
                });
            },
            error: function(jqXHR, status, error){
                console.log(status +": " + error);
            },
            complete: function(){artQ.resolve(art);}
        });
        return artQ;
    },
    artikel_in_warenkorb: function(artObj){
        $(artObj).addClass('hidden');
	$(artObj).closest('.artikel-einzel').find('.artikel-abbestellen').removeClass('hidden');
        $('#warenkorb').trigger('change');
    },
    artikel_aus_warenkorb: function(artObj){
        $(artObj).closest('.artikel-einzel').find('.artikel-abbestellen').addClass('hidden');
	$(artObj).removeClass('hidden');
        $('#warenkorb').trigger('change');
    },
    url: "/ajax/artikel.php?artikel=alle",
    container: $('#artikelliste'),
    tpl_debug: "{{#artikel}}{{{.}}}{{/artikel}}",
    tpl: "<div class='span3 artikel-einzel'><p class='artikel-name'> {{ name }} </p> <p class='artikel-beschreibung'> {{ &beschreibung }}</p><p class='artikel-preis'> {{ &preis }} </p> <button class='btn-primary btn-default artikel-bestellen artikel-interaktion' data-artikelnummer='{{ art_id }}' id='artikel_{{ art_id }}'><span>Will ich</span><span class='glyphicon glyphicon-shopping-cart' aria-hidden='true'></span></button><button class='artikel-interaktion btn-primary btn-default artikel-abbestellen hidden' data-artikelnummer='{{ art_id }}' id='artikel_rm_{{ art_id }} '><span>Will ich nicht mehr</span><span class='glyphicon glyphicon-shopping-cart' aria-hidden='true'></span></button></div>",
    tpl_kategorie: "<div class='row'><h3> {{ kategorie }} </h3></div>",
    tpl_noartikel: "<div class='span6 offset3 text-error'> Leider sind derzeit keine Artikel im System </div>",
    tpl_keine_artikel: function(){ return $('tpl_no_artikel').html();},
    refresh: function(){
        if(this.object === undefined){
            location.refresh();
        }
        this.render(this.object);
    },
    render_single: function(){},
    load_single: function(){}
};
