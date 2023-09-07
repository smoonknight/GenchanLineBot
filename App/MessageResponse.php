<?php
namespace App;

use App\Keyword;
use App\LineBot;

class MessageResponse
{
    private $keyword;
    private $bot;
    function __construct()
    {
        $this->keyword = new Keyword();
        $this->bot = new LineBot();
    }
    
    public function response() : bool
    {
        $getMessageType = $this->bot->getMessageType();
        $sended = $this->$getMessageType();
        return $sended;
    }

    public function text() : bool
    {
        $isKeyExist = $this->keyword->FindKeyword($this->bot->getMessageText(true)[0]);
        return $isKeyExist;
    }

    public function sticker() : bool
    {
        $getMessageStickerId = $this->bot->getMessageStickerId();
        $random = mt_rand(0, 5);
        $isGivenResponseSticker = $random > 3 ? true : false;
        if ($isGivenResponseSticker)
        {
            $blueprintLinkLineStickerById = Genchan::blueprintLinkLineStickerById($getMessageStickerId);
            $this->bot->replyImage($blueprintLinkLineStickerById);
            return true;
        }
        return false;
    }
}
?>