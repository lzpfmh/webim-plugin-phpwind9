<?php

defined('WEKIT_VERSION') or exit(403);
/**
 * App_Webim_WebimDao - dao
 *
 * @author 杭州巨鼎信息技术有限公司 <webim20@gmail.com>
 * @copyright http://www.webim20.cn
 * @license http://www.webim20.cn
 */
class App_Webim_SettingDao extends PwBaseDao {
	
	/**
	 * table name
	 */
	protected $_table = 'app_webim_settings';
	/**
	 * primary key
	 */
	protected $_pk = 'id';
	/**
	 * table fields
	 */
	protected $_dataStruct = array('id', 'uid', 'web', 'air', 'created_at', 'updated_at' /*, 'field' */);

	public function getByUid($uid, $type = 'web') {
		$sql = $this->_bindTable("SELECT " . $type . " FROM %s WHERE uid = ? ");
		$row = $this->getConnection()->createStatement($sql)->getOne(array($uid));
		if($row) {
			return json_decode($row[$type]);
		}
		$this->_add(array('uid' => $uid, $type => "{}"), true);
		return new stdClass();
	}
	
	public function updateByUid($uid, $data, $type = 'web' ) {
		if( $data ) {
			if ( !is_string( $data ) ){
				$data = json_encode( $data );
			}
			$fields = array($type => $data);
			$sql = $this->_bindSql('UPDATE %s SET %s Where uid = ?', $this->getTable(), $this->sqlSingle($fields));
			$smt = $this->getConnection()->createStatement($sql);
			$smt->update(array($uid), true);
		}
	}
	
}

?>
