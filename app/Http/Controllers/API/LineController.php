<?php

namespace App\Http\Controllers\API;

use App\Classes\LineBot;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use Psr\Http\Message\ResponseInterface;

class LineController extends Controller
{
    private LineBot $lineBot;
    public function __construct()
    {
        $this->lineBot = new LineBot();
    }
    public function callback(Request $request)
    {
        $token = json_decode($request->getContent());
        $token = $token->{"events"}[0]->{"replyToken"};
        $bot = $this->lineBot->Reply("tes", $token);
        Storage::put("debug.txt", $token);
    }
}
