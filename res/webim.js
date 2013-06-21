//custom
(function(webim){
	var path = _IMC.path;
	webim.extend(webim.setting.defaults.data, _IMC.setting );
	var webim = window.webim;
	webim.csrf_token = _IMC.csrf_token;
	webim.defaults.urls = {
		online: "index.php?m=app&app=webim&c=webim&a=online",
		offline: "index.php?m=app&app=webim&c=webim&a=offline",
		message: "index.php?m=app&app=webim&c=webim&a=message",
		presence: "index.php?m=app&app=webim&c=webim&a=presence",
		refresh: "index.php?m=app&app=webim&c=webim&a=refresh",
		status: "index.php?m=app&app=webim&c=webim&a=status"
	};
	webim.setting.defaults.url = "index.php?m=app&app=webim&c=webim&a=setting";
	webim.history.defaults.urls = {
		load: "index.php?m=app&app=webim&c=webim&a=history",
		clear: "index.php?m=app&app=webim&c=webim&a=clearHistory",
		download: "index.php?m=app&app=webim&c=webim&a=downloadHistory"
	};
	webim.room.defaults.urls = {
		member: "index.php?m=app&app=webim&c=webim&a=members",
		join: "index.php?m=app&app=webim&c=webim&a=join",
		leave: "index.php?m=app&app=webim&c=webim&a=leave"
	};
	webim.buddy.defaults.url = "index.php?m=app&app=webim&c=webim&a=buddies";
	webim.notification.defaults.url = "index.php?m=app&app=webim&c=webim&a=notifications";

	webim.ui.emot.init({"dir": path + "/images/emot/default"});
	var soundUrls = {
		lib: path + "/assets/sound.swf",
		msg: path + "/assets/sound/msg.mp3"
	};
	var ui = new webim.ui(document.body, {
		imOptions: {
			jsonp: _IMC.jsonp
		},
		soundUrls: soundUrls
	}), im = ui.im;

	if( _IMC.user ) im.user( _IMC.user );
	//TODO: if( _IMC.menu ) ui.addApp("menu", { "data": _IMC.menu } );
	if( _IMC.enable_shortcut ) ui.layout.addShortcut( _IMC.menu );

	ui.addApp("buddy", {
		is_login: _IMC['is_login'],
		loginOptions: _IMC['login_options']
	} );
	TODO: ui.addApp("room");
	ui.addApp("notification");
	ui.addApp("setting", {"data": webim.setting.defaults.data});
	if( !_IMC.disable_chatlink )ui.addApp("chatlink", {
		link_href: [/u.php\?action=show&uid=(\d+)$/i, /u.php\?uid=(\d+)$/i, /mode.php\?m=o&space=1&q=user&u=(\d+)/, /mode.php\?m=o&q=user&uid=(\d+)/i, /mode.php\?m=o&q=user&u=(\d+)/i],
		space_href: [],
		off_link_class: /gray/
	});
	ui.render();
	_IMC['is_login'] && im.autoOnline() && im.online();
})(webim);
