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
 
$(document).ready(function(){
var warenkorb = new WARENKORB();
warenkorb.init();
$('#register_btn').on('click', function(e){
   e.preventDefault();
   Register.new($('#username').val(), $('#password').val());
});

$('.onclick_false').on('click', function(){return false;});

$('#triggerling').on('warenkorb_open', function(){
        console.log('waremkorb opened');
        $('.remove-article').on('click', function(e){
            console.log('remove-triggered');
            warenkorb.remove($(this).data('artikel'));
            $(this).closest('.warenkorb_artikel').fadeOut(400);
        });
    });

$('#triggerling').on('artikel_geladen', function(){
    console.log('triggerling: "artikel_geladen"');
    $('.artikel-bestellen').on('click',function(e){
        e.preventDefault();
        console.log($(this).data('artikelnummer'));
        warenkorb.add($(this).data('artikelnummer'));
    });
    $('.artikel-abbestellen').on('click',function(e){
        e.preventDefault();
        console.log($(this).data('artikelnummer'));
        warenkorb.remove($(this).data('artikelnummer'));
    });
});

});

