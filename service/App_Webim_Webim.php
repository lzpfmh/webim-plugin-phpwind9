<?php
defined('WEKIT_VERSION') or exit(403);
Wind::import('EXT:webim.service.dm.App_Webim_WebimDm');
/**
 * App_Webim_Webim - 数据服务接口
 *
 * @author 杭州巨鼎信息技术有限公司 <webim20@gmail.com>
 * @copyright http://www.webim20.cn
 * @license http://www.webim20.cn
 */
class App_Webim_Webim {
	
	/**
	 * add record
	 *
	 * @param App_Webim_WebimDm $dm
	 * @return multitype:|Ambigous <boolean, number, string, rowCount>
	 */
	public function add(App_Webim_WebimDm $dm) {
		if (true !== ($r = $dm->beforeAdd())) return $r;
		return $this->_loadDao()->add($dm->getData());
	}
	
	/**
	 * update record
	 *
	 * @param App_Webim_WebimDm $dm
	 * @return multitype:|Ambigous <boolean, number, rowCount>
	 */
	public function update(App_Webim_WebimDm $dm) {
		if (true !== ($r = $dm->beforeUpdate())) return $r;
		return $this->_loadDao()->update($dm->getId(), $dm->getData());
	}
	
	/**
	 * get a record
	 *
	 * @param unknown_type $id
	 * @return Ambigous <multitype:, multitype:unknown , mixed>
	 */
	public function get($id) {
		return $this->_loadDao()->get($id);
	}
	
	/**
	 * delete a record
	 *
	 * @param unknown_type $id
	 * @return Ambigous <number, boolean, rowCount>
	 */
	public function delete($id) {
		return $this->_loadDao()->delete($id);
	}
	
	/**
	 * @return App_Webim_WebimDao
	 */
	private function _loadDao() {
		return Wekit::loadDao('EXT:webim.service.dao.App_Webim_WebimDao');
	}
}

?>