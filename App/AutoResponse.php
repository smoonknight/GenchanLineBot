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
    public function genchanAutoResponseReply($function, $param, $feeling)
    {
		$this->$function($param, $feeling);
    }

    private function reply($param, $feeling)
    { 
        $kaomoji = mt_rand(0, 5) > 3 ? " " . $this->genchan->kaomojiGenerator($feeling) : "";
        $this->bot->reply($param . $kaomoji);
    }

    private function replyImage($param, $feeling)
    {
        $this->bot->replyImage($param);
    }

    private function replyAudio($param, $feeling)
    {
        $this->bot->replyAudio($param);
    }
}

?>