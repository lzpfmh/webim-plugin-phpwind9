<?php
defined('WEKIT_VERSION') or exit(403);
/**
 * 后台菜单添加
 *
 * @author 杭州巨鼎信息技术有限公司 <webim20@gmail.com>
 * @copyright http://www.webim20.cn
 * @license http://www.webim20.cn
 */
class App_Webim_ConfigDo {
	
	/**
	 * 获取webim后台菜单
	 *
	 * @param array $config
	 * @return array 
	 */
	public function getAdminMenu($config) {
		$config += array(
			'ext_webim' => array('webim', 'app/manage/*?app=webim', '', '', 'appcenter'),
			);
		return $config;
	}
}

?>