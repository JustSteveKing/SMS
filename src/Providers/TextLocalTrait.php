<?php

namespace JustSteveKing\SMS\Providers;

trait TextLocalTrait {

	private static $requestUri = 'http://api.txtlocal.com/';
	private static $requestTimeout = 60;

	private $errorReporting = false;
	private $apiKey;
	private $apiMode;
	private $sender;

	public $errors = [];
	public $warnings = [];
	public $lastRequest = [];

	public function __construct($apiKey, $apiMode = null, $sender = null) {
		$this->apiKey = $apiKey;
		($apiMode) ? $this->apiMode = $apiMode : $this->apiMode = false;
		($sender) ? $this->sender = $sender : $this->sender = "Test Sender";
	}

	private function request($command, $params = []) {
		$params['apiKey'] = $this->apiKey;
		$params['test'] = $this->apiMode;
		$this->lastRequest = $params;
		$rawResponse = $this->curl($command, $params);
		$result = json_decode($rawResponse);
		if (isset($result->errors)) {
			if (count($result->errors) > 0) {
				foreach ($result->errors as $error) {
					switch ($error->code) {
						default:
							throw new \Exception($error->message);
					}
				}
			}
		}
		return $result;
	}

	private function curl($command, $params) {
		$url = self::$requestUri . $command . '/';
		$ch = curl_init($url);
		curl_setopt_array($ch, array(
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $params,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT        => self::$requestTimeout
		));
		$rawResponse = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$error = curl_error($ch);
		curl_close($ch);
		if ($rawResponse === false) {
			throw new \Exception('Failed to connect to the Textlocal service: ' . $error);
		} elseif ($httpCode != 200) {
			throw new \Exception('Bad response from the Textlocal service: HTTP code ' . $httpCode);
		}

		return $rawResponse;
	}

	public function getLastRequest() {
		return $this->lastRequest;
	}

	public function send(
			$numbers, $message, $sender = null, $sched = null, 
			$receiptURL = null, $custom = null, $optouts = false, $simpleReplyService = false) {

		if (!is_array($numbers))
			throw new \Exception('Invalid $numbers format. Must be an array');
		if (empty($message))
			throw new \Exception('Empty message');
		if (!is_null($sched) && !is_numeric($sched))
			throw new \Exception('Invalid date format. Use numeric epoch format');

		$params = [
			'message'       => urlencode($message),
			'numbers'       => implode(',', $numbers),
			'sender'        => urlencode($this->sender),
			'schedule_time' => $sched,
			'test'          => $this->apiMode,
			'receipt_url'   => $receiptURL,
			'custom'        => $custom,
			'optouts'       => $optouts,
			'simple_reply'  => $simpleReplyService
		];

		return $this->request('send', $params);
	}

	public function createContact($number, $firstname = "", $lastname = "", $custom1 = "", $custom2 = "", $custom3 = "") {
		$user = new stdClass();
		$user->number = $number;
		$user->firstname = $firstname;
		$user->lastname = $lastname;
		$user->custom1 = $custom1;
		$user->custom2 = $custom2;
		$user->custom3 = $custom3;
		return $user;
	}

	public function templates() {
		return $this->request('get_templates');
	}

	public function checkKeyword($keyword) {
		$params = ['keyword' => $keyword];
		return $this->request('check_keyword', $params);
	}

	public function createGroup($name) {
		$params = ['name' => $name];
		return $this->request('create_group', $params);
	}

	public function contacts($groupId, $limit, $startPos = 0) {
		if (!is_numeric($groupId))
			throw new \Exception('Invalid $groupId format. Must be a numeric group ID');
		if (!is_numeric($startPos) || $startPos < 0)
			throw new \Exception('Invalid $startPos format. Must be a numeric start position, 0 or above');
		if (!is_numeric($limit) || $limit < 1)
			throw new \Exception('Invalid $limit format. Must be a numeric limit value, 1 or above');

		$params = [
			'group_id' => $groupId,
			'start'    => $startPos,
			'limit'    => $limit
		];
		return $this->request('get_contacts', $params);
	}

	public function createContacts($numbers, $groupid = '5') {
		$params = ["group_id" => $groupid];

		if (is_array($numbers)) {
			$params['numbers'] = implode(',', $numbers);
		} else {
			$params['numbers'] = $numbers;
		}

		return $this->request('create_contacts', $params);
	}

	public function groups() {
		return $this->request('get_groups');
	}

	public function messageStatus($messageid) {
		$params = ["message_id" => $messageid];
		return $this->request('status_message', $params);
	}

	public function senderNames() {
		return $this->request('get_sender_names');
	}

	public function inboxes() {
		return $this->request('get_inboxes');
	}

	public function balance() {
		$result = $this->request('balance');
		return ['sms' => $result->balance->sms, 'mms' => $result->balance->mms];
	}

	public function messages($inbox) {
		if (!isset($inbox)) return false;
		$options =  ['inbox_id' => $inbox];
		return $this->request('get_messages', $options);
	}

	public function cancelScheduledMessage($id) {
		if (!isset($id)) return false;
		$options = ['sent_id' => $id];
		return $this->request('cancel_scheduled', $options);
	}

	public function scheduledMessages() {
		return $this->request('get_scheduled');
	}

	public function deleteContact($number, $groupid = 5) {
		if (!isset($number)) return false;
		$options = ['number' => $number, 'group_id' => $groupid];
		return $this->request('delete_contact', $options);
	}

	public function deleteGroup($groupid) {
		$options = ['group_id' => $groupid];
		return $this->request('delete_group', $options);
	}

	public function singleMessageHistory($start, $limit, $min_time, $max_time) {
		return $this->history('get_history_single', $start, $limit, $min_time, $max_time);
	}

	public function APIMessageHistory($start, $limit, $min_time, $max_time) {
		return $this->history('get_history_api', $start, $limit, $min_time, $max_time);
	}

	private function history($type, $start, $limit, $min_time, $max_time) {
		if (!isset($start) || !isset($limit) || !isset($min_time) || !isset($max_time)) return false;
		$options = ['start' => $start, 'limit' => $limit, 'min_time' => $min_time, 'max_time' => $max_time];
		return $this->request($type, $options);
	}

	public function optouts($time = null) {
		return $this->request('get_optouts');
	}
}