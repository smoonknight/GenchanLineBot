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

        $data = array();

        foreach ($characterDescription->find("tr") as $tr)
        {
            $key = $tr->find("td", 0)->plaintext;
            if ($key == "")
            {
                $key = $tr->find("td", 1)->plaintext;
                $value = $tr->find("td", 2)->plaintext;
                $data[$key] = $value;
                continue;
            }
            if ($key == "Rarity")
            {
                $value = count($tr->find("td", 1)->find('img[class="cur_icon"]'));
                $data[$key] = $value;
                continue;
            }
            $value = $tr->find("td", 1)->plaintext;
            $data[$key] = $value;
        }

        unset($data["Character Ascension Materials"]);
        unset($data["Skill Ascension Materials"]);

        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
?>