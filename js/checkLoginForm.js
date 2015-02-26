function checkLoginFields(){
    var password,username,login,check,min;
    check = false;
    password = $('#password').val();
    username = $('#username').val();
    if(password.length > 7 && username.length > 4){
        check = true;
    }
    return check;
}

$(document).ready(function(){
    $('#username').on('keyup', function(event){
       checkLoginFields() ? $('.login_btn').prop('disabled',false): $('#login_btn').prop('disabled',true);
    });
    $('#password').on('keyup', function(event){
       checkLoginFields() ? $('.login_btn').prop('disabled',false): $('#login_btn').prop('disabled',true);
    });
});