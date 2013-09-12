<?php
defined('WEKIT_VERSION') or exit(403);

define( "WEBIM_HISTORY_KEYS", "`to`,`nick`,`from`,`style`,`body`,`type`,`timestamp`" );

/**
 * App_Webim_WebimDao - dao
 *
 * @author 杭州巨鼎信息技术有限公司 <webim20@gmail.com>
 * @copyright http://www.webim20.cn
 * @license http://www.webim20.cn
 */
class App_Webim_HistoryDao extends PwBaseDao {
	
	/**
	 * table name
	 */
	protected $_table = 'app_webim_histories';
	/**
	 * primary key
	 */
	protected $_pk = 'id';
	/**
	 * table fields
	 */
	protected $_dataStruct = array('id', 'send', 'type', 'nick', 'body', 'style', 'timestamp', 'todel', 'fromdel', 'to', 'from', 'created_at', 'updated_at' /*, 'field' */);

	/**
	 * Get history message
	 *
	 * @param string $with chat
	 * @param string $type chat or grpchat
	 * @param int    $limit history num
	 *
	 * Example:
	 *
	 * 	webim_get_history( 'susan', 'chat' );
	 *
	 */
	public function getHistory($uid, $with, $type = 'chat', $limit = 30 ) {
		if( $type == "chat" ){
			$sql = $this->_bindSql("SELECT * FROM %s  
				WHERE `type` = 'chat' 
				AND ((`to`=%s AND `from`=%s AND `fromdel` != 1) 
				OR (`send` = 1 AND `from`=%s AND `to`=%s AND `todel` != 1))  
				ORDER BY timestamp DESC LIMIT %d", $this->getTable(), $with, $uid, $with, $uid, $limit );
		} else {
			$sql = $this->_bindSql("SELECT * FROM  %s 
				WHERE `to`=%s AND `type`='grpchat' AND send = 1 
				ORDER BY timestamp DESC LIMIT %d", $this->getTable(), $with, $limit);
		}
		$smt = $this->getConnection()->createStatement($sql);
		return array_reverse($smt->queryAll(array()));
	}

	/**
	 * Clear user history message
	 *
	 * @param string $with chat user
	 *
	 */
	public function clearHistory($uid, $with ) {
		$fields = array( "fromdel" => 1, "type" => "chat" );
		$sql = $this->_bindSql('UPDATE %s SET %s Where from = ? and to = ?', $this->getTable(), $this->sqlSingle($fields));
		$smt = $this->getConnection()->createStatement($sql);
		$smt->update(array($uid, $with));
		
		$fields = array( "todel" => 1, "type" => "chat" );
		$sql = $this->_bindSql('UPDATE %s SET %s Where to = ? and from = ?', $this->getTable(), $this->sqlSingle($fields));
		$smt = $this->getConnection()->createStatement($sql);
		$smt->update(array($uid, $with));

		$sql = $this->_bindTable("DELETE FROM %s WHERE todel=1 AND fromdel=1");
		$this->getConnection()->execute($sql);
	}
	
	/**
	 * Get new message
	 */
	function getNewMessages($uid, $limit = 50 ) {
		$sql = $this->_bindSql("SELECT * FROM %s 
			WHERE `to` = ? and send != 1 
			ORDER BY timestamp DESC LIMIT %d", $this->getTable(), $limit );
		$smt = $this->getConnection()->createStatement($sql);
		return array_reverse( $smt->queryAll(array($uid)) );
	}
	
	
	/**
	 * mark the new message as read.
	 *
	 */
	public function newMessageToHistroy($uid) {
		$fields = array( "send" => 1 );
		$sql = $this->_bindSql("UPDATE %s SET %s where to = ? and send = ?");
		$smt = $this->getConnection()->createStatement($sql);
		$smt->update(array($uid, 0));
	}
	
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
