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

        if ($characterDescription)
        {
            $name = $characterDescription->find("tr", 0)->find("td", 1)->plaintext;
            return $name;
        }

        return "kosong";
    }
}
?>