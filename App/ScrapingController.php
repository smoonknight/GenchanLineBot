<?php

namespace App;

require_once("simple_html_dom.php");

class ScrapingController
{
    public static function GenshinImpactHoneyScraping($characterName)
    {
        $url = @"https://genshin.honeyhunterworld.com/$characterName/?lang=EN";
        $html = file_get_html($url);

        $characterDescription = $html->find('table[class="genshin_table main_table"]', 0);

        $name = $characterDescription->find("tr", 1)->find("td", 1)->plaintext;
        $title = $characterDescription->find("tr", 2)->find("td", 1)->plaintext;
        $occupation = $characterDescription->find("tr", 3)->find("td", 1)->plaintext;
        
        $rarity = $characterDescription->find("tr", 4)->find("td", 1)->plaintext;
        $weapon = $characterDescription->find("tr", 5)->find("td", 1)->plaintext;
        $element = $characterDescription->find("tr", 6)->find("td", 1)->plaintext;
        $dayOfBirth = $characterDescription->find("tr", 7)->find("td", 1)->plaintext;
        $monthOfBirth = $characterDescription->find("tr", 8)->find("td", 1)->plaintext;
        $visionIntroduced = $characterDescription->find("tr", 9)->find("td", 1)->plaintext;
        $constellationIntroduced = $characterDescription->find("tr", 10)->find("td", 1)->plaintext;
        $chineseSeuyu = $characterDescription->find("tr", 11)->find("td", 1)->plaintext;
        $japaneseSeuyu = $characterDescription->find("tr", 12)->find("td", 1)->plaintext;
        $englishSeuyu = $characterDescription->find("tr", 13)->find("td", 1)->plaintext;
        $koreanSeuyu = $characterDescription->find("tr", 14)->find("td", 1)->plaintext;
        $description = $characterDescription->find("tr", 15)->find("td", 1)->plaintext;
        
        return $constellationIntroduced;
    }
}
?>