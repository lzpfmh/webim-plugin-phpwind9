<?php
defined('WEKIT_VERSION') or exit(403);
/**
 * App_Webim_WebimDm - 数据模型
 *
 * @author 杭州巨鼎信息技术有限公司 <webim20@gmail.com>
 * @copyright http://www.webim20.cn
 * @license http://www.webim20.cn
 */
class App_Webim_WebimDm extends PwBaseDm {

	protected $id;
	
	/**
	 * @return field_type
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param field_type $id
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	
	/**
	 * set table field value
	 *
	 * @param mixed $field_value
	 */
	public function setField($field_value) {
		$this->_data['field'] = $field_value;
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeAdd()
	 */
	protected function _beforeAdd() {
		// TODO Auto-generated method stub
		//check the fields value before add
		return true;
	}

	/* (non-PHPdoc)
	 * @see PwBaseDm::_beforeUpdate()
	 */
	 protected function _beforeUpdate() {
		// TODO Auto-generated method stub
	 	//check the fields value before update
		return true;
	}
}

?>
