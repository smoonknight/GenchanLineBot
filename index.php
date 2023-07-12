<?php
require_once __DIR__ . '/lineBot.php';
require_once __DIR__ . '/genchan.php';
require_once __DIR__ . '/ResponseDecoration.php';
require_once __DIR__ . '/keyword.php';

include_once('simple_html_dom.php');

define("KEYWORD", "./data/keyword.json");
define("COMMAND", "./data/command.json");
define("STICKER", "./data/sticker.json");
define("RESPONSE", "./data/response.json");
define("CHARACTER", "./data/character.json");
define("BATTLE", "./data/battle.json");
define("KAOMOJI", "./data/kaomoji.json");
define("LOGCHAT", "./groupfiles/LOGCHAT_");
define("SETUPGROUP", "./groupfiles/SETUPGROUP_");
define("MAINTENANCE", "Sedang dalam perbaikan");
define("OPENAIAPIKEY", "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");

//admin U4fb6e2e7339dea74d4b27de42543dec1
//default on teyvat Cd7a78153c79fcc71c782f7bb65d84266
//baitgroup C9057879014a23255bf2176f9238e8255

$bot = new Linebot();
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

if ($bot->getJoinedMemberCondition() == true) {
    $bot->reply("Wah ada yang join, salken aku genchan (o´∀`o)");
}

//////// if(file_exists($bot->getUserId())){
////////     $file = file_get_contents($bot->getUserId());
////////     $json = json_decode($file, true);
////////     $chainKeyword = $json['chainKeyword'];
////////     $keyword->FindKeyword($chainKeyword);
//////// }

$keyword->FindKeyword(getParseText()[0]);

if (strlen(getTextChat()) < 400 && $bot->getMessageText() != null) {
    $path = LOGCHAT . $bot->getGroupId();
    $put = file_get_contents($path);
    file_put_contents($path, $put . $bot->getDisplayNameOnGroup($bot->getGroupId(), $bot->getUserId()) . " : " . $bot->getMessageText() . "\n");
}

if (in_array(getTextChat(), ['/keyword', '/help', '/command'])) {
    $command = getCommand();

    foreach ($command['genshin'] as $gn) {
        $resultGenshin .= "" . $gn . "\n";
    }
    foreach ($command['other'] as $ot) {
        $resultOther .= "" . $ot . "\n";
    }
    $responseDecorationArray = array(
        array("h1", "Webhook Response Key"),
        array("p", "Genshin Impact Key"),
        array("text", $resultGenshin),
        array("lb", ""),
        array("p", "Other Response Key"),
        array("text", $resultOther),
        array("footer", "")
    );
    $result = $responseDecoration->decorationResponse($responseDecorationArray);
    $bot->reply($result);
} elseif ($bot->getMentionId()) {
    $groupId = $bot->getGroupId();
    $userIds = $bot->getEntireMentionId();

    foreach ($userIds as $userId) {
        $displayName .= "kak " . $bot->getDisplayNameOnGroup($groupId, $userId) . ", ";
    }
    
    $response = array("<name>dicariin tuh", "<name>ada yang mention ayu bales", "<name>kemana ada yang tag tuh");
    $result = str_replace("<name>", $displayName, $response[rand(0, sizeof($response) - 1)]);
    $bot->reply($result);
} elseif (strpos(getTextChat(), ':') || getTextChat()[0] == ":") {
    $sticker = getStickerId();
    $parseText = getParseText();
    foreach ($parseText as $q) {
        if ($sticker[$q] != null) {
            $bot->replyImage($sticker[$q]);
        }
    }
} elseif (sizeof(getParseText()) < 7) {
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
