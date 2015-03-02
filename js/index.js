/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
 
 var LOGIN = function(){
   $('#register_btn').on('click', function(e){
   e.preventDefault();
   Register.new($('#username').val(), $('#password').val());
   return false;
});
 };
 
var MAIN = function(){
var warenkorb = new WARENKORB();
warenkorb.init();
$('#logout').on('click',function(e){
    BootstrapDialog.confirm("Wirklich abmelden?", function(bool){
        if(bool){ sessionStorage.clear(); location.href = 'logout.php'; }
        else {
            return false;
        }
    });
                                
});
$('.onclick_false').on('click', function(){return false;});

$('#triggerling').on('artikel_reload', function(){
    console.log('Triggerling: artikel_reload');
    Artikel.refresh();
});
$('#triggerling').on('remove_from_warenkorb', function(artikelnummer){
	warenkorb.remove(artikelnummer);
});

$('#triggerling').on('warenkorb_open', function(){
        $('.remove-article').on('click', function(e){
            warenkorb.remove($(this).data('artikel'));
            var preis_item = $(this).closest('.warenkorb_artikel').find('.artikel_preis'),
            preis = (preis_item.html()).replace(',','.'),
            gesamtPreis = ($('#gesamtPreis_warenkorb').html()).replace(',','.'),
            newGesamtPreis = gesamtPreis-preis;
            $('#gesamtPreis_warenkorb').html(newGesamtPreis.formatMoney(2,',','.'));
            $(this).closest('.warenkorb_artikel').fadeOut(400);
            var artikel_id = "#artikel_"+$(this).data('artikel');
            Artikel.artikel_aus_warenkorb(artikel_id);
           });
    });

$('#triggerling').on('artikel_geladen', function(){
    warenkorb.refresh_artikel();
    $('.artikel-bestellen').on('click',function(e){
        $('#warenkorb').trigger('change');
        e.preventDefault();
        warenkorb.add($(this).data('artikelnummer'));
        var artikel_id = "#artikel_"+$(this).data('artikelnummer');
        Artikel.artikel_in_warenkorb(artikel_id)
    });
    $('.artikel-abbestellen').on('click',function(e){
        $('#warenkorb').trigger('change');
        e.preventDefault();
        warenkorb.remove($(this).data('artikelnummer'));
        var artikel_id = "#artikel_"+$(this).data('artikelnummer');
        Artikel.artikel_aus_warenkorb(artikel_id);
    });
});
$('#warenkorb').trigger('change');
};