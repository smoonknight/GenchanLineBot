<?php
class Setting
{
    public static function getChannelAccessToken()
    {
        $channelAccessToken = "3+S2JTELfTRe6B90tk+h9GNM7WKLy8GV2JSlBTqcIVpGMqsA3+9az8V9/MhGQK8/mvGKWTcVGY+dNYw2ZqqR4zQD2W+gTkiQov5WJRhMNCKlDxd8JH5QmBHGvADZCRxMWcp4TTMS5l7J+8Xwm76/XQdB04t89/1O/w1cDnyilFU=";
        return $channelAccessToken;
    }
    public static function getChannelSecret()
    {
        $channelSecret = "8948ebc8edb77835b86ae0e88b892a75";
        return $channelSecret;
    }
    public static function baseUrl()
    {
        $settings = json_decode(file_get_contents('extension/settings.json'));
        $base = $settings->{"base"};
        return $base;
    }
    public static function getApiReply()
    {
        $api = "https://api.line.me/v2/bot/message/reply";
        return $api;
    }
    public static function getApiPush()
    {
        $api = "https://api.line.me/v2/bot/message/push";
        return $api;
    }
    public static function getBotInfo()
    {
        $api = "https://api.line.me/v2/bot/info";
        return $api;
    }
    public static function getWebhookEndpoint()
    {
        $api = "https://api.line.me/v2/bot/channel/webhook/endpoint";
        return $api;
    }
    public static function testWebhookEndpoint()
    {
        $api = "https://api.line.me/v2/bot/channel/webhook/test";
        return $api;
    }
    public static function getUserProfile($userId)
    {
        $api = "https://api.line.me/v2/bot/profile/{userId}";
        $api = str_replace("{userId}", $userId, $api);
        return $api;
    }
    public static function getApiGroupChatSummary($groupId)
    {
        $api = "https://api.line.me/v2/bot/group/{groupId}/summary";
        $api = str_replace("{groupId}", $groupId, $api);
        return $api;
    }
    public static function getGroupProfileUser($groupId, $userId)
    {
        $api = "https://api.line.me/v2/bot/group/{groupId}/member/{userId}";
        $api = str_replace(["{groupId}","{userId}"], [$groupId,$userId], $api);
        return $api;
    }
    public static function getContent($messageId)
    {
        $api = "https://api-data.line.me/v2/bot/message/{messageId}/content";
        $api = str_replace("{messageId}", $messageId, $api);
    }

    public static function getApiSafebooru($req)
    {
        $api = "https://SMoonKnight:n17scrUGqLC7RLoW6DdsC1cq@safebooru.donmai.us/posts.json?tags=" . $req . "&limit=50";
        return $api;
    }

    public static function getApiWikipedia($req)
    {
        $api = "https://id.wikipedia.org/w/api.php?action=query&generator=search&gsrlimit=4&prop=extracts&exintro&explaintext&exlimit=max&format=json&gsrsearch=" . $req;
        return $api;
    }

    public static function getApiGenshinDevSelect($type, $select)
    {
        $api = "https://api.genshin.dev/{type}/{select}";
        $api = str_replace(["{type}", "{select}"], [$type, $select], $api);
        return $api;
    }

    public static function getApiGenshinDevType($type)
    {
        $api = "https://api.genshin.dev/" . $type;
        return $api;
    }
}
