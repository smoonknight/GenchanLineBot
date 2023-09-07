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
    public function genchanAutoResponseReply($function, $value, $package)
    {
		$this->$function($value, $package);
    }

    private function reply($value, $package)
    {
        if ($package['isKaomojiAllowed'] == false)
        {
            $this->bot->reply($value);
            return;
        }
        $kaomoji = mt_rand(0, 5) > 3 ? " " . $this->genchan->kaomojiGenerator($package['feeling']) : "";
        $this->bot->reply($value . $kaomoji);
    }

    private function replyImage($value, $package)
    {
        $this->bot->replyImage($value);
    }

    private function replyAudio($value, $package)
    {
        $this->bot->replyAudio($value);
    }
}

?>