<?php
namespace App;

class TextTemplate
{
    public static function GenshinImpactCharacter($characterData)
    {
        $data = array();
        #region character description
        $name = $characterData["Character Description"]["Name"];
        $title = $characterData["Character Description"]["Title"];
        $occupation = $characterData["Character Description"]["Occupation"];
        $rarity = $characterData["Character Description"]["Rarity"];
        $weapon = $characterData["Character Description"]["Weapon"];
        $element = $characterData["Character Description"]["Element"];
        $dayOfBirth = $characterData["Character Description"]["Day of Birth"];
        $monthOfBirth = $characterData["Character Description"]["Month of Birth"];
        $visionIntroduced = $characterData["Character Description"]["Vision (Introduced)"];
        $constellationIntroduced = $characterData["Character Description"]["Constellation (Introduced)"];
        $chineseSeuyu = $characterData["Character Description"]["Chinese Seuyu"];
        $japaneseSeuyu = $characterData["Character Description"]["Japanese Seuyu"];
        $englishSeuyu = $characterData["Character Description"]["English Seuyu"];
        $koreanSeuyu = $characterData["Character Description"]["Korean Seuyu"];
        $description = $characterData["Character Description"]["Description"];
        $characterAscensionMaterials = $characterData["Character Description"]["Character Ascension Materials"];
        $skillAscensionMaterials = $characterData["Character Description"]["Skill Ascension Materials"];



        $data[] = html_entity_decode("Karakter ini bernama $name, dengan gelar $title, dan bekerja sebagai $occupation. Ia memiliki tingkat kelangkaan $rarity, menggunakan senjata tipe $weapon, dan memiliki elemen $element. Lahir pada tanggal $dayOfBirth bulan $monthOfBirth, ia diperkenalkan dengan visi $visionIntroduced dan konstelasi $constellationIntroduced. Pemeran suaranya dalam bahasa Tionghoa dikenal sebagai $chineseSeuyu, dalam bahasa Jepang sebagai $japaneseSeuyu, dalam bahasa Inggris sebagai $englishSeuyu, dan dalam bahasa Korea sebagai $koreanSeuyu. Deskripsi karakter ini adalah: $description.\nBahan-bahan yang diperlukan untuk meningkatkan level karakter yaitu $characterAscensionMaterials\nBahan-bahan yang diperlukan dalam meningkatkan skillnya yaitu $skillAscensionMaterials", ENT_QUOTES, 'UTF-8');
        #endregion
        #region character stat
        $characterStat = $characterData["Character Stat"];
        $textCharacterStat = "";

        $characterStat80Level = $characterStat["12"];
        $lv = $characterStat80Level["Lv"];
        unset($characterStat80Level["Lv"]);
        unset($characterStat80Level["Materials"]);
        $characterStat80Level = ["Level" => $lv] + $characterStat80Level;

        foreach ($characterStat80Level as $key => $value)
        {
            $textCharacterStat .= @"$key : $value \n";
        }
        $textCharacterStat .= "\n";

        $characterStat90Level = $characterStat["14"];
        $lv = $characterStat90Level["Lv"];
        unset($characterStat90Level["Lv"]);
        $characterStat90Level = ["Level" => $lv] + $characterStat90Level;

        foreach ($characterStat90Level as $key => $value)
        {
            $textCharacterStat .= @"$key : $value \n";
        }

        $data[] = $textCharacterStat;

        return $data;
    }
}
?>