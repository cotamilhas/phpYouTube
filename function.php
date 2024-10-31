<?php
$apikey = "YOUR_API_KEY"; 

// getting json content
function getJSONContent($url)
{
    $data = file_get_contents($url);
    return json_decode($data, true);
}

// getting channel id
function getChannelId($channelHandle, $apikey)
{
    $channelHandle = str_replace(" ","", $channelHandle);

    $url = "https://www.googleapis.com/youtube/v3/channels?forHandle=$channelHandle&key=$apikey";
    $json = getJSONContent($url);

    $channelId = $json['items'][0]['id'] ?? null;
    return $channelId;
}

// I'm probably adding more functions here, maybe moving channel.php functions over here...
?>
