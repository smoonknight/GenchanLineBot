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

    public function getCommand()
    {
        return json_decode(file_get_contents(COMMAND), true);
    }

    public function handleJoinedMember()
    {
        if ($this->bot->getJoinedMemberCondition() == true) {
            $this->bot->reply("Wah ada yang join, salken aku genchan (o´∀`o)");
        }
    }

    public function logChat()
    {
        if (strlen($this->textChat) < 400 && $this->textChat != null) {
            $path = LOGCHAT . $this->bot->getGroupId();
            $put = file_get_contents($path);
            file_put_contents($path, $put . $this->bot->getDisplayNameOnGroup($this->bot->getGroupId(), $this->bot->getUserId()) . " : " . $this->bot->getMessageText() . "\n");
        }
    }

    public function message()
    {
        $isKeyExist = $this->keyword->FindKeyword($this->bot->getMessageText(true)[0]);

        if ($isKeyExist) {
            return;
        }

        if (strpos($this->textChat, ':') || $this->textChat[0] == ":") {
            $this->handleSticker();
            return;
        }

        $this->handleAutoResponse();
    }

    public function memberJoined()
    {
        $this->handleMention();
    }

    public function handleMention()
    {
        $groupId = $this->bot->getGroupId();
        $userIds = $this->bot->getEntireMentionId();
        $displayName = "";

        foreach ($userIds as $userId) {
            $displayName .= "kak " . $this->bot->getDisplayNameOnGroup($groupId, $userId) . ", ";
        }

        $response = array("<name>dicariin tuh", "<name>ada yang mention ayu bales", "<name>kemana ada yang tag tuh");
        $result = str_replace("<name>", $displayName, $response[rand(0, sizeof($response) - 1)]);
        $this->bot->reply($result);
    }

    public function handleSticker()
    {
        $sticker = $this->bot->getStickerId();
        $parseText = $this->bot->getMessageText(true);
        foreach ($parseText as $q) {
            if ($sticker[$q] != null) {
                $this->bot->replyImage($sticker[$q]);
                return;
            }
        }
    }

    public function handleAutoResponse()
    {
        $parseText = $this->bot->getMessageText(true);
        if (sizeof($parseText) < 7) {
            $getTextChat = str_replace("?", "", $this->textChat);
            $parseText = str_replace('?', '', $parseText);
            $autoResponse = json_decode(file_get_contents(RESPONSE), true);
            $result = '';
            foreach ($parseText as $q) {
                if ($autoResponse['response'][$q] != null) {
                    $random = rand(1, sizeof($autoResponse['response'][$q]));
                    $result = $autoResponse['response'][$q][$random];
                    if ($result[1] == "reply") {
                        $kaomoji = mt_rand(0, 5) > 3 ? " " . $this->genchan->kaomojiGenerator(rand(1, 10)) : "";
                    }
                }
            }
            $this->bot->botAutoResponse($result[0] . $kaomoji, $result[1]);
        }
    }
}

$index = new Index();