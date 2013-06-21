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
class App_Webim {
	
	const WEBIM_DAO = 0;
	const SETTING_DAO = 1;
	const HISTORY_DAO = 2;
	
	private $friend_groups = array();
	
	/*
	foreach($friend_groups as $k => $v){
		$friend_groups[$k] = to_utf8($v);
	}
	*/

	public function getUser($uid) {
		return Wekit::load('user.PwUser')->getUserByUid($uid, PwUser::FETCH_ALL);
	}

	public function userSpaceUrl($uid) {
		return "index.php?m=space&uid={$uid}"; 
	}

	public function userAvatar($uid) {
		return Pw::getAvatar($uid, 'small');
	}
	
	public function userNick($user) {
		if($user['realname'] != '') return $user['realname'];
		return $user['username'];
	}

	public function getNewMessages($uid) {
		return $this->_loadDao(self::HISTORY_DAO)->getNewMessages($uid);
	}

	public function newMessageToHistory($uid) {
		return $this->_loadDao(self::HISTORY_DAO)->newMessageToHistory($uid);
	}

	public function getSetting($uid) {
		return $this->_loadDao(self::SETTING_DAO)->getByUid($uid);
	}

	public function updateSetting($uid, $data) {
		return $this->_loadDao(self::SETTING_DAO)->updateByUid($uid, $data);
	}

	public function getHistory($uid, $with, $type = "unicast") {
		return $this->_loadDao(self::HISTORY_DAO)->getHistory($uid, $with, $type);
	}

	public function clearHistory($uid, $with) {
		return $this->_loadDao(self::HISTORY_DAO)->clearHistory($uid, $with);
	}

	public function insertHistory($uid, $nick, $type, $to, $body, $style, $send, $timestamp) {
		$row = array(
			"from" => $uid,
			"nick" => $nick,
			"send" => $send,
			"type" => $type,
			"to" => $to,
			"body" => $body,
			"style" => $style,
			"timestamp" => $timestamp,
			"created_at" => date( 'Y-m-d H:i:s' ),
		);
		$this->_loadDao(self::HISTORY_DAO)->add($row);
	}

	public function getOnlineBuddies($uid, $limit=1000) {
		$friends = Wekit::load('attention.PwAttention')->getFriendsByUid($uid, $limit, 0);
		$uids = array();
		foreach($friends as $friend) {
			$uids[] = $friend['same_uid'];
		}
		return $this->getBuddiesByUids($uids);
	}

	public function getBuddiesByUids($uids) {
		$users = Wekit::load('user.PwUser')->fetchUserByUid(array_unique($uids), PwUser::FETCH_ALL);
		$buddies = array();
		foreach($users as $u) {
			$buddies[] = (object)array(
				'uid' => $u['uid'],
				'id' => $u['uid'],
				'nick' => $this->userNick($u),
				'url' => "index.php?m=space&uid={$u['uid']}",
				'pic_url' => Pw::getAvatar($u['uid'], 'small'),
				'default_pic_url' => '',
				'status' => $u['profile']
			);
		}
		return $buddies;
	}

	//TODO:
	public function getRoom($uid, $gid) {
		return null;
	}

	public function getRooms($uid) {
		$tags = Wekit::load('tag.PwTag')->getAttentionByUid($uid, 0, 50);
		$rooms = array();
		foreach($tags as $tag) {
			$rooms[]=(object)array(
				'id'=>$tag['id'],
				'nick'=> $tag['name'],
				'pic_url'=>'',//$pic,
				'all_count' => 0,//TODO:
				'url'=> "tag/{$tag['name']}",
				'count'=>"");

		}
		return $rooms;
	}

	//FIXME:
	public function getRoomsByIds($uid, $ids) {
		return array();
	}

	public function getNotifications($uid) {
		$notices = Wekit::load('message.PwMessageNotices')->getNotices($uid);
		$result = array();
		foreach($notices as $notice) {
			if($notice['is_read'] == 0) {
				$time = strftime("%X", $notice['created_time']);
				$result[] = array("text" => $notice['title'], "time" => $time);
				/*
					[id] => 13
					[uid] => 2
					[typeid] => 5
					[param] => 2
					[is_read] => 1
					[is_ignore] => 0
					[title] => 'xxxx'
					[extend_params] => a:6:{s:2:"id";s:1:"1";s:5:"title";s:18:"å‘å¸ƒä¸€ä¸ªå¸–å­";s:4:"icon";s:0:"";s:3:"url";s:18:"bbs/post/run?fid=2";s:12:"created_time";i:1371655415;s:8:"complete";i:1;}
					[created_time] => 1371655415
					[modified_time] => 1371655415
				*/
			}
		}
		return $result;
	} 

	public function getMenu($uid) {
		$menu = array(
			#array("title" => 'doing',"icon" =>$site_url . "image/app/doing.gif","link" => $site_url . "space.php?do=doing"),
		);
		return $menu;

	}
	
	/**
	 * @return App_Webim_WebimDao
	 */
	private function _loadDao($type = self::WEBIM_DAO) {
		if($type == self::HISTORY_DAO) return Wekit::loadDao('EXT:webim.service.dao.App_Webim_HistoryDao');
		if($type == self::SETTING_DAO) return Wekit::loadDao('EXT:webim.service.dao.App_Webim_SettingDao');
		return Wekit::loadDao('EXT:webim.service.dao.App_Webim_WebimDao');
	}

}

?>
