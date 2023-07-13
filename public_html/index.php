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

//admin U4fb6e2e7339dea74d4b27de42543dec1
//default on teyvat Cd7a78153c79fcc71c782f7bb65d84266
//baitgroup C9057879014a23255bf2176f9238e8255

$bot = new LineBot();
$genchan = new Genchan();
$responseDecoration = new ResponseDecoration();
$keyword = new Keyword();

function getTextChat()
{
    $bot = new Linebot();
    return strtolower($bot->getMessageText());
}

function getParseText()
{
    $bot = new Linebot();
    return explode(' ', trim(strtolower($bot->getMessageText())));
}

function getStickerId()
{
    return json_decode(file_get_contents(STICKER), true);
}

function getCommand()
{
    return json_decode(file_get_contents(COMMAND), true);
}

$bot->getType()();

if ($bot->getJoinedMemberCondition() == true)
{
    $bot->reply("Wah ada yang join, salken aku genchan (o´∀`o)");
}

//////// if(file_exists($bot->getUserId())){
////////     $file = file_get_contents($bot->getUserId());
////////     $json = json_decode($file, true);
////////     $chainKeyword = $json['chainKeyword'];
////////     $keyword->FindKeyword($chainKeyword);
//////// }

$isKeyExist = $keyword->FindKeyword(getParseText()[0]);

if ($isKeyExist)
{
    return;
}

if (strlen(getTextChat()) < 400 && $bot->getMessageText() != null)
{
    $path = LOGCHAT . $bot->getGroupId();
    $put = file_get_contents($path);
    file_put_contents($path, $put . $bot->getDisplayNameOnGroup($bot->getGroupId(), $bot->getUserId()) . " : " . $bot->getMessageText() . "\n");
}

if ($bot->getMentionId()) 
{
    $groupId = $bot->getGroupId();
    $userIds = $bot->getEntireMentionId();

    foreach ($userIds as $userId) {
        $displayName .= "kak " . $bot->getDisplayNameOnGroup($groupId, $userId) . ", ";
    }
    
    $response = array("<name>dicariin tuh", "<name>ada yang mention ayu bales", "<name>kemana ada yang tag tuh");
    $result = str_replace("<name>", $displayName, $response[rand(0, sizeof($response) - 1)]);
    $bot->reply($result);
}

function message()
{
    $bot = new LineBot();
    if (strpos(getTextChat(), ':') || getTextChat()[0] == ":") 
    {
        $sticker = getStickerId();
        $parseText = getParseText();
        foreach ($parseText as $q) {
            if ($sticker[$q] != null) {
                $bot->replyImage($sticker[$q]);
                return;
            }
        }
    } 
}

if (sizeof(getParseText()) < 7) 
{
    $getTextChat = str_replace("?", "", getTextChat());
    $parseText = str_replace('?', '', getParseText());
    $autoResponse = json_decode(file_get_contents(RESPONSE), true);
    $result = '';
    foreach ($parseText as $q) {
        if ($autoResponse['response'][$q] != null) {
            $random = rand(1, sizeof($autoResponse['response'][$q]));
            $result = $autoResponse['response'][$q][$random];
            if ($result[1] == "reply") {
                $kaomoji = mt_rand(0, 5) > 3 ? " " . $genchan->kaomojiGenerator(rand(1, 10)) : "";
            }
        }
    }
    $bot->botAutoResponse($result[0] . $kaomoji, $result[1]);
}