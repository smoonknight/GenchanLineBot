<?php

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

class Index
{
    private $bot;
    private $genchan;
    private $responseDecoration;
    private $keyword;
    private $textChat;

    public function __construct()
    {
        $this->bot = new LineBot();
        $this->genchan = new Genchan();
        $this->responseDecoration = new ResponseDecoration();
        $this->keyword = new Keyword();
        $this->textChat = $this->bot->getTextChat();

        $type = $this->bot->getType();
        $this->$type();
    }

    public function message()
    {
        $isKeyExist = $this->keyword->FindKeyword($this->bot->getMessageText(true)[0]);

        if ($isKeyExist)
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
        $sticker = $this->bot->getStickerId();
        $parseText = $this->bot->getMessageText(true);
        foreach ($parseText as $text) {
            if ($sticker[$text] != null) {
                $this->bot->replyImage($sticker[$text]);
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
                $result = $response['response'][$text]['reaction'][$random];
                $autoResponse->genchanAutoResponseReply($result[1], $result[0]);
                return;
            }
        }
    }
}

$index = new Index();