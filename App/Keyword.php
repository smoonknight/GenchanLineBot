<?php

namespace App;

use App\LineBot;
use App\Genchan;
use App\ResponseDecoration;
use App\FirebaseController;
use App\ScrapingController;

class Keyword{
    public $bot;
    private $genchan;
    private $responseDecoration;

    function __construct()
    {
        $this->bot = new Linebot();
        $this->genchan = new Genchan();
        $this->responseDecoration = new ResponseDecoration();
    }

    public function FindKeyword($key)
    {
        $keyList = json_decode(file_get_contents(KEYWORD), true);

        if ($keyList[$key] == null)
        {
            return false;
        }
        $keys = $keyList[$key];
        $this->$keys();
        return true;
    }

    public function GroupChatSummary()
    {
        $description = "ID Group : {groupId}\nName Group : {groupName}\n";
        $groupChatSummary = $this->bot->getGroupChatSummary($this->bot->getGroupId());
        $result = str_replace(["{groupId}","{groupName}"], [$groupChatSummary["groupId"], $groupChatSummary["groupName"]], $description);

        $this->bot->replyChatWithImage($result, $groupChatSummary["pictureUrl"]);
    }

    public function sticker()
    {
        $listSticker = "";
        foreach ($this->bot->getStickerId() as $key => $id) {
            $listSticker .= "" . $key . "\n";
        }
        $responseDecorationArray = array(
            array("h1", "List sticker"),
            array("text", $listSticker),
            array("footer", "")
        );
        $result = $this->responseDecoration->decorationResponse($responseDecorationArray);
        $this->bot->reply($result);
    }
    
    public function RollCharacterGenshinImpact()
    {
        $parseText = strtolower($this->bot->getMessageText());
        $username = "Username : " . $this->bot->getDisplayName($this->bot->getUserId());
        $gachaResult = "";
        $description = "";
        $max = 0;
        $min = 100;
        $guaranted = true;
        for ($int = 1; $int < 11; $int++) {
            switch ($parseText[1]) {
                case "-sr":
                    $rand = mt_rand(942, 1000) / 10;
                    break;
                case "-ssr":
                    $rand = 100;
                    break;
                default:
                    $rand = mt_rand(0, 1000) / 10;
                    break;
            }
            if ($int == 10) {
                $gachaResult .= ($guaranted ? "" . Genchan::roll(mt_rand(942, 1000) / 10) . "\n" : "" . Genchan::roll($rand) . "\n");
                break;
            }
            if ($guaranted) $guaranted = ($rand > 94.1 ? false : true);
            $gachaResult .= "" . Genchan::roll($rand) . "\n";
            $max = ($rand > $max ? $rand : $max);
            $min = ($rand < $min ? $rand : $min);
        }
        $description .= "║ Max rate : $max \n";
        $description .= "║ Min rate : $min \n";
        $responseDecorationArray = array(
            array("h1", "simulation results"),
            array("p", $username),
            array("text", $gachaResult),
            array("lb", ""),
            array("text", $description),
            array("footer", "")
        );
        $result = $this->responseDecoration->decorationResponse($responseDecorationArray);
        $this->bot->reply($result);
    }

    public function AskingGenchan()
    {
        $parseText = $this->bot->getMessageText(true);
        $text = Genchan::getTextRequest($parseText, 1);
        $result = $this->bot->openAI($text);
        $array = array();
        foreach ($result["choices"] as $choice)
        {
            $array[] = $choice["text"];
        }
        $this->bot->multiReply($array);
    }

    public function AskForWikipedia()
    {
        $parseText = $this->bot->getMessageText(true);
        $result = array();
            
        $parseText = str_replace('?', '', $parseText);
        $requestText = Genchan::getTextRequest($parseText, 3, '_');
        $wikipediaApiTexts = $this->bot->getWikipedia($requestText);
        if(count($wikipediaApiTexts) != 0){
            $result[] = "Berikut adalah informasi yang genchan dapat";
            foreach($wikipediaApiTexts as $texts){
                $result[] = $texts["extract"];
            }
            $this->bot->multiReply($result);
        }
        else {
            $this->bot->reply("Wakaranai~");
        }
    }
    public function GenerateNumber()
    {
        $this->bot->reply(mt_rand(0, 100));
    }

    public function FindOperatorList()
    {
        $url = "https://gamepress.gg/arknights/operator/suzuran";
        $scrap = file_get_html($url);
        $name = $scrap->find('div[id=page-title]',0)->find('h1', 0)->plaintext;
        $talents = $scrap->find('div[class=talent-cell]');
        $talentText = "";
        foreach($talents as $talent)
        {
            $talentCells = $talent->find('div[class=talent-cell]');
            if ($talentCells == null)
            {
                return;
            } 
            foreach ($talentCells as $talentCell)
            {
                $talentText .= $talentCell->plaintext . "\n";
            }
        }
        $images = $scrap->find('div[class=operator-image]');
        $imageArray = array();
        foreach($images as $image)
        {
            $imageArray[] = $image->find('a', 0)->href;
        }
        $this->bot->replyChatWithImage($talentText, $imageArray[0]);
    }

    // public function FindAllOperatorList()
    // {
    //             $count = 0;
    //     $array = array();
    //     foreach($list as $data) 
    //     {
    //         $count++;
    //         $array[] = $data->find('a')->plaintext;
    //     }
    //     $json = json_encode($array);
    //     file_put_contents("operator.json", $json);
    // }

    public function FindWeaponGenshinImpact()
    {
        $description = "Name : {name}\nType : {type}\nRarity : {rarity}\nBase Attack : {baseAttack}\nSub Stat : {subStat}\nPassive Name : {passiveName}\nPassive Description : {passiveDesc}\nAscension Material : {ascensionMaterial}\nWeapon ini didapat dari {location}\n";
        $parseText = $this->bot->getMessageText(true);
        $textRequest = Genchan::getTextRequest($parseText, 1, "-");
        $type = $this->bot->getGenshinDevType("weapons");
        $predict = Genchan::predictQuestion($textRequest, $type);
        if ($predict[0] == null)
        {
            $this->bot->reply("Tidak ditemukan~");
        }
        $select = $this->bot->getGenshinDevSelect("weapons", $predict[0]);
        $result = str_replace(["{name}", "{type}", "{rarity}","{baseAttack}", "{subStat}", "{passiveName}", "{passiveDesc}", "{ascensionMaterial}", "{location}"], 
                    [$select["name"], $select["type"], $select["rarity"], $select["baseAttack"], $select["subStat"], $select["passiveName"], $select["passiveDesc"], $select["ascensionMaterial"], $select["location"]],
                    $description);
        $result .= "\n" . $predict[1] . "\n";

        $responseDecorationArray = array(
            array("h1", "Weapon Information"),
            array("text", $result),
            array("footer", "")
        );
        $result = $this->responseDecoration->decorationResponse($responseDecorationArray);
        $this->bot->reply($result);
    }
    
    public function FindEntireWeaponGenshinImpact()
    {
        $result = "";  
        $weapon = "";
        $type = $this->bot->getGenshinDevType("weapons");
        foreach($type as $weap){
            $weapon .= "- " . str_replace("-", " ", $weap)  . "\n";
        }
        $responseDecorationArray = array(
            array("h1", "Weapons"),
            array("text", $weapon),
            array("text", "tulis /weapon [name] untuk lebih spesifik (tidak harus ditulis lengkap)"),
            array("footer", "")
        );
        $result = $this->responseDecoration->decorationResponse($responseDecorationArray);
        $this->bot->reply($result);
    }

    public function FindEntireCharacterGenshinImpact()
    {
        $result = "";
        $character = "";
        // $type = $this->bot->getGenshinDevType("characters");
        // foreach($type as $char){
        //     $character .= "- " . str_replace("-", " ", $char) . "\n";
        // }

        
        $responseDecorationArray = array(
            array("h1", "Characters"),
            array("text", $character),
            array("text", "tulis /character [name] untuk lebih spesifik (tidak harus ditulis lengkap)"),
            array("footer", "")
        );
        $result = $this->responseDecoration->decorationResponse($responseDecorationArray);
        $this->bot->reply($result);
    }

    public function FindCharacterGenshinImpact()
    {
        $result = ScrapingController::GenshinImpactHoneyScrapingCharacter("fischl_031");
        
        $this->bot->contextReply("Paimon", $result);
    }
    
    public function CalculateGenshinImpact()
    {
        $parseText = $this->bot->getMessageText(true);
        $atk = $parseText[1];
        $critDmg = $parseText[2];
        $critDmgResult = Genchan::calculateCritDmg($atk, $critDmg);
        $text = "= = = = = = = = =\nCalculate critical\n= = = = = = = = =\natk : <atk>\ncritical damage : <critDmg>\n\nCritical damage result is <critDmgResult>";
        $result = str_replace(['<atk>', '<critDmg>', '<critDmgResult>'], [$atk, $critDmg, $critDmgResult], $text);
        $this->bot->reply($result);
    }

    public function SimulateGenshinImpact()
    {
        $sumOfDmg = 0;
        $simulationResult = "";
        $parseText = $this->bot->getMessageText(true);

        if ($parseText[1] == NULL) 
        {
            $this->bot->reply("/simulate [atk] [talent] [elemental/physical bonus] [crit dmg] [crit rate] [level character] [level enemy]");
            die();
        }

        $atk = $parseText[1];
        $ability = $parseText[2];
        $dmgBonus = $parseText[3];
        $critDmg = $parseText[4];
        $critRate = ($parseText[5] != NULL ? $parseText[5] : 5);
        $levelCharacter = ($parseText[6] != NULL ? $parseText[6] : 90);
        $levelEnemy = ($parseText[7] != NULL ? $parseText[7] : 82);
        $resistEnemy = 10;
        $outgoingDmg = Genchan::calculateOutgoingDmg($atk, $ability, $dmgBonus);
        $nonCritDmgResult = Genchan::calculateCritDmg($outgoingDmg, 0);
        $critDmgResult = Genchan::calculateCritDmg($outgoingDmg, $critDmg);

        for ($int = 0; $int < 5; $int++)
        {
            $random = mt_rand(0, 1000) / 10;
            $dealtCritDmg = ($random < $critRate ? $critDmgResult : $outgoingDmg);
            $text = ($random < $critRate ? "dealt critical damage by " : "dealt damage by ");
            $incomingDmg = Genchan::calculateIncomingDmg($dealtCritDmg, $levelEnemy, $levelCharacter, $resistEnemy);
            $sumOfDmg += $incomingDmg;
            $simulationResult .= $text . $incomingDmg;
            $simulationResult .= ($int < 5 ? "\n" : '');
        }

        $text = "= = = = = = = = =\nDamage simulation\n= = = = = = = = =\nAtk : <atk>\nTalent : <ability>%\nElemental/physical bonus : <dmgBonus>%\nCritical damage : <critDmg>%\nCritical rate : <critRate>%\n\nIf outgoing damage(raw) non critical : <nonCriDmgResult>\nIf outgoing damage(raw) get critical : <critDmgResult>";
        $firstResult = str_replace(['<atk>', '<ability>', '<dmgBonus>', '<critDmg>', '<critRate>', '<critDmgResult>', '<nonCriDmgResult>'], [$atk, $ability, $dmgBonus, $critDmg, $critRate, $critDmgResult, $nonCritDmgResult], $text);
        $text = "- - - - - - - - - - - -\nResult demage\n- - - - - - - - - - - -\nIni adalah damage murni(atk, talent stat dan ele/pys bonus) ke musuh dengan reduksi def dan resist.\n\nName enemy : hilicurl\nLevel enemy : <levelEnemy>\nResist enemy : <resistEnemy>%\n\nDef multiplier((<levelCharacter> + 100) / (<levelCharacter> + <levelEnemy> + 200)) = <DefEnemy>\nRes multiplier(1 - <resistEnemy>) = <resEnemy>\n\n<simulationResult>\nTotal damage : <total>\nAverage damage : <avg>\n\nHasil bisa tidak akurat dikarenakan faktor dari weapon memiliki efek special ability, set artefak, constel, level enemy yang dilawan dan level character yang digunakan. jadi, jika weapon memiliki efek special ability/set artefak/constel coba untuk menambahkan ke ele/pys bonus (ele/pys bonus = ele/pys bonus + special ability + set artefak + constel)";
        $secondResult = str_replace(['<levelCharacter>', '<levelEnemy>', '<resistEnemy>', '<simulationResult>', '<total>', '<avg>', '<DefEnemy>', '<resEnemy>'], [$levelCharacter, $levelEnemy, $resistEnemy, $simulationResult, $sumOfDmg, ($sumOfDmg / 5), round(Genchan::getDefMultiplier($levelCharacter, $levelEnemy), 2), round(Genchan::getResMultiplier($resistEnemy), 2)], $text);
        $this->bot->secondReply($firstResult, $secondResult);
    }

    public function FindEventInformationGenshinImpact()
    {
        $CurrentEvents = "= = = = = = = = =\nCurrent Events\n= = = = = = = = =\n";
        $UpcomingEvents = "= = = = = = = = =\nUpcoming Events\n= = = = = = = = =\n";
        $PermanentEvents = "= = = = = = = = =\nPermanent Events\n= = = = = = = = =\n";
    
        $url = "https://genshin-impact.fandom.com/wiki/Event";
        $html = file_get_html($url);
        $CEtbody = $html->find('tbody', 1);
        $UEtbody = $html->find('tbody', 2);
        $PEtbody = $html->find('tbody', 3);
        $count = sizeof($CEtbody->find('tr')) - 1;

        for ($int = 1; $int < $count; $int++)
        {
            $collect = "";
            $collect .= $CEtbody->find('tr', $int)->find('td', 0)->plaintext . "\n";
            $collect .= "Duration : " . $CEtbody->find('tr', $int)->find('td', 1)->plaintext . "\n";
            $collect .= "Type : " . $CEtbody->find('tr', $int)->find('td', 2)->plaintext . "\n";
            $collect .= "https://genshin-impact.fandom.com/" . $CEtbody->find('tr', $int)->find('td', 0)->find('a', 0)->href;
            $CurrentEvents .= $collect . "\n";
        }
        $count = sizeof($UEtbody->find('tr')) - 1;

        for ($int = 1; $int < $count; $int++)
        {
            $collect = "";
            $collect .= $UEtbody->find('tr', $int)->find('td', 0)->plaintext . "\n";
            $collect .= "Duration : " . $UEtbody->find('tr', $int)->find('td', 1)->plaintext . "\n";
            $collect .= "Type : " . $UEtbody->find('tr', $int)->find('td', 2)->plaintext . "\n";
            $collect .= "https://genshin-impact.fandom.com/" . $UEtbody->find('tr', $int)->find('td', 0)->find('a', 0)->href;
            $UpcomingEvents .= $collect;
        }
        $count = sizeof($PEtbody->find('tr')) - 1;
        
        for ($int = 1; $int < $count; $int++)
        {
            $collect = "";
            $collect .= $PEtbody->find('tr', $int)->find('td', 0)->plaintext . "\n";
            $collect .= "Start Date : " . $PEtbody->find('tr', $int)->find('td', 1)->plaintext . "\n";
            $collect .= "Type : " . $PEtbody->find('tr', $int)->find('td', 2)->plaintext . "\n";
            $collect .= "https://genshin-impact.fandom.com/" . $PEtbody->find('tr', $int)->find('td', 0)->find('a', 0)->href;
            $PermanentEvents .= $collect;
        }
        $link = "Official : https://genshin.mihoyo.com/id/news\n" .
            "Fandom : https://genshin-impact.fandom.com/wiki/Events";
        $this->bot->fourthReply($CurrentEvents, $UpcomingEvents, $PermanentEvents, $link);
    }

    public function CheckProfileLine()
    {
        $parseText = $this->bot->getMessageText(true);
        $userId = ($parseText[1] != NULL ? $this->bot->getMentionId() : $this->bot->getUserId());
        $description = "Name : [Name]\nDescription : [Description]\nUser ID : [UserId]";
        $name = $this->bot->getDisplayName($userId);
        $profile = $this->bot->getstatusMessage($userId) != null ? $this->bot->getstatusMessage($userId) : "...";
        $pictureUrl = $this->bot->getpictureUrl($userId);
        $userId = $this->bot->getUserId();
        $description = str_replace(["[Name]", "[Description]", "[UserId]"], [$name, $profile, $userId], $description);
        $this->bot->replyChatWithImage($description, $pictureUrl);
    }

    public function CheckBotInfo()
    {
        $getInfo = $this->bot->getInfo();
        $name = $getInfo->{"displayName"};
        $pictureUrl = $getInfo->{"pictureUrl"};
        $endpoint = $this->bot->getEndpoint($getInfo);
        $jsonKeyword = json_decode(file_get_contents(KEYWORD), true);
        $countKeyword = count($jsonKeyword);
        $jsonResponse = json_decode(file_get_contents(RESPONSE), true);
        $countResponse = count($jsonResponse['response']);
        $text = "Saya [name]. Memiliki [keyword] keyword aktif, cek /keyword untuk listnya. saya juga sudah mempelajari [response] kata beserta jawabannya. Saya berada di [endpoint]. Terimakasih OwO";
        $result = str_replace(["[name]", "[keyword]","[response]","[endpoint]"],[$name, $countKeyword, $countResponse, $endpoint], $text);
        $this->bot->replyChatWithImage($result, $pictureUrl);
    }

    public function KissMentionLine()
    {
        $parseText = $this->bot->getMessageText(true);

        $userId = $this->bot->getUserId();
        $targetId = ($parseText[1] != NULL ? $this->bot->getMentionId() : $userId);
        $text = ($targetId == $userId ? "[Name] Mencium diri sendiri (selfcest moment)" : "[Name] mencium [target] dengan mesra ❤️❤️❤️");
        $name = $this->bot->getDisplayName($userId);
        $targetName = ($targetId == NULL ? $parseText[1] : $this->bot->getDisplayName($targetId));
        $text = str_replace(["[Name]", "[target]"], [$name, $targetName], $text);
        $this->bot->replyKiss($text);
    }

    public function FindEntireDegenerateText()
    {
        $result = "";
        $degen = array("/wangy", "/gemeteran", "/simp", "/klaimwaifu", "/kasus");
        foreach ($degen as $text) {
            $result .= $text . " [name]\n";
        }
        $this->bot->reply($result);
    }

    public function WangyCopypasta()
    {
        $parseText = $this->bot->getMessageText(true);
        $text = strtoupper(Genchan::getTextRequest($parseText, 1));
        if ($parseText[1] != NULL) {
            if (sizeof($parseText) < 4) {
                $result = Genchan::wangyGenerator($text);
                $this->bot->reply($result);
            } else {
                $this->bot->reply("nama terlalu panjang");
            }
        }
    }

    public function GemeteranCopypasta()
    {
        $parseText = $this->bot->getMessageText(true);
        $text = strtoupper(Genchan::getTextRequest($parseText, 1));
        if ($parseText[1] != NULL) {
            if (sizeof($parseText) < 4) {
                $result = Genchan::gemeteranGenerator($text);
                $this->bot->reply($result);
            } else {
                $this->bot->reply("nama terlalu panjang");
            }
        }
    } 

    public function SimpCopypasta()
    {
        $parseText = $this->bot->getMessageText(true);
        $text = strtoupper(Genchan::getTextRequest($parseText, 1));
        if ($parseText[1] != NULL) {
            if (sizeof($parseText) < 4) {
                $result = Genchan::simpGenerator($text);
                $this->bot->reply($result);
            } else {
                $this->bot->reply("nama terlalu panjang");
            }
        }
    } 

    public function KlaimWaifuCopypasta()
    {
        $parseText = $this->bot->getMessageText(true);
        $text = strtoupper(Genchan::getTextRequest($parseText, 1));
        if ($parseText[1] != NULL) {
            if (sizeof($parseText) < 4) {
                $result = Genchan::klaimWaifuGenerator($text);
                $this->bot->reply($result);
            } else {
                $this->bot->reply("nama terlalu panjang");
            }
        }
    } 
    
    public function ButuhkagaCopypasta()
    {
        $parseText = $this->bot->getMessageText(true);
        $text = Genchan::getTextRequest($parseText, 1);
        if ($parseText[1] != NULL) {
            if (sizeof($parseText) < 4) {
                $result = Genchan::butuhKagaGenerator($text);
                $this->bot->multiReply($result);
            } else {
                $this->bot->reply("nama terlalu panjang");
            }
        }
    }

    public function KasusCopypasta()
    {
        $parseText = $this->bot->getMessageText(true);
        $text = strtoupper(Genchan::getTextRequest($parseText, 1));
        if ($parseText[1] != NULL) {
            if (sizeof($parseText) < 4) {
                $result = Genchan::kasusGenerator($text);
                $this->bot->reply($result);
            } else {
                $this->bot->reply("nama terlalu panjang");
            }
        }
    }

    public function FindPictureBooru()
    {
        $result = "";
        $parseText = $this->bot->getMessageText(true);
        $requestText = Genchan::getTextRequest($parseText, 1);
        $tag = explode(" & ", trim($requestText));
        foreach ($tag as $tg) {
            $result .= "*" . str_replace(' ', '_', $tg) . "*%20";
        }
        $data = $this->bot->getSafebooruByTags($result);
    }

    public function MockingText()
    {
        $parseText = $this->bot->getMessageText(true);
        $result = Genchan::mockingGenerator(Genchan::getTextRequest($parseText, 1));
        $this->bot->reply($result);
    }

    public function Debugging()
    {
        $this->bot->paimonReply("Test");
        // $testing = new FirebaseController();
        // $result = $testing->PostData();
        // $this->bot->reply($result);
    }
    public function PingGenchan()
    {
        date_default_timezone_set('asia/jakarta');
        $responseDecorationArray = array(
            array("h1", "Check status"),
            array("textLabel", "OK"),
            array("textLabel", date("j M Y H:i:s T")),
            array("footer", "")
        );
        $result = $this->responseDecoration->decorationResponse($responseDecorationArray);
        $this->bot->reply($result);
    } 

    public function EpicBattle()
    {
        $logs = [];
        $result = [];
        $active = true;
        $countResult = 1;
	    $checkPlayer = explode(":", trim((str_replace(["/battle","@"], "", $this->bot->getMessageText()))));
        $battlePrefix = json_decode(file_get_contents(BATTLE), true);
        $attackText = "⚔<player> <description> mengakibatkan damage sebesar <damage>, hp musuh tersisa <hp>";
        $healText = "🩸<player> <description> mengakibatkan pertambahan darah sebesar <heal>, hpnya menjadi <hp>";
        $winnerText = "\nPertarungan dimenangkan oleh <player> !!!, selamat ya kak";
        $startText = "HP <player1> : <hp1>\nHP <player2> : <hp2>\n";

        if(sizeof($checkPlayer) == null || sizeof($checkPlayer) > 2)
        {
            $this->bot->reply("Tidak ada pemain dan pastikan hanya terdapat 2 player saja (mention dan atau tulis manual, contoh '/battle @satria : @coek' atau '/battle eren : cicak')");
        }

        $playerOne = trim($checkPlayer[0]);
        $playerTwo = trim($checkPlayer[1]);
        $hpPlayerOne = 10000;
        $hpPlayerTwo = 10000;

        $startText = str_replace(["<player1>","<hp1>","<player2>","<hp2>"], [$playerOne, $hpPlayerOne, $playerTwo, $hpPlayerTwo], $startText);
        
        while($active)
        {
            $random = rand(1, sizeof($battlePrefix));
            $deal = round(rand($battlePrefix[$random]["min"], $battlePrefix[$random]["max"]),-2);
            $description = $battlePrefix[$random]["description"];
            $category = $battlePrefix[$random]["category"];
    
            if($category == "attack")
            {
                $damage = $hpPlayerTwo - $deal;
                $hpPlayerTwo = $damage < 0 ? 0 : $damage;
                $logs[] = str_replace(["<player>", "<description>", "<damage>", "<hp>"],[$playerOne, $description, $deal, $hpPlayerTwo], $attackText) . "\n";
            }
            else if($category == "healing")
            {
                $hpPlayerOne += $deal;
                $logs[] = str_replace(["<player>", "<description>", "<heal>", "<hp>"],[$playerOne, $description, $deal, $hpPlayerOne], $healText) . "\n";
            }
            if(empty($hpPlayerTwo)){
                $logs[] = str_replace("<player>",$playerOne, $winnerText);
                $active = false;
                continue;
            }

            $random = rand(1, sizeof($battlePrefix));
            $deal = round(rand($battlePrefix[$random]["min"], $battlePrefix[$random]["max"]),-2);
            $description = $battlePrefix[$random]["description"];
            $category = $battlePrefix[$random]["category"];
    
            if($category == "attack"){
                $damage = $hpPlayerOne - $deal;
                $hpPlayerOne = $damage < 0 ? 0 : $damage;
                $logs[] = str_replace(["<player>", "<description>", "<damage>", "<hp>"],[$playerTwo, $description, $deal, $hpPlayerOne], $attackText) . "\n";
            }
            else if($category == "healing"){
                $hpPlayerTwo += $deal;
                $logs[] = str_replace(["<player>", "<description>", "<heal>", "<hp>"],[$playerTwo, $description, $deal, $hpPlayerTwo], $healText) . "\n";
            }
            if(empty($hpPlayerOne)){
                $logs[] = str_replace("<player>",$playerTwo, $winnerText);
                $active = false;
                continue;
            }
        }

        foreach($logs as $log)
        {
            $result[$countResult] .= $log;
            if(strlen($result[$countResult] > 450)){
                $countResult++;
            }
            else if($countResult > 5){
                break;
            }
        }
        $responseDecorationArray = array(
            array("h1", "Epic Battle"),
            array("text", $startText),
            array("text", "Pertarungan dimulai!!!"),
            array("footer", "")
        );
        $result[0] = $this->responseDecoration->decorationResponse($responseDecorationArray);
        $this->bot->multiReply($result);  
    }

    public function RetriveLogChat()
    {
        $this->bot->reply(MAINTENANCE);
    }

    public function RetriveLogChat_old()
    {
        if ($this->bot->getGroupId() == null)
        {
            $this->bot->reply("Groupchat only");
            return;
        }
        $parseText = $this->bot->getMessageText(true);
        $logChat = [];
        $path = LOGCHAT . $this->bot->getGroupId();


        $file = file($path);
        $logChat = $file;
        if ($parseText[1] != null)
        {
            $searchword = Genchan::getTextRequest($parseText);
            $logChat = array_filter($logChat, function($var) use ($searchword) { return preg_match("/\b$searchword\b/i", $var); });
        }
        $logChat = array_slice($logChat, -20);
        $responseDecorationArray = array(
            array("h1", "Log Chat"),
            array("text", "Message Text Only\n"),
            array("lb", ""),
            array("text", ($file != null ? implode("", $logChat) : "Tidak ada"))
        );
        $result = $this->responseDecoration->decorationResponse($responseDecorationArray);
        $this->bot->reply($result);
    }

    public function KeywordList()
    {
        $parseText = $this->bot->getMessageText(true);
        $command = $this->bot->getCommand();
        $resultGenshin = "";
        $resultOther = "";

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
        
        $result = $this->responseDecoration->decorationResponse($responseDecorationArray);
        $this->bot->reply($result);
    }
}