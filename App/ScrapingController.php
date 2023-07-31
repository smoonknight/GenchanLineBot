<?php

namespace App;

use App\simple_html_dom;

class ScrapingController
{
    public static function GenshinImpactHoneyScraping($characterName)
    {
        $url = @"https://genshin.honeyhunterworld.com/$characterName/?lang=EN";
        $html = file_get_html($url);
        $characterDescription = $html->find('', 0);

        if ($characterDescription)
        {
            $name = $characterDescription->find("tr", 2)->plaintext;
            return $name;
        }

        return "kosong";
    }
}
?>