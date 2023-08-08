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

        foreach ($characterDescription->find("tr") as $td)
        {
            $key = $td->find("td", 0)->plaintext;
            $value = $td->find("td", 1)->plaintext;
            $data[$key] = $value;
        }

        return json_encode($data, JSON_PRETTY_PRINT);
    }
}
?>