var administration = {
	user_tpl: "{{#user}}\
<div class='row-fluid admin_user_einzel'>\
	<div class='span2 user_name'>{{name}}</div>\
	<div class='span4 user_email'>{{email}}</div>\
	<div class='span1'>{{{online}}}</div>\
	<div class='span3'><input type='checkbox' class='admin_user_admin' {{idAdminChecked}} id='{{user_id}}' data-user='{{user_id}}' /></div>\
	<div class='span2'><button class='btn btn-danger admin_user_delete' data-user='{{user_id}}'>Löschen</button></div>\
</div>\
{{/user}}",
	order_tpl: "\
{{#user}}\
	<div class='row-fluid'><div class='span12 text-center'>{{name}} -- {{{gesamtPreis}}}</div></div>\
	<div class='row-fluid'>\
		{{#artikel}}\
			<div class='span5 artikel-einzel'>\
				<p class='artikel-name'> {{ Artikelname }} </p> \
				<p class='artikel-preis formatEuro'> {{ Artikelpreis }} </p>\
			</div>\
		{{/artikel}}\
	</div>\
	<div class='row-fluid'>\
		<div class='span3 pull-right text-right>'\
			<button class='btn btn-inverse admin_bestellung_delete' data-order='{{ebest_id}}>Bestellung löschen</button>'\
		</div>\
	</div>\
{{/user}}\
",
	init: function(){
		var self = this;
		this.get_data().done(function(users, orders){
			var html, user_html, order_html, trigger;
			Mustache.parse(self.user_tpl);
			Mustache.parse(self.order_tpl);
			user_html = Mustache.render(self.user_tpl, {user: users});
			order_html = Mustache.render(self.order_tpl, {user : orders});
			$('#administration_nutzer').html(user_html);
			$('#administration_bestellungen').html(order_html);
			$(".admin_user_admin").bootstrapSwitch();
			$('.formatEuro').each(function(i,item){
				var number = $(item).text()
				$(item).html(number.formatMoney(2,',','.') + "€");
			});
			$('.admin_bestellung_delete').on('click', function(e){self.rm_einzel(e, this);});
			$('.admin_user_admin').on('change', function(e){
				if($(this).prop('checked')){
					self.user_admin($(this).data('user'));
				} else {
					self.user_no_admin($(this).data('user'));
				}
			});
			$('.admin_user_delete').on('click', function(e){
				self.rm_user(e, this);
			});
		});
		
		$('#admin_bestellung_submit').on('click', function(e){
			self.bestellung_schliessen(e);
		});
		$('#admin_bestellung_clear').on('click', function(e){
			self.bestellung_ganz_loeschen(e);
		});
		$('#admin_bestellung_print').on('click', function(e){
			self.bestellung_schliessen_print(e);
		});
	},
	get_data: function(){
		var url, users, orders, orderObj, out, q = $.Deferred(), self = this;
		url = "ajax/administration.php?admin=schauschau";
		$.getJSON(url,function(datablock){
			if(datablock.success){
				users = datablock.users;
				orders = datablock.orders;
				$.each(users, function(i,user){
					user.online = user.loggedIn === 'false' ? "<span class='offline'>&nbsp;</span>" :  "<span class='online'>&nbsp;</span>";
					user.idAdminChecked = user.admin === 1 ? "checked" : ""; 
				});
				q.resolve(users, orders);
			}
		});
		return q;
	},
	rm_user: function(e, obj){
		BootstrapDialog.confirm({
			title: 'Achtung!',
			message : "User " + $(obj).closest('.admin_user_einzel').find('user_name').text() + " wirklich löschen?",
			type: BootstrapDialog.TYPE_WARNING,
			btnOKLabel: "Ja, der kriegt nix!",
			btnCancelLabel: "Nein",
			btnOKClass: "btn-inverse",
			callback : function(result){
				if(result){
					var url = "ajax/administration.php?admin=dennichtmehr&uid="+$(obj).data('user');
					$.getJSON(url, function(data){
						data.success ? 
						BootstrapDialog.alert('User '+ $(obj).closest('.admin_user_einzel').find('user_name').text() + ' entfernt. <br> Der kriegt keinen döner mehr.') 
						: BootstrapDialog.alert('Da ist was schief gegangen. Bitte später nochmal versuchen'); })
				} else {
					this.close();
				}
		}
		});
	},
	rm_einzel: function(e, item){
		BootstrapDialog.confirm({
			title: 'Achtung!',
			message : "Bestellung wirklich löschen?",
			type: BootstrapDialog.TYPE_WARNING,
			btnOKLabel: "Ja",
			btnCancelLabel: "Nein",
			btnOKClass: "btn-inverse",
			callback : function(result){
				if(result){
					var url = "ajax/administration.php?admin=dasnicht&ebestid="+$(obj).data('order');
					$.getJSON(url, function(data){
						data.success ? 
						BootstrapDialog.alert('Bestellung gelöscht.') 
						: BootstrapDialog.alert('Da ist was schief gegangen. Bitte später nochmal versuchen'); })
				} else {
					this.close();
				}
		}
		});
	},
	user_admin: function(id){
		var url = "ajax/administration.php?admin=deristtoll&uid="+id;
			$.getJSON(url, function(data){
				if(data.success != true){
					$('#'+id).prop('checked',false);
				}
			});
	},
	user_no_admin: function(id){
		var url = "ajax/administration.php?admin=nichtmehrtoll&uid="+id;
			$.getJSON(url, function(data){
				if(data.success != true){
					$('#'+id).prop('checked',true);
				}
			});
	},
	bestellung_loeschen: function(e){},
	bestellung_schliessen: function(e){},
	bestellung_schliessen_print: function(e){}
};