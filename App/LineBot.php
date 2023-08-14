<?php
namespace App;

require_once 'Setting.php';

use App\Setting;

class LineBot {
	private $channelAccessToken;
	private $channelSecret;
	private $webhookResponse;
	private $webhookEventObject;
	private $apiReply;
	private $apiPush;
	private $adminUserId;
	private $baseUrl;
	private $apiUserProfile;
	private $apiUserId;
	public function __construct()
	{
		$data = json_decode(file_get_contents(TOKEN), true);

		$this->channelAccessToken = $data["channelAccessToken"];
		$this->channelSecret = $data["channelSecret"];
		$this->apiReply = $data["apiReply"];
		$this->apiPush = $data["apiPush"];
		$this->baseUrl = $data["endpoint"];
		$this->adminUserId = $data["adminUserId"];

		$this->webhookResponse = file_get_contents('php://input');
		$this->webhookEventObject = json_decode($this->webhookResponse);
	}
	
	private function httpPost($api,$body){
		$ch = curl_init($api); 
		curl_setopt($ch, CURLOPT_POST, true); 
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body)); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 
		'Content-Type: application/json; charser=UTF-8', 
		'Authorization: Bearer '.$this->channelAccessToken)); 
		$result = curl_exec($ch);
		curl_close($ch); 
		return $result;
	}

	private function httpOpenAIPost($req){
		$ch = curl_init("https://api.openai.com/v1/completions");
		$api_params = array(
			'model' => 'text-davinci-003',
			'prompt' => $req,
			'temperature' => 0.9,
			'max_tokens' => 400,
			'frequency_penalty' => 0,
			'presence_penalty' => 0.6,
			);
		$api_query = json_encode($api_params);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $api_query);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 
		'Content-Type: application/json', 
		'Authorization: Bearer '. OPENAIAPIKEY,
		)); 
		$result = curl_exec($ch);
		curl_close($ch); 
		return $result;
	}
	

	private function httpGet($api){
		$ch = curl_init($api);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	private function userGet($api){
		$ch = curl_init($api); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array( 
		'Content-Type: application/json; charser=UTF-8', 
		'Authorization: Bearer '.$this->channelAccessToken)); 
		$result = curl_exec($ch);
		curl_close($ch); 
		return $result;
	}

	public function openAI($req)
	{
		$webhook = $this->httpOpenAIPost($req);
		$webhook = json_decode($webhook, true);
		return $webhook;
	}
	public function botAutoResponse($result, $function){
		$this->$function($result);
	}

	public function getGroupChatSummary($groupId){
		$api = setting::getApiGroupChatSummary($groupId);
		$webhook = json_decode($this->userGet($api), true);
		return $webhook;
	}
	
	public function getWikipedia($search){
		$api = Setting::getApiWikipedia($search);
		$webhook = $this->httpGet($api);
		$webhook = json_decode($webhook, true);
		$getPage = $webhook["query"]["pages"];
		return $getPage;
	}

	public function getGenshinDevSelect($type, $select){
		$api = Setting::getApiGenshinDevSelect($type, $select);
		$webhook = $this->httpGet($api);
		$webhook = json_decode($webhook, true);
		return $webhook;
	}

	public function getGenshinDevType($type){
		$api = Setting::getApiGenshinDevType($type);
		$webhook = $this->httpGet($api);
		$webhook = json_decode($webhook, true);
		return $webhook;
	}

	public function getSafebooruByTags($tag){
		$api = Setting::getApiSafebooru($tag);
		$webhook = $this->httpGet($api);
		
		return $webhook;
	}

	public function reply($text){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"}; 
		$body["replyToken"] = $replyToken;
		$body["messages"][0] = array(
			"type" => "text",
			"text"=>$text
		);
		$result = $this->httpPost($api,$body);
		return $result;
	}

	public function paimonReply($text){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"}; 
		$body["replyToken"] = $replyToken;
		$body["messages"][0] = array(
			"type" => "text",
			"text"=>$text,
			"sender" => array(
				"name" => "Paimon",
			)
		);
		$result = $this->httpPost($api,$body);
		return $result;
	}

	public function contextReply($context, $text){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"}; 
		$body["replyToken"] = $replyToken;
		$body["messages"][0] = array(
			"type" => "text",
			"text"=> $text,
			"sender" => array(
				"name" => $context,
			)
		);
		$result = $this->httpPost($api,$body);
		return $result;
	}

	public function multiReply($text){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"}; 
		$body["replyToken"] = $replyToken;
		for($int = 0; $int < sizeof($text); $int++){
			$body["messages"][$int] = array(
				"type" => "text",
				"text"=>$text[$int]
			);
		}
		$result = $this->httpPost($api,$body);
		return $result;
	}

	public function contextMultiReply($text, $context){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"}; 
		$body["replyToken"] = $replyToken;
		for($int = 0; $int < sizeof($text); $int++){
			$body["messages"][$int] = array(
				"type" => "text",
				"text"=>$text[$int],
				"sender" => array(
					"name" => $context,
				)
			);
		}
		$result = $this->httpPost($api,$body);
		return $result;
	}

	public function autoMultiReply($text)
	{
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"}; 

		$countPart = 0;
		$countText = 0;
		$body["replyToken"] = $replyToken;
		$textPart = substr($text, $countPart, 500);
		$body["messages"][$countText] = array(
			"type" => "text",
			"text"=>$textPart
		);
		$result = $this->httpPost($api,$body);
		return $result;
	}
	
	public function flex($text){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"}; 
		$body["replyToken"] = $replyToken;
		$body["messages"][0] = array(
			"type" => "bubble",
			"body" => [
				"type" => "box",
				"layout" => "horizontal",
				"contents" => [
					array(
						"type" => "text",
						"text" => "Rate"
					),
					array(
						"type" => "text",
						"text" => "Name"
					)
				]
			]
		);
		$result = $this->httpPost($api,$body);
		return $result;
	}

	public function secondReply($firstText,$secondText){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"}; 
		$body["replyToken"] = $replyToken;
		$body["messages"][0] = array(
			"type" => "text",
			"text"=>$firstText
		);
		$body["messages"][1] = array(
			"type" => "text",
			"text"=>$secondText
		);
		$result = $this->httpPost($api,$body);
		return $result;
	}

	public function thirdReply($firstText,$secondText,$thirdText){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"};
		$body["replyToken"] = $replyToken;
		$body["messages"][0] = array(
			"type" => "text",
			"text" => $firstText
		);
		$body["messages"][1] = array(
			"type" => "text",
			"text" => $secondText
		);
		$body["messages"][2] = array(
			"type" => "text",
			"text" => $thirdText
		);
		$result = $this->httpPost($api,$body);
		return $result;
	}

	public function fourthReply($firstText,$secondText,$thirdText, $fourthText){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"};
		$body["replyToken"] = $replyToken;
		$body["messages"][0] = array(
			"type" => "text",
			"text" => $firstText
		);
		$body["messages"][1] = array(
			"type" => "text",
			"text" => $secondText
		);
		$body["messages"][2] = array(
			"type" => "text",
			"text" => $thirdText
		);
		$body["messages"][3] = array(
			"type" => "text",
			"text" => $fourthText
		);
		$result = $this->httpPost($api,$body);
		return $result;
	}
	
	public function push($body){
		$api = $this->apiPush;
		$result = $this->httpPost($api, $body);
		return $result;
    }

    public function pushText($to, $text){
		$body = array(
		    'to' => $to,
		    'messages' => [
			array(
			    'type' => 'text',
			    'text' => $text
			)
		    ]
		);
		$this->push($body);
	 }

   	public function pushImage($to, $imageUrl, $previewImageUrl = false){
        	$body = array(
		    'to' => $to,
		    'messages' => [
			array(
			    'type' => 'image',
			    'originalContentUrl' => $imageUrl,
			    'previewImageUrl' => $previewImageUrl ? $previewImageUrl : $imageUrl
			)
		    ]
		);
		$this->push($body);
    	}

    public function pushVideo($to, $videoUrl, $previewImageUrl){
        	$body = array(
          	  'to' => $to,
          	  'messages' => [
          	      array(
			    'type' => 'video',
			    'originalContentUrl' => $videoUrl,
			    'previewImageUrl' => $previewImageUrl
			)
		    ]
		);
        	$this->push($body);
    	}

    public function pushAudio($to, $audioUrl, $duration){
		$body = array(
		    'to' => $to,
		    'messages' => [
			array(
			    'type' => 'audio',
			    'originalContentUrl' => $audioUrl,
			    'duration' => $duration
			)
		    ]
		);
		$this->push($body);
	}

    public function pushLocation($to, $title, $address, $latitude, $longitude){
		$body = array(
		    'to' => $to,
		    'messages' => [
			array(
			    'type' => 'location',
			    'title' => $title,
			    'address' => $address,
			    'latitude' => $latitude,
			    'longitude' => $longitude
			)
		    ]
		);
		$this->push($body);
	}

	public function getType()
	{
		$webhook = $this->webhookEventObject;
		$type = $webhook->{"events"}[0]->{"type"};
		return $type;
	}

	public function getStickerId()
    {
        return json_decode(file_get_contents(STICKER), true);
    }

	public function getTextChat()
    {
        return strtolower($this->getMessageText());
    }

	public function getCommand()
    {
        return json_decode(file_get_contents(COMMAND), true);
    }
	
	public function getMessageText(bool $parseMessage = false){
		$webhook = $this->webhookEventObject;
		$messageText = $webhook->{"events"}[0]->{"message"}->{"text"};
		if (!$parseMessage)
		{
			return $messageText;
		} 
		return explode(' ', trim(strtolower($messageText)));
	}

	public function getMessageId(){
		$webhook = $this->webhookEventObject;
		$messageId = $webhook->{"events"}[0]->{"message"}->{"id"}; 
		return $messageId;
	}

	public function getJoinedMemberCondition(){
		$webhook = $this->webhookEventObject;
		$messageText = $webhook->{"events"}[0]->{"type"}; 
		$isEventJoinedMember = ($messageText == "memberJoined" ? true : false);
		return $isEventJoinedMember;
	}

	public function getMessageCondition(){
		$webhook = $this->webhookEventObject;
		$messageText = $webhook->{"events"}[0]->{"type"}; 
		$isEventmessage = ($messageText == "message" ? true : false);
		return $isEventmessage;
	}

	public function getLeaveMemberCondition(){
		$webhook = $this->webhookEventObject;
		$messageText = $webhook->{"events"}[0]->{"type"}; 
		$isEventLeaveMember = ($messageText == "memberLeft" ? true : false);
		return $isEventLeaveMember;
	}

	public function getUnsendCondition(){
		$webhook = $this->webhookEventObject;
		$messageText = $webhook->{"events"}[0]->{"type"}; 
		$isEventUnsend = ($messageText == "unsend" ? true : false);
		return $isEventUnsend;
	}
	
	public function postbackEvent(){
		$webhook = $this->webhookEventObject;
		$postback = $webhook->{"events"}[0]->{"postback"}->{"data"}; 
		return $postback;
	}
	
	public function getUserId(){
		$webhook = $this->webhookEventObject;
		$userId = $webhook->{"events"}[0]->{"source"}->{"userId"}; 
		return $userId;
	}

	public function getMentionId(){
		$webhook = $this->webhookEventObject;
		$getMentionId = $webhook->{"events"}[0]->{"message"}->{"mention"}->{"mentionees"}[0]->{"userId"}; 
		return $getMentionId;
	}

	public function getEntireMentionId(){
		$result = [];
		$webhook = $this->webhookEventObject;
		$getMentionId = $webhook->{"events"}[0]->{"message"}->{"mention"}->{"mentionees"}; 
		foreach($getMentionId as $mentionId){
			$result[] = $mentionId->{"userId"};
		}
		return $result;
	}

	public function getGroupId(){
		$webhook = $this->webhookEventObject;
		$groupId = $webhook->{"events"}[0]->{"source"}->{"groupId"};
		return $groupId;
	}

	public function replyImage($imageUrl, $previewImageUrl = false){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"}; 
		$body["replyToken"] = $replyToken;
		$body["messages"][0] = array(
			    'type' => 'image',
			    'originalContentUrl' => $imageUrl,
			    'previewImageUrl' => $previewImageUrl ? $previewImageUrl : $imageUrl
			);		
		$result = $this->httpPost($api,$body);
		return $result;
	}

	public function multiReplyImage($imageUrl, $previewImageUrl = false){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"}; 
		$body["replyToken"] = $replyToken;
		for($int = 0; $int < sizeof($imageUrl); $int++){
			$body["messages"][$int] = array(
			    'type' => 'image',
			    'originalContentUrl' => $imageUrl[$int],
			    'previewImageUrl' => $previewImageUrl ? $previewImageUrl[$int] : $imageUrl[$int]
			);	
		}	
		$result = $this->httpPost($api,$body);
		return $result;
	}

	public function replyVideo($videoUrl, $previewImageUrl){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"}; 
		$body["replyToken"] = $replyToken;
		$body["messages"][0] =  array(
			    'type' => 'video',
			    'originalContentUrl' => $videoUrl,
			    'previewImageUrl' => $previewImageUrl
			);		
		$result = $this->httpPost($api,$body);
		return $result;
	}

	public function replyAudio($audioUrl){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"}; 
		$body["replyToken"] = $replyToken;
		$body["messages"][0] =  array(
			    'type' => 'audio',
			    'originalContentUrl' => $audioUrl,
			    'duration' => 60000
			);		
		$result = $this->httpPost($api,$body);
		return $result;
	}

	public function getDisplayName($userId){
		$api = setting::getUserProfile($userId);
		$webhook = json_decode($this->userGet($api));
		$displayName = $webhook->{"displayName"};
		return $displayName;
	}

	public function getEndpoint($userId){
		if($userId == $this->adminUserId){
			$api = setting::getWebhookEndpoint();
			$webhook = json_decode($this->userGet($api));
			$endpoint = $webhook->{"endpoint"};
			return $endpoint;
		}
		else return "[REDACTED]";
	}

	public function getInfo(){
		$api = setting::getBotInfo();
		$webhook = json_decode($this->userGet($api));
		return $webhook;
	}

	public function tesEndpoint($URL){
		$api = setting::testWebhookEndpoint();
		$webhook = $this->webhookEventObject;
		$body["endpoint"] = $URL;
		$this->httpPost($api,$body);
		$result = $webhook->{"reason"};
		return $result;
	}

	public function getpictureUrl($userId){
		$api = setting::getUserProfile($userId);
		$webhook = json_decode($this->userGet($api));
		$displayName = $webhook->{"pictureUrl"};
		return $displayName;
	}

	public function getDisplayNameOnGroup($groupId, $userId){
		$api = Setting::getGroupProfileUser($groupId, $userId);
		$webhook = json_decode($this->userGet($api));
		$displayName = $webhook->{"displayName"};
		return $displayName;
	}

	public function getstatusMessage($userId){
		$api = setting::getUserProfile($userId);
		$webhook = json_decode($this->userGet($api));
		$displayName = $webhook->{"statusMessage"};
		return $displayName;
	}

	public function debuging(){

	}

	public function replyChatWithImage($description, $url){
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"}; 
		$body["replyToken"] = $replyToken;
		$body["messages"][0] = array(
			'type' => 'image',
			'originalContentUrl' => $url,
			'previewImageUrl' => $url ? $url : $url
		);
		$body["messages"][1] = array(
			"type" => "text",
			"text"=>$description
		);
		$result = $this->httpPost($api,$body);
		return $result;
	}

	public function replyKiss($text){
		$url = 'https://wallpapercave.com/wp/wp2784903.jpg';
		$api = $this->apiReply;
		$webhook = $this->webhookEventObject;
		$replyToken = $webhook->{"events"}[0]->{"replyToken"}; 
		$body["replyToken"] = $replyToken;
		$body["messages"][0] = array(
			'type' => 'image',
			'originalContentUrl' => $url,
			'previewImageUrl' => $url ? $url : $url
		);
		$body["messages"][1] = array(
			"type" => "text",
			"text"=>$text
		);
		$result = $this->httpPost($api,$body);
		return $result;
	}
	
	
}
