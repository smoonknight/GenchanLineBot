<?php

namespace App;

class AutoResponse
{
    private $bot;
    private $genchan;

    function __construct()
    {
        $this->bot = new LineBot();
        $this->genchan = new Genchan();
    }
    public function genchanAutoResponseReply($function, $param)
    {
		$this->$function($param);
    }

    private function reply($param)
    { 
        $kaomoji = mt_rand(0, 5) > 3 ? " " . $this->genchan->kaomojiGenerator() : "";
        $this->bot->reply($param . $kaomoji);
    }

    private function replyImage($param)
    {
        $this->bot->replyImage($param);
    }

    private function replyAudio($param)
    {
        $this->bot->replyAudio($param, 10000);
    }
}

?>