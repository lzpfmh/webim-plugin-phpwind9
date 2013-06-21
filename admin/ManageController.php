<?php

defined('WEKIT_VERSION') or exit(403);

Wind::import('APPS:admin.library.AdminBaseController');

/**
 * 后台访问入口
 *
 * generated by phpwind.com
 */
class ManageController extends AdminBaseController {
	
	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
	}
	
	/* (non-PHPdoc)
	 * @see WindController::run()
	 */
	public function run() {
		$c = Wekit::C('app_webim');
		$conf = array(
			'isopen' => '1',
			'domain' => 'test',
			'apikey' => 'test',
			'host' => 'localhost',
			'port' => '8000',
			'local' => 'zh-CN',
			'emot' => 'default',
			'opacity' => '80',
			'theme' => 'base',
			'show_realname' => '0',
			'disable_room' => '0',
			'disable_chatlink' => '0',
			'disable_menu' => '1',
		);
		if($c) {
			if($c['webim.isopen'])	$conf['isopen'] = $c['webim.isopen'];
			if($c['webim.domain'])	$conf['domain'] = $c['webim.domain'];
			if($c['webim.apikey'])	$conf['apikey'] = $c['webim.apikey'];
			if($c['webim.host'])	$conf['host'] = $c['webim.host'];
			if($c['webim.port'])	$conf['port'] = $c['webim.port'];
			if($c['webim.local'])	$conf['local'] = $c['webim.local'];
			if($c['webim.emot'])	$conf['emot'] = $c['webim.emot'];
			if($c['webim.opacity']) $conf['opacity'] = $c['webim.opacity'];
			if($c['webim.theme'])	$conf['theme'] = $c['webim.theme'];
			if($c['webim.show_realname'])	$conf['show_realname'] = $c['webim.show_realname'];
			if($c['webim.disable_room'])	$conf['disable_room'] = $c['webim.disable_room'];
			if($c['webim.disable_chatlink'])$conf['disable_chatlink'] = $c['webim.disable_chatlink'];
			if($c['webim.disable_menu'])	$conf['disable_menu'] = $c['webim.disable_menu'];
		}
		$this->setOutput($conf, 'conf');
	}
	
	/**
	 * 应用的设置提交
	 *
	 */
	public function doRunAction() {
		$c = $this->getInput('conf', 'post');
		$config = new PwConfigSet('app_webim');
		$config->set('webim.isopen', $c['isopen'])
		       ->set('webim.domain', $c['domain'])
		       ->set('webim.apikey', $c['apikey'])
		       ->set('webim.host',  $c['host'])
		       ->set('webim.port', $c['port'])
		       ->set('webim.theme', $c['theme'])
		       ->set('webim.emot', $c['emot'])
		       ->set('webim.opacity', $c['opacity'])
		       ->set('webim.local', $c['local'])
		       ->set('webim.show_realname', $c['show_realname'])
		       ->set('webim.disable_room', $c['disable_room'])
		       ->set('webim.disable_chatlink', $c['disable_chatlink'])
		       ->set('webim.disable_menu', $c['disable_menu'])
		       ->flush();
		$this->showMessage("success");
	}
}

?>
