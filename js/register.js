var Register = {
    url : "ajax/register.php",
    extradata : {
        timelock: false,
        register: true
        },
    checktime : function(){
        var timelock = localStorage.getItem('timelock')
        if (timelock == null || timelock == undefined){ return false; }
        return  timelock < (+new Date()) ? true : false ;
    },
    new : function(user,pass){
        var self = this, data = this.extradata, popup_active = this.popup, durl, registerDialog;
        registerDialog = "<form class='container-fluid'>\
        <div class='row-fluid passwortfehler nichtgleich' style='display: none;' >\
            <p class='span10 text-error'>Passwörter stimmen nicht überein</p>\
        </div>\
        <div class='row-fluid passwortfehler zukurz' style='display: none;' >\
            <p class='span10 text-error'>Passwort zu kurz. Bitte mindestens 8 Zeichen.</p>\
        </div>\
        <div class='row-fluid roboteralarm' style='display: none;' >\
            <p class='span10 text-error'>Bist du wirklich ein Roboter?.</p>\
        </div>\
        <div class='row-fluid' >\
            <label class='span4' for='register_name'>Nutzername</label>\
            <input name='name' id='register_name' class='span8' disabled value='{{name}}' />\
        </div>\
        <div class='row-fluid' > \
            <label class='span4' for='register_pass'>Passwort</label>\
            <input name='passwort' id='register_pass' class='span8' disabled type='password' value='{{passwort}}' />\
        </div>\
        <div class='row-fluid'>\
            <label class='span4' for='register_pass_confirm'>Passwort bestätigen</label>\
            <input name='passwort' id='register_pass_confirm' class='span8'  type='password' />\
        </div>\
        <div class='row-fluid' > \
            <label class='span4' for='register_email'>Email Adresse</label>\
            <input name='email' type='email' id='register_email' class='span8' />\
        </div>\
        <div class='row-fluid' > \
            <label class='span4' for='human'>Bist du ein Roboter?</label>\
            <input name='robot' id='iamnotahuman' type='checkbox' checked class='span8' />\
        </div>\
        </form>";
        var dialog = BootstrapDialog.show({
            message: Mustache.render(registerDialog,{name: user, passwort: pass}),
            title: "Registrieren",
            buttons: [
                {
                    label: "Registrieren",
                    cssClass: "btn btn-primary",
                    action: function(dialogItself){
                        var form_cont = dialogItself.getModalBody();
                        if($('#iamnotahuman').prop('checked') === true){
                            $('.roboteralarm').fadeIn(400);
                            setTimeout(function(){$('.roboteralarm').fadeOut(800);}, 1500);
                            return false;
                        }
                        if($('#register_pass_confirm').val() !== $('#register_pass').val()){
                            $('.passwortfehler.nichtgleich').fadeIn(400);
                            setTimeout(function(){$('.passwortfehler.nichtgleich').fadeOut(800);}, 1500);
                            return false;
                        }
                        if($('#register_pass').val().length < 8) {
                            $('.passwortfehler.zukurz').fadeIn(400);
                            setTimeout(function(){$('.passwortfehler.zukurz').fadeOut(800);}, 1500);
                            return false;
                        }
                        data.email = $('#register_email').val();
                        data.user = $('#register_name').val();
                        data.pass = $('#register_pass').val();
                        data.timelock = self.checktime();
                        durl = self.url + "?" + $.param(data);
                        $.getJSON(durl, function(data){
                            if(data.success !== undefined){
                                 form_cont.find('div').html(data.message);
                                if(data.success === true){
                                    setTimeout(function(){location.reload();}, 2000);
                                } else {
                                    localStorage.setItem('timelock', (5000+new Date()));
                                    setTimeout(function(){location.reload();}, 2000);
                                }
                            }
                         });
                    },
                },
                {
                    label: "Bearbeiten",
                    cssClass: "btn btn-warn",
                    action: function(dialogItself){
                        var form_cont = dialogItself.getModalBody();
                        form_cont.find('input').prop('disabled',false);
                    }
                },
                {
                    label: "Abbrechen",
                    cssClass: "btn btn-warn",
                    action: function(dialogItself){
                        dialogItself.close();
                    }
                }
            ]
        });
    }
};
