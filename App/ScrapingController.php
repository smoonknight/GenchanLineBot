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

        // mengolah konten deskripsi karakter
        $characterDescription = $html->find('table[class="genshin_table main_table"]', 0);

        $increment = 0;
        $data = array();

        foreach ($characterDescription->find("tr") as $tr)
        {
            $key = $tr->find("td", 0)->plaintext;
            if ($key == "")
            {
                $key = $tr->find("td", 1)->plaintext;
                $value = $tr->find("td", 2)->plaintext;
                $data[$key] = $value;
                $increment++;
                continue;
            }

            $value = $tr->find("td", 1)->plaintext;

            if ($value == "")
            {
                $value = $increment;
            }
            $data[$key] = $value;
            $increment++;
        }

        // mengolah array Rarity
        if (key_exists("Rarity", $data))
        {
            $value = count($characterDescription->find("tr", $data["Rarity"])->find("td", 1)->find('img[class="cur_icon"]'));
            $data["Rarity"] = $value;
        }

        // mengolah array Character Ascension Materials
        if (key_exists("Character Ascension Materials", $data))
        {
            $array = array();
            foreach ($characterDescription->find("tr", $data["Character Ascension Materials"])->find("td", 1)->find("img") as $img)
            {
                $array[] = $img->alt;
            }
            $data["Character Ascension Materials"] = Genchan::ArrayToText($array, "0", ",");
        }

        // mengolah array "Skill Ascension Materials
        if (key_exists("Skill Ascension Materials", $data))
        {
            $array = array();
            foreach ($characterDescription->find("tr", $data["Skill Ascension Materials"])->find("td", 1)->find("img") as $img)
            {
                $array[] = $img->alt;
            }
            $data["Skill Ascension Materials"] = Genchan::ArrayToText($array, "0", ",");
        }
        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
?>