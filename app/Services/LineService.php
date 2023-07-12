<?php
    namespace App\Services;

    use GuzzleHttp\Client;

    class LineService
    {
    	public $channelAccessToken;
    	public $channelSecret;
        public $apiReply;
        public $apiPush;
        public $adminUserId;

        public $client;
        public $headerBearer;

        public function __construct()
        {
            $tokens = file_get_contents(base_path("/storage/app/token/line.token.json"));
            $data = json_decode($tokens, true);
            $this->channelAccessToken = $data["channelAccessToken"];
            $this->channelSecret = $data["channelSecret"];
            $this->apiReply = $data["apiReply"];
            $this->apiPush = $data["apiPush"];
            $this->adminUserId = $data["adminUserId"];

            $this->headerBearer = [
                'Authorization' => 'Bearer ' . $this->channelAccessToken
            ];

            $this->client = new Client();
        }

        public function HttpGet($url)
        {
            $response = $this->client->request('GET', $url, [
                'headers' => $this->headerBearer,
            ]);
            $status = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            return $body;
        }

        public function HttpPost($url, $body)
        {
            $response = $this->client->request('POST', $url, [
                'headers' => $this->headerBearer,
                'form_params' => $body
            ]);
            $status = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            return $body;
        }
    }
?>
