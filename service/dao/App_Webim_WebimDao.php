<?php
defined('WEKIT_VERSION') or exit(403);
/**
 * App_Webim_WebimDao - dao
 *
 * @author 杭州巨鼎信息技术有限公司 <webim20@gmail.com>
 * @copyright http://www.webim20.cn
 * @license http://www.webim20.cn
 */
class App_Webim_WebimDao extends PwBaseDao {
	
	/**
	 * table name
	 */
	protected $_table = 'app_webim_table';
	/**
	 * primary key
	 */
	protected $_pk = 'id';
	/**
	 * table fields
	 */
	protected $_dataStruct = array('id' /*, 'field' */);
	
	public function add($fields) {
		return $this->_add($fields, true);
	}
	
	public function update($id, $fields) {
		return $this->_update($id, $fields);
	}
	
	public function delete($id) {
		return $this->_delete($id);
	}
	
	public function get($id) {
		return $this->_get($id);
	}
}

?>