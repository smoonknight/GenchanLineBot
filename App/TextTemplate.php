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

        $data[] = "Karakter ini bernama $name, dengan gelar $title, dan bekerja sebagai $occupation. Ia memiliki tingkat kelangkaan $rarity, menggunakan senjata tipe $weapon, dan memiliki elemen $element. Lahir pada tanggal $dayOfBirth bulan $monthOfBirth, ia diperkenalkan dengan visi $visionIntroduced dan konstelasi $constellationIntroduced. Pemeran suaranya dalam bahasa Tionghoa dikenal sebagai $chineseSeuyu, dalam bahasa Jepang sebagai $japaneseSeuyu, dalam bahasa Inggris sebagai $englishSeuyu, dan dalam bahasa Korea sebagai $koreanSeuyu. Deskripsi karakter ini adalah: $description.";
        #endregion
        return $data;
    }
}
?>