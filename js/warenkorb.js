var WARENKORB = {
    korb : [],
    add: function(id){
        this.korb.push(id);
    },
    remove: function(id){
       this.korb.splice(this.korb.indexOf(id));
    },
    show: function(){
        var urldata, url, def = $.Deferred();
        urldata = JSON.stringify({art_ids: this.korb});
        url= "/ajax/artikel.php?";
        $.getJSON(url+urldata, function(data){
            if(data !== undefined){
                if(data.success == true){
                    def.resolve(data.artikel);
                } else {
                    def.resolve(false);
                }
            }
        });
        return def;
    },
    confirm: function(){
        var urldata, url, def = $.Deferred();
        urldata = JSON.stringify({art_ids: this.korb});
        url= "/ajax/tagesbestellung.php?";
        $.getJSON(url+urldata, function(data){
            if(data !== undefined){
                def.resolve(data.success);
            }
        });
        return def;
    },
    
    init: function(){}
    
};
