<?php
class Genchan {

    public function predictQuestion($key, $sources, $returnSuggest = true){
        $result = array();
        $suggest = array();
        foreach($sources as $source){
            if(str_contains($source, $key))
            {
                $result[0] = $source;
                $suggest[] = $source;
            }
        }
        if ($returnSuggest){
            $result[1] = "Mungkin maksud kakak : " . $this->getTextRequest($suggest, 0, ", ");
        }
        return $result;
    }
    public function SplitToArray($text)
    {
        $count = 0;
        $array = array();
        for($int = 0; $count < strlen($text); $int++)
        {
            if ($count < strlen($text))
            {
                $array[] = substr($text, $count, 500);
                $count += 500;
            }
        }
        return array_slice($array, 0, 5, true);
        
    }
    public function answerQuestion($question) {
        $bot = new Linebot();
        if($question <= 20) {
            $answer = "iya";
            $bot->reply($answer);
        }
        else if($question <= 40 && $question >= 21) {
            $answer = "tidak";
            $bot->reply($answer);
        }
        else if($question <= 60 && $question >= 41) {
            $answer = "mungkin";
            $bot->reply($answer);
        }
        else if($question <= 80 && $question >= 61) {
            $answer = "spn kh bgt?";
            $bot->reply($answer);
        }
        else if($question <= 100 && $question >= 81) {
            $answer = "tidak...";
            $nextAnswer = "...salah lagi :D";
            $bot->secondReply($answer, $nextAnswer);
        }
        else if($question <= 120 && $question >= 101) {
            $answer = "100% iya";
            $bot->reply($answer);
        }
    }
    
    public function getTextRequest($array, $index = 1, $space = " ") {
        $result = '';
        for($int = $index; $int < sizeof($array); $int++){
            $result .= $array[$int];
            $result .= ($int < (sizeof($array)-1) ? $space : "");
        }
        return $result;
    }
    
    public function getLinkWebsite($url, $fill, $parse, $index) {
        $text = '';
        for($int = $index; $int < sizeof($fill); $int++){
            $text .= $fill[$int];
            $text .= ($int < (sizeof($fill)-1) ? $parse : "");
        }
        return str_replace('<url>', $text, $url);
    }

    public function keyFilter($text){
        return str_replace(['/db/weapon/','/db/char/','/?lang=EN'],['',''],$text);
    }


    public function roll($rate) {
        $character_name = json_decode(file_get_contents('character.json'), true);
        if($rate <= 94.1) {
            $get = "★★★ - " . $this->searchCharacterById($character_name["r"],rand(1,sizeof($character_name["r"])));
        }
        else if($rate <= 99.3 && $rate > 94.1) {
            $get = "★★★★ - " . $this->searchCharacterById($character_name["sr"],rand(1,sizeof($character_name["sr"])));
        }
        else if($rate <= 100 && $rate > 99.3) {
            $get = "★★★★★ - " . $this->searchCharacterById($character_name["ssr"],rand(1,sizeof($character_name["ssr"])));
        }
        return $get;
    }
    public function recruit($rate) {
        $character_name = json_decode(file_get_contents('character-counterside.json'));
        if($rate <= 42.4) {
            $get = "N - " . $this->searchCharacterById($character_name->n,rand(1,11));
        }
        else if($rate <= 81.4 && $rate > 42.5) {
            $get = "R - " . $this->searchCharacterById($character_name->r,rand(1,34));
        }
        else if($rate <= 96.4 && $rate > 81.5) {
            $get = "SR - " . $this->searchCharacterById($character_name->sr,rand(1,34));
        }
        else if($rate <= 100 && $rate > 96.5) {
            $get = "SSR - " . $this->searchCharacterById($character_name->ssr,rand(1,27));
        }
        return $get;
    }

    function kaomojiGenerator($random) {
        $kaomoji = json_decode(file_get_contents(KAOMOJI), true);
        return array_search($random, $kaomoji);
    }

    function searchCharacterById($array,$id) {
        foreach($array as $name => $val) {
            if($val == $id){
                return $name;
            }
        }
    } 

    function calculateCritDmg($outgoingDmg, $critDmg){
        return round(($outgoingDmg * (1+($critDmg/100))),0);
    }

    function calculateOutgoingDmg($atk, $ability, $dmgBonus) {
        return round(($atk * ($ability/100) * (1+($dmgBonus/100))),0);
    }

    function calculateIncomingDmg($outgoingDmg,$levelEnemy,$levelCharacter,$resistEnemy) {
        $defMultiplier = $this->getDefMultiplier($levelCharacter,$levelEnemy);
        $resMultiplier = $this->getResMultiplier($resistEnemy);
        return round($outgoingDmg * $defMultiplier * $resMultiplier,0);
    }

    function getDefMultiplier($levelCharacter,$levelEnemy){
        return ($levelCharacter+100)/($levelCharacter+$levelEnemy+200);
    }
    function getResMultiplier($resistEnemy){
        return (1-($resistEnemy/100));
    }

    function wangyGenerator($key) {
        return str_replace("<name>",$key,"<name> <name> <name>   ❤️ ❤️ WANGI WANGI WANGI WANGI HU HA HU HA HU HA, aaaah baunya rambut <name> wangi aku mau nyiumin aroma wanginya <name> AAAAAAAAH ~ Rambutnya.... aaah rambutnya juga pengen aku elus-elus ~~~~ AAAAAH <name> keluar pertama kali di anime juga manis ❤️ ❤️ ❤️ banget AAAAAAAAH <name> AAAAA LUCCUUUUUUUUUUUUUUU............<name> AAAAAAAAAAAAAAAAAAAAGH ❤️ ❤️ ❤️apa ? <name> itu gak nyata ? Cuma HALU katamu ? nggak, ngak ngak ngak ngak NGAAAAAAAAK GUA GAK PERCAYA ITU DIA NYATA NGAAAAAAAAAAAAAAAAAK PEDULI BANGSAAAAAT !! GUA GAK PEDULI SAMA KENYATAAN POKOKNYA GAK PEDULI. ❤️ ❤️ ❤️ <name> gw ...<name> di laptop ngeliatin gw, <name> .. kamu percaya sama aku ? aaaaaaaaaaah syukur <name> aku gak mau merelakan <name> aaaaaah ❤️ ❤️ ❤️ YEAAAAAAAAAAAH GUA MASIH PUNYA <name> SENDIRI PUN NGGAK SAMA AAAAAAAAAAAAAAH");
    }

    function gemeteranGenerator($key) {
        return str_replace("<name>",$key,"Bro, gue gemeteran. GUE GEMETERAN ANJING Gue gak pernah mau berkembangbiak dengan siapapun lebih daripada seorang <name>. Badannya yang cakep, TETE GEDE, pinggul seksi dari seorang BIDADARI. Jujur aja, sakit hati kalau tau KENYATAANNYA gue GAK AKAN PERNAH BISA BUAT KAWIN SAMA DIA, ngewarisin gen gue lewat dia, dan ngebiarin dia ngelahirin anak yang sempurna dari gue.Gue rela ngelakuin APAPUN demi kesempatan buat bikin <name> hamil. A P A P U N. Dan gue bener-bener gk bisa terima kenyataan. Kenapa Authornya rela bikin suatu hal yang sempurna kyk dia? Buat ngegoda kita? NGETAWAIN KITA DI DEPAN MUKA?SUMPAH BRO, GUE UDAH BENER BENER GAK TAHAN. ANJING.");
    }

    function simpGenerator($key) {
        return str_replace("<name>",$key,"Buruan, panggil gue SIMP, ato BAPERAN. ini MURNI PERASAAN GUE. Gue pengen genjot bareng <name>. Ini seriusan, suaranya yang imut, mukanya yang cantik, apalagi badannya yang aduhai ningkatin gairah gue buat genjot <name>. Setiap lapisan kulitnya pengen gue jilat. Saat gue mau crot, gue bakal moncrot sepenuh hati, bisa di perut, muka, badan, teteknya, sampai lubang burit pun bakal gue crot sampai puncak klimaks. Gue bakal meluk dia abis gue moncrot, lalu nanya gimana kabarnya, ngrasain enggas bareng saat telanjang. Dia bakal bilang kalau genjotan gue mantep dan nyatain perasaannya ke gue, bilang kalo dia cinta ama gue. Gue bakal bilang balik seberapa gue cinta ama dia, dan dia bakal kecup gue di pipi. Terus kita ganti pakaian dan ngabisin waktu nonton film, sambil pelukan ama makan hidangan favorit. Gue mau <name> jadi pacar, pasangan, istri, dan idup gue. Gue cinta dia dan ingin dia jadi bagian tubuh gue. Lo kira ini copypasta? Kagak cok. Gue ngetik tiap kata nyatain prasaan gue. Setiap kali elo nanya dia siapa, denger ini baik-baik : DIA ISTRI GUE. Gue sayang <name>, dan INI MURNI PIKIRAN DAN PERASAAN GUE.");
    }

    function klaimWaifuGenerator($key) {
        return str_replace("<name>",$key,"Sejujurnya gw ga nyangka ama tindakan lo yg ga dewasa begini Kita udh temenan dri kecil ,melalui berbagai kenangan ,tapi sikaplo begini ke gw ,ga habis pikir. Padahal sudah berjanji tidak mengusik hubungan satu sama lain lagi ,tapi maksud tindakan mu sekarang ini apa? Tiba tiba di pagi bangun tidur lu make Pp <name>. Lu kira lucu begitu anjing? Make waifu pp org seenaknya? Ngeklaim pula bangsad maksudnya apa apaan coba . Pertemanan dari kecil kita ga dihargai sama sekali.\nGw tunggu klarifikasi lo ");
    }

    function kasusGenerator($key) {
        return str_replace("<name>",$key,"Jika dalam kasus investigasi oleh entitas federal atau sejenisnya, saya tidak terlibat dengan grup ini atau dengan orang-orang di dalamnya, saya tidak tahu bagaimana saya di sini, mungkin ditambahkan oleh pihak ketiga. Saya juga tidak  mendukung tindakan apa pun oleh anggota grup ini. Saya menyatakan <name> bertanggung jawab sepenuhnya atas ucapan yang dilontarkan di grup ini.");
    }

    function butuhKagaGenerator($key) {
        $result = array();
        $result[] = str_replace("<name>",$key,"awkwkkw tapi lu butuh kagak <name>");
        $result[] = str_replace("<name>",$key,"kalo butuh sih gacha ae");
        $result[] = str_replace("<name>",$key,"gw sih soalnya kalo gacha <name>, ntar malah gak ada tempat");
        return $result;
    }

    function mockingGenerator($key) {
        $clone = '';
        $skip = 0;
        for($i = 0; $i < strlen($key); $i++) {
            $clone[$i+$skip] = (rand(0, 100) > 50 ? strtolower($key[$i]) : strtoupper($key[$i]));
            if(mt_rand(0,100) > 65 && $key[$i] != " ") {
                $skip++;
                $clone[$i+$skip] = $key[$i]; 
            }
        }
        return $clone;
    }
}
?>