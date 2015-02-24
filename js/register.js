var Register = {
    url : "ajax/register.php",
    extradata : {
        timelock: false,
        register: true
        },
    popup : $('<div></div>'),
    css : {
        position: 'absolute',
        left: '20%',
        right: '20%',
        top: '20%',
        bottom: '20%',
        height: '60%',
        width: '60%',
        'z-index': 1000,
        border: "none",
        'border-radius':"10%",
        'box-shadow': "2px 2px 4px hsla(263, 35%, 85%, 0.6)",
        'background': 'hsla(263, 25%, 85%, 0.6)'
        },
    checktime : function(){
        var timelock = localStorage.getItem('timelock')
        if (timelock == null || timelock == undefined){ return false; }
        return  timelock < (+new Date()) ? true : false ;
    },
    new : function(user,pass){
        this.popup.css(this.css);
        this.popup.html('<p style="margin:auto, text-align: center">Vielen Dank für die Registrierung</p>\
                    <p style="margin:auto, text-align: center">Bitte warten sie einen kurzen Moment</p>\
                        ')
        $('body').append('popup');
        var data = {};
        var self = this;
        data.merge(this.extradata);
        data.user = user;
        data.pass = pass;
        data.timelock = this.checktime();
        var durl = url + $.param(data);
        $.getJSON(durl, function(data){
            if(data.success !== undefined){
                self.popup.html = data.message;
                if(data.success === true){
                    setTimeout(function(){location.reload();}, 1000);
                } else {
                    localStorage.setItem('timelock', (5000+new Date()));
                    setTimeout(function(){location.reload();}, 1000);
                }
            }
         });
    }
};


$('#register_btn').on('click', function(e){
   e.preventDefault();
   Register.new($('#user').val(), $('#password').val());
});