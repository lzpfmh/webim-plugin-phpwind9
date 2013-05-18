<?php
defined('WEKIT_VERSION') or exit(403);
/**
 * 全局底部
 *
 * @author 杭州巨鼎信息技术有限公司 <webim20@gmail.com>
 * @copyright http://www.webim20.cn
 * @license http://www.webim20.cn
 */
class App_Webim_FooterDo {
	
	/**
	 * 嵌入js脚本
	 */
	public function createHtml() {
		echo "<script type=\"text/javascript\" src=\"http://blog.webim20.cn/webim/custom.js.php?domain=webim20.cn\"></script>"; 
	}
}

?>
