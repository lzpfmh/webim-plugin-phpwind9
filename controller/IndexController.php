<?php

defined('WEKIT_VERSION') or exit(403);

/**
 * 前台入口
 *
 * generated by phpwind.com
 */
class IndexController extends PwBaseController {

	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
	}
	
	public function run() {
		$imconf = Wekit::C('app_webim');
		if($this->loginUser->isExists() and $imconf and $imconf['webim.isopen'] == '1') {
			
			header("Content-type: application/javascript");
			/** set no cache in IE */
			header("Cache-Control: no-cache");
			$setting = json_encode( $this->setting($this->loginUser->uid) );
			$imuser = $this->imuser($this->loginUser->uid);
			$imuser = json_encode( $imuser );
			if ( !$conf['disable_menu'] ) {
				$menu = json_encode( array() ); //$this->_service().getMenu() webim_get_menu() );
			}
			$windToken = Wind::getComponent('windToken');
			$csrf_token = $windToken->getToken('csrf_token');
	
			$s_is_login = "";
			$s_login_opt = json_encode( array("notice" => "使用phpwind帐号登录", "questions" => null) );
			$imuser_json = $imuser ? $imuser : '""';
			$setting_json = $setting ? $setting : '""';
			$menu_json = $imconf['webim.disable_menu'] ? $menu : '""';
			$disable_link = $imconf['webim.disable_chatlink'] ? "1" : "";
			$enable_shortcut = $imconf['webim.enable_shortcut'] ? "1" : "";
			$disable_menu = $imconf['webim.disable_menu'] ? "1" : "";

			$theme = $imconf['webim.theme'];
			$local = $imconf['webim.local'];
			
			//window.location.href.indexOf("webim_debug") != -1 ? "" : ".min"
			$script=<<<EOF

			var _IMC = {
				production_name: 'phpwind',
				version: '1.0',
				path: 'src/extensions/webim/res/',
				is_login: '1',
				login_options: $s_login_opt,
				csrf_token: '$csrf_token',
				user: $imuser_json,
				setting: $setting_json,
				menu: $menu_json,
				disable_chatlink: '$disable_chatlink',
				enable_shortcut: '$enable_shortcut',
				disable_menu: '$disable_menu',
				theme: '$theme',
				local: '$local',
				min: ''
			};
			_IMC.script = window.webim ? '' : ('<link href="' + _IMC.path + 'webim.' + _IMC.production_name + _IMC.min + '.css?' + _IMC.version + '" media="all" type="text/css" rel="stylesheet"/><link href="' + _IMC.path + 'themes/' + _IMC.theme + '/jquery.ui.theme.css?' + _IMC.version + '" media="all" type="text/css" rel="stylesheet"/><script src="' + _IMC.path + 'webim.' + _IMC.production_name + _IMC.min + '.js?' + _IMC.version + '" type="text/javascript"></script><script src="' + _IMC.path + 'i18n/webim-' + _IMC.local + '.js?' + _IMC.version + '" type="text/javascript"></script>');
			_IMC.script += '<script src="' + _IMC.path + 'webim.js?' + _IMC.version + '" type="text/javascript"></script>';
			document.write( _IMC.script );

EOF;
			echo $script;

		}
		exit;
	}

	private function imuser($uid) {
		$user = $this->_service()->getUser($uid);		
		return (object)array(
			'uid' => $uid,
			'id'  => $uid,
			'nick' => $this->_service()->userNick($user),
			'url' => $this->_service()->userSpaceUrl($uid), 
			'pic_url' => $this->_service()->userAvatar($uid),
			'default_pic_url' =>  '',
			'show' => "unavailable",
			'status' => $user['profile']);
	}

	private function setting($uid) {
		return $this->_service()->getSetting($uid);
	}
	
	private function _service() {
		return Wekit::load('EXT:webim.service.App_Webim');
	}
}

?>
