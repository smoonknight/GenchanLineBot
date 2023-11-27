<?php
use App\FirebaseController;

require_once "../vendor/autoload.php";

define("KEYWORD", "../storage/data/keyword.json");
define("COMMAND", "../storage/data/command.json");
define("STICKER", "../storage/data/sticker.json");
define("RESPONSE", "../storage/data/response.json");
define("CHARACTER", "../storage/data/character.json");
define("BATTLE", "../storage/data/battle.json");
define("KAOMOJI", "../storage/data/kaomoji.json");
define("TOKEN", "../storage/token/line.token.json");
define("LOGCHAT", "./groupfiles/LOGCHAT_");
define("SETUPGROUP", "./groupfiles/SETUPGROUP_");
define("MAINTENANCE", "Sedang dalam perbaikan");
define("OPENAIAPIKEY", "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");

use App\LineBot;
use App\Genchan;
use App\ResponseDecoration; 
use App\Keyword;
use App\AutoResponse;
use App\ScrapingController;
use App\MessageResponse;

class Index
{
    private $bot;
    private $genchan;
    private $responseDecoration;
    private $keyword;
    private $textChat;
    private $messageResponse;

    public function __construct()
    {
        $this->bot = new LineBot();
        $this->genchan = new Genchan();
        $this->responseDecoration = new ResponseDecoration();
        $this->keyword = new Keyword();
        $this->textChat = $this->bot->getTextChat();

        $this->messageResponse = new MessageResponse();

        $type = $this->bot->getType();
        $this->$type();
    }

    public function message()
    {
        $isAlreadyDelivered = $this->messageResponse->response();

        if ($isAlreadyDelivered)
        {
            return;
        }

        if (strpos($this->textChat, ':') || $this->textChat[0] == ":")
        {
            $this->handleSticker();
            return;
        }

        if ($this->bot->getMentionId())
        {
            $this->handleMention();
            return;
        }

        $this->handleAutoResponse();
    }

    public function memberJoined()
    {
        $this->bot->reply("Wah ada yang join, salken aku genchan (o´∀`o)");
    }

    public function logChat()
    {
        if (strlen($this->textChat) < 400 && $this->textChat != null) {
            $path = LOGCHAT . $this->bot->getGroupId();
            $put = file_get_contents($path);
            file_put_contents($path, $put . $this->bot->getDisplayNameOnGroup($this->bot->getGroupId(), $this->bot->getUserId()) . " : " . $this->bot->getMessageText() . "\n");
        }
    }

    public function handleMention()
    {
        $groupId = $this->bot->getGroupId();
        $userIds = $this->bot->getEntireMentionId();
        $displayNames = array();

        foreach ($userIds as $userId)
        {
            $displayNames[] = $this->bot->getDisplayNameOnGroup($groupId, $userId);
        }
        $displayName = Genchan::ArrayToText($displayNames, 0, ", ");
        $jsonDecode = json_decode(file_get_contents(RESPONSE), true);
        $mentionResponse = $jsonDecode["mention"];
        $result = str_replace("<name>", $displayName, $mentionResponse[rand(0, sizeof($mentionResponse) - 1)]);
        $kaomoji = Genchan::kaomojiGenerator();
        $result = $result . " " . $kaomoji;
        $this->bot->reply($result);
    }

    public function handleSticker()
    {
        $groupId = $this->bot->getGroupId();

        $firebaseController = new FirebaseController();
        $sticker = $this->bot->getStickerId();
        $parseText = $this->bot->getMessageText(true);

        foreach ($parseText as $text)
        {
            if ($sticker[$text] != null)
            {
                $this->bot->replyImage($sticker[$text]);
                return;
            }
        }

        $stickerGroup = $firebaseController->GetData("group-data/$groupId/sticker/");

        foreach ($parseText as $text)
        {
            if ($stickerGroup[$text] != null)
            {
                $this->bot->replyImage($stickerGroup[$text]);
                return;
            }
        }
    }

    public function handleAutoResponse()
    {
        $autoResponse = new AutoResponse();
        $parseText = $this->bot->getMessageText(true);
        $sizeOfParseText = sizeof($parseText);
        if ($sizeOfParseText > 7)
        {
            return;
        }

        $parseText = str_replace('?', '', $parseText);
        $response = json_decode(file_get_contents(RESPONSE), true);
        
        $result = '';
        foreach ($parseText as $text)
        {
            if ($response['response'][$text] != null)
            {
                $random = rand(0, sizeof($response['response'][$text]['reaction']) - 1);

                $selectedReaction = $response['response'][$text]['reaction'][$random];
                $value = $selectedReaction[0];
                $function = $selectedReaction[1];

                $feeling = $response['response'][$text]['feeling'];
                $isKaomojiAllowed = $response['response'][$text]['isKaomojiAllowed'];

                $package = array(
                    "feeling" => $feeling,
                    "isKaomojiAllowed" => $isKaomojiAllowed
                );
                $autoResponse->genchanAutoResponseReply($function, $value, $package);
                
                return;
            }
        }
    }
}

$index = new Index();