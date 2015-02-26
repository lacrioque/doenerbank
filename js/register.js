var Register = {
    url : "ajax/register.php",
    extradata : {
        timelock: false,
        register: true
        },
    popup : $('<div></div>'),
    css : {
        position: 'absolute',
        'box-sizing': 'border-box',
        left: '1%',
        right: '1%',
        top: '1%',
        bottom: '1%',
        height: '98%',
        width: '98%',
        'z-index': 1000,
        border: "none",
        'box-shadow': "2px 2px 4px hsla(263, 35%, 85%, 0.6)",
        'background': 'hsla(263, 25%, 85%, 0.6)',
        'padding': '10%',
        'font-size': '120%',
        'text-align': 'center'
        },
    checktime : function(){
        var timelock = localStorage.getItem('timelock')
        if (timelock == null || timelock == undefined){ return false; }
        return  timelock < (+new Date()) ? true : false ;
    },
    new : function(user,pass){
        
        var self = this, data = this.extradata, popup_active = this.popup, durl;
        
        popup_active.css(this.css);
        popup_active.html('<p style="margin:auto, text-align: center">Vielen Dank für die Registrierung</p>\
                    <p style="margin:auto, text-align: center">Bitte warten sie einen kurzen Moment</p>\
                        ')
        $('body').append(popup_active);
        data.user = user;
        data.pass = pass;
        data.timelock = this.checktime();
        durl = this.url + "?" + $.param(data);
        $.getJSON(durl, function(data){
            console.log(data);
            if(data.success !== undefined){
                popup_active.html("<p>" +data.message + "</p>");
                if(data.success === true){
                    setTimeout(function(){location.reload();}, 2000);
                } else {
                    localStorage.setItem('timelock', (5000+new Date()));
                    setTimeout(function(){location.reload();}, 2000);
                }
            }
         });
    }
};
