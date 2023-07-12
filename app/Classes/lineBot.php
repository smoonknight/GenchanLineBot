<?php
    namespace App\Classes;

    use App\Services\LineService;

    class LineBot extends LineService
    {
        public function Reply($text, $token)
        {
            $url = $this->apiReply;

            $body["replyToken"] = $token;
            $body["messages"][0] = array(
                "type" => "text",
                "text" => $text
            );
            $result = $this->HttpPost($url, $body);
            return $result;
        }
    }
?>
