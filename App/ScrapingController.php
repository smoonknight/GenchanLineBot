<?php

namespace App;

require_once("simple_html_dom.php");

use App\Genchan;

class ScrapingController
{
    public static function GenshinImpactHoneyScraping($characterName)
    {
        // mendapatkan html element
        $url = @"https://genshin.honeyhunterworld.com/$characterName/?lang=EN";
        $html = file_get_html($url);

        $data = array();
        #region mengolah konten deskripsi karakter
        $characterDescription = $html->find('table[class="genshin_table main_table"]', 0);

        $characterDescriptionData = array();
        $increment = 0;
        foreach ($characterDescription->find("tr") as $tr)
        {
            $key = $tr->find("td", 0)->plaintext;
            if ($key == "")
            {
                $key = $tr->find("td", 1)->plaintext;
                $value = $tr->find("td", 2)->plaintext;
                $characterDescriptionData[$key] = $value;
                $increment++;
                continue;
            }

            $value = $tr->find("td", 1)->plaintext;

            if ($value == "")
            {
                $value = $increment;
            }
            $characterDescriptionData[$key] = $value;
            $increment++;
        }
        // mengolah array Rarity
        if (key_exists("Rarity", $characterDescriptionData))
        {
            $value = count($characterDescription->find("tr", $characterDescriptionData["Rarity"])->find("td", 1)->find('img[class="cur_icon"]'));
            $characterDescriptionData["Rarity"] = $value;
        }

        // mengolah array Character Ascension Materials
        if (key_exists("Character Ascension Materials", $characterDescriptionData))
        {
            $array = array();
            foreach ($characterDescription->find("tr", $characterDescriptionData["Character Ascension Materials"])->find("td", 1)->find("img") as $img)
            {
                $array[] = $img->alt;
            }
            $characterDescriptionData["Character Ascension Materials"] = Genchan::ArrayToText($array, "0", ",");
        }

        // mengolah array "Skill Ascension Materials
        if (key_exists("Skill Ascension Materials", $characterDescriptionData))
        {
            $array = array();
            foreach ($characterDescription->find("tr", $characterDescriptionData["Skill Ascension Materials"])->find("td", 1)->find("img") as $img)
            {
                $array[] = $img->alt;
            }
            $characterDescriptionData["Skill Ascension Materials"] = Genchan::ArrayToText($array, "0", ", ");
        }

        #endregion
        #region mengolah konten stat karakter

        $characterStat = $html->find('section[id="char_stats"]', 0);
        $characterStatData = array();

        $headerNames = array();
        foreach($characterStat->find("thead", 0)->find("td") as $theadTD)
        {
            $headerNames[] = $theadTD->plaintext;
        }
        $characterStatData[] = $headerNames;
        
        $data["Character Description"] = $characterDescriptionData;
        $data["Character Stat"] = $characterStatData;
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
?>