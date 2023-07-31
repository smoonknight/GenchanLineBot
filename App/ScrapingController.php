<?php

namespace App;

use Sunra\PhpSimple\HtmlDomParser;

class ScrapingController
{
    public static function GenshinImpactHoneyScraping($characterName)
    {
        $url = @"https://genshin.honeyhunterworld.com/$characterName/?lang=EN";
        $html = HtmlDomParser::file_get_html($url);

        $characterDescription = $html->find('table[class="genshin_table main_table"]', 0);

        if ($characterDescription)
        {
            $name = $characterDescription->find("tr", 0);
            return $name;
        }

        return "kosong";
    }
}
?>