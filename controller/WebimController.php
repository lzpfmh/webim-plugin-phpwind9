<?php

defined('WEKIT_VERSION') or exit(403);

/**
 * Webim Main Controller
 */
class WebimController extends PwBaseController {

	/*
	 * Webim User
	 */
	private $imuser;

	/*
	 * Webim Client 
	 */
	private $client;

	public function beforeAction($handlerAdapter) {
		parent::beforeAction($handlerAdapter);
		$conf = Wekit::C('app_webim');
		if(! ($this->loginUser->isExists() and $conf and $conf['webim.isopen'] == '1') ) {
			header( "HTTP/1.0 400 Bad Request" );
			exit;
		}
		$this->_initUser($this->getInput('show'));
		include_once(dirname(__FILE__) . '/../lib/HttpClient.php');
		include_once(dirname(__FILE__) . '/../lib/WebimClient.php');
		$ticket = $this->getInput('ticket');
		if( $ticket ) {
			$ticket = stripslashes($ticket);
		}
		$this->client = new WebimClient( (object)$this->imuser, $ticket, $conf['webim.domain'], $conf['webim.apikey'], $conf['webim.host'], $conf['webim.port'] );
	}

	public function run() {
		//noting todo
		exit;
	}

	public function onlineAction() {
		$uid = $this->loginUser->uid;
		#$domain = $this->getInput("domain");
		$im_buddies = array();//For online.
		$im_rooms = array();//For online.
		$strangers = $this->_idsArray($this->getInput('stranger_ids'));
		$cache_buddies = array();//For find.
		$cache_rooms = array();//For find.

		$active_buddies = $this->_idsArray( $this->getInput('buddy_ids') );
		$active_rooms = $this->_idsArray( $this->getInput('room_ids') );

		$service = $this->_service();
		$new_messages = $service->getNewMessages($uid);
		$online_buddies = $service->getOnlineBuddies($uid);
		$buddies_with_info = array(); //Buddy with info.

		//Active buddy who send a new message.
		$count = count($new_messages);
		for($i = 0; $i < $count; $i++){
			$active_buddies[] = $new_messages[$i]->from;
		}

		//Find im_buddies
		foreach($online_buddies as $k => $v){
			$id = $v->id;
			$im_buddies[] = $id;
			$buddies_with_info[] = $id;
			$v->presence = "offline";
			$v->show = "unavailable";
			$cache_buddies[$id] = $v;
		}

		//Get active buddies info.
		$buddies_without_info = array();
		foreach($active_buddies as $k => $v) {
			if(!in_array($v, $buddies_with_info)) {
				$buddies_without_info[] = $v;
			}
		}
		if(!empty($buddies_without_info) || !empty($strangers)){
			$bb = $service->getBuddies(array_merge($buddies_without_info, $strangers));
			foreach( $bb as $k => $v){
				$id = $v->id;
				$im_buddies[] = $id;
				$v->presence = "offline";
				$v->show = "unavailable";
				$cache_buddies[$id] = $v;
			}
		}
		if(!$_IMC['disable_room']){
			$rooms = $service->getRooms($uid);
			$setting = $service->getSetting($uid);
			$blocked_rooms = $setting && is_array($setting->blocked_rooms) ? $setting->blocked_rooms : array();
			//Find im_rooms 
			//Except blocked.
			foreach($rooms as $k => $v){
				$id = $v->id;
				if(in_array($id, $blocked_rooms)){
					$v->blocked = true;
				}else{
					$v->blocked = false;
					$im_rooms[] = $id;
				}
				$cache_rooms[$id] = $v;
			}
		}else{
			$rooms = array();
		}

		//===============Online===============
		$data = $this->client->online( implode(",", array_unique( $im_buddies ) ), implode(",", array_unique( $im_rooms ) ) );

		if( $data->success ){
			$data->new_messages = $new_messages;

			if(!$_IMC['disable_room']){
				//Add room online member count.
				foreach ($data->rooms as $k => $v) {
					$id = $v->id;
					$cache_rooms[$id]->count = $v->count;
				}
				//Show all rooms.
			}
			$data->rooms = $rooms;

			$show_buddies = array();//For output.
			foreach($data->buddies as $k => $v){
				$id = $v->id;
				if(!isset($cache_buddies[$id])){
					$cache_buddies[$id] = (object)array(
						"id" => $id,
						"nick" => $id,
						"incomplete" => true,
					);
				}
				$b = $cache_buddies[$id];
				$b->presence = $v->presence;
				$b->show = $v->show;
				if( !empty($v->status) )
					$b->status = $v->status;
				#show online buddy
				$show_buddies[] = $id;
			}
			#show active buddy
			$show_buddies = array_unique(array_merge($show_buddies, $active_buddies));
			$o = array();
			foreach($show_buddies as $id){
				//Some user maybe not exist.
				if(isset($cache_buddies[$id])){
					$o[] = $cache_buddies[$id];
				}
			}

			//Provide history for active buddies and rooms
			foreach($active_buddies as $id){
				if(isset($cache_buddies[$id])){
					$cache_buddies[$id]->history = $service->getHistory($uid, $id, "chat" );
				}
			}
			foreach($active_rooms as $id){
				if(isset($cache_rooms[$id])){
					$cache_rooms[$id]->history = $service->getHistory($uid, $id, "grpchat" );
				}
			}
			$show_buddies = $o;
			$data->buddies = $show_buddies;
			$service->newMessageToHistory($uid);
			echo $this->webimCallback($data);
			exit;
		} else {
			exit( $this->webimCallback( array( "success" => false, "error_msg" => empty( $data->error_msg ) ? "IM Server Not Found" : "IM Server Not Authorized", "im_error_msg" => $data->error_msg ) ) );
		}
		exit;
	}

	public function offlineAction() { 
		$this->_validateInput("ticket");
		$this->client->offline();
		echo $this->webimCallback( "ok"  );
		exit();
	}

	public function messageAction() { 
		$this->_validateInput( "ticket", "type", "to", "body" );
		$type = $this->getInput("type");
		$offline = $this->getInput("offline");
		$to = $this->getInput("to");
		$body = $this->getInput("body");
		$style = $this->getInput("style");
		$send = $offline == "true" || $offline == "1" ? 0 : 1;
		$timestamp = $this->_microtimeFloat() * 1000;
		$this->_service()->insertHistory( $this->loginUser->uid, $this->imuser['nick'],
			$type, $to, $body, $style, $send, $timestamp );
		if($send == 1) {
			$this->client->message($type, $to, $body, $style, $timestamp);
		}
		echo $this->webimCallback( "ok" );
		exit();
	}

	public function presenceAction() {
		$this->_validateInput( "ticket", "show" );
		$this->client->presence( $this->getInput("show"), $this->getInput("status") );
		echo $this->webimCallback( "ok" ) ;
		exit();
	}

	public function statusAction() {
		$this->_validateInput( "ticket", "show", "to" );
		$this->client->status( $this->getInput("to"), $this->getInput("show") );
		echo $this->webimCallback( "ok" ) ;
		exit();
	}

	public function refreshAction() {
		$this->_validateInput( "ticket" );
		$this->client->offline();
		echo $this->webimCallback( "ok" );
		exit();
	}

	public function settingAction() {
		$this->_validateInput( 'data' );
		$this->_service()->updateSetting($this->loginUser->uid, stripslashes( $this->getInput( 'data' ) ) );
		echo $this->webimCallback( 'ok' );
		exit();
	}

	public function historyAction() {
		$this->_validateInput( "id", "type" );
		$id =  $this->getInput("id");
		$type = $this->getInput("type");
		$history = $this->_service()->getHistory($this->loginUser->uid, $id, $type);
		echo $this->webimCallback( $history );
		exit();
	}

	public function clearHistoryAction() {
		$this->_validateInput( "id" );
		$id = $this->getInput("id");
		$this->_service()->clearHistory($this->loginUser->uid, $id);
		echo $this->webimCallback( "ok" );
		exit();
	}

	//FIXME:
	public function downloadHistory() {
		echo "....";
		exit;
	}

	public function membersAction() {
		$this->_validateInput( "ticket", "id" );
		$gid = $this->getInput("id");
		$re = $this->client->members( $gid );
		echo $re ? $this->webimCallback( $re ) : "Not Found";
		exit();
	}

	public function joinAction() {
		$this->_validateInput( "ticket", "id" );
		$gid = $this->getInput("id");
		$room = $this->_service()->getRoom($this->loginUser->uid, $gid);
		if($room){
			$re = $this->client->join($gid);
			if($re){
				$room->count = $re->count;
				echo $this->webimCallback( $room );
			}else{
				header("HTTP/1.0 404 Not Found");
				echo "Con't join this room right now";
			}
		}else{
			header("HTTP/1.0 404 Not Found");
			echo "Con't found this room";
		}
		exit();
	}

	public function leaveAction() {
		$this->_validateInput( "ticket", "id" );
		$gid = $this->getInput("id");
		$this->client->leave( $gid );
		echo $this->webimCallback( "ok" );
		exit;
	}

	public function buddiesAction() {
		$this->_validateInput( "ids" );
		$ids = $this->getInput("ids");
		$buddies = $this->_service()->getBuddiesByUids( $ids );
		echo $this->webimCallback( $buddies );
		exit;
	}

	public function roomsAction() {
		$this->_validateInput( "ids" );
		$ids = $this->getInput("ids");
		$rooms = $this->_service()->getRoomsByIds($this->loginUser->uid, $ids);
		echo $this->webimCallback( $rooms );
		exit();
	}

	public function notificationsAction() {
		$notifications = $this->_service()->getNotifications($this->loginUser->uid);
		echo $this->webimCallback( $notifications );
		exit();
	}

	/*
	 * Init current user
	 */
	private function _initUser($show) {
		$uid = $this->loginUser->uid;
		$user = $this->_service()->getUser($uid);
		$this->imuser = array();
		$this->imuser['uid'] = $uid;
		$this->imuser['id']  =  $uid;
		$this->imuser['nick'] = $this->_service()->userNick($user);
		$this->imuser['url'] = $this->_service()->userSpaceUrl($uid); 
		$this->imuser['pic_url'] = $this->_service()->userAvatar($uid);
		$this->imuser['default_pic_url'] =  '';
		$this->imuser['show'] = $show ? $show : "available";
		$this->imuser['status'] = $user['profile'];
	}

	private function _validateInput() {
		$keys = func_get_args();
		$invalid_keys = array();
		foreach( $keys as $key ) {
			$val = $this->getInput( $key );
			if ( !$val || !trim( $val )  ) 
				$invalid_keys[] = $key;
		}
		if( $invalid_keys ) {
			header( "HTTP/1.0 400 Bad Request" );
			exit( "Empty get " . implode( ",", $invalid_keys ) );
		}
	}

	private function webimCallback( $data, $jsonp = "callback" ){
		$data = json_encode( $data );
		$cb = $this->getInput( $jsonp );
		return  $cb ? $cb . "($data);" : $data;
	}

	private function _service() {
		return Wekit::load('EXT:webim.service.App_Webim');
	}

	/** Simple function to replicate PHP 5 behaviour */
	private function _microtimeFloat() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	private function _idsArray( $ids ){
		return ($ids===NULL || $ids==="") ? array() : (is_array($ids) ? array_unique($ids) : array_unique(explode(",", $ids)));
	}

	private function _isRemote() {
		$remote = false;
		if ( strlen($_SERVER['HTTP_REFERER']) ) {
			$referer = parse_url( $_SERVER['HTTP_REFERER'] );
			$referer['port'] = isset( $referer['port'] ) ? $referer['port'] : "80";
			if ( $referer['port'] != $_SERVER['SERVER_PORT'] || $referer['host'] != $_SERVER['SERVER_NAME'] || $referer['scheme'] != ( (@$_SERVER["HTTPS"] == "on") ? "https" : "http" ) ){
				$remote = true;
			}
		}
		return $remote;
	}

}

?>
