<?php

namespace App;

require_once("simple_html_dom.php");

use App\Genchan;

class ScrapingController
{
    public static function GenshinImpactHoneyScrapingCharacter($url)
    {
        // mendapatkan html element
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

        // membuat header
        $headerNames = array();
        $theadTDs = $characterStat->find("thead", 0)->find("td");
        foreach($theadTDs as $theadTD)
        {
            $headerNames[] = str_replace("%", "", $theadTD->plaintext);
        }
        $tr = $characterStat->find("tr");
        $requireBodyContentIndex = array(12, 14);

        foreach ($requireBodyContentIndex as $bodyContentIndex)
        {
            $increment = 0;
            $bodyContentArray = array();
            foreach ($tr[$bodyContentIndex]->find("td") as $td)
            {
                $plainText = $td->plaintext;
                $content = "";
                if ($plainText != "")
                {
                    $content = $plainText;
                }

                // deteksi apakah terdapat hyperlink yang memiliki gambar
                // dengan asumsi bahwa hal tersebut merupakan material
                $hyperlinks = $td->find("a");
                if (count($hyperlinks) != 0)
                {
                    $hyperlinkArray = array();
                    foreach ($hyperlinks as $hyperlink)
                    {
                        $hyperlinkArray[] = $hyperlink->plaintext . $hyperlink->find("img", 0)->alt;
                    }
                    $content = Genchan::ArrayToText($hyperlinkArray, 0, ", ");
                }

                if ($content == "")
                {
                    $increment++;
                    continue;
                }
                $bodyContentArray[$headerNames[$increment]] = $content;
                $increment++;
            }

            $characterStatData[$bodyContentIndex] = $bodyContentArray;
        }
        #endregion
        // #region mengolah konten skill karakter
        // $characterSkill = $html->find('section[id="char_skills"]', 0);
        // $characterSkillData = array();
        
        // $skillNormalAttack = $characterSkill->find('table[class="genshin_table skill_table"]', 0);
        // $skillNormalAttackText = $skillNormalAttack->find("tr", 0)->plaintext . "\n";
        // $skillNormalAttackText .= $skillNormalAttack->find("tr", 1)->plaintext . "\n";
        
        // #endregion
        #region mengolah konten informasi data pada karakter
        $characterInformationData["imageUrl"] = "https://genshin.honeyhunterworld.com/" . $html->find('img[class="main_image"]', 0)->src;
        $characterInformationData["refrenceUrl"] = $url;
        #endregion
        $data["Character Description"] = $characterDescriptionData;
        $data["Character Stat"] = $characterStatData;
        $data["Character Infomation"] = $characterInformationData;
        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public static function GenshinImpactHoneyScrapingAllCharacters()
    {
        $url = "https://genshin.honeyhunterworld.com/fam_chars/?lang=EN";
        $html = file_get_html($url);

        $characters = $html->find('section[id="characters"]', 0);
        $charactersData = array();
        foreach ($characters->find('tr') as $tr)
        {
            $charactersData[$tr->find('td', 1)->plaintext] = $tr->find('a', 0)->href;
        }

        return json_encode($charactersData, JSON_PRETTY_PRINT);
    }
}
?>