<?php
$channelhandle = $_GET['id'];
$apikey = 'YOUR_API_KEY';
$channelId = getChannelId($channelhandle, $apikey);
$channelSnippet = channelSnippet($channelhandle, $apikey);

// getting json
function getJSONContent($url) {
    $data = file_get_contents($url);
    $json = json_decode($data, true);

    return $json;
}

// channel id
function getChannelId($channelhandle, $apikey){
    $url = 'https://www.googleapis.com/youtube/v3/channels?&forHandle=' . $channelhandle . '&key=' . $apikey;
    $json = getJSONContent($url);

    return $json['items'][0]['id'] ?? "Channel not found.";
}
// channel snippet aka getting channel's avatar, about description and username
function channelSnippet($channelhandle, $apikey){
    $url = 'https://www.googleapis.com/youtube/v3/channels?part=snippet&forHandle=' . $channelhandle . '&key=' . $apikey;
    $json = getJSONContent($url);

    $channelUsername = $json['items'][0]['snippet']['title'];
    $channelDescription = $json['items'][0]['snippet']['description'];
    $channelAvatar = '<img src="' . $json['items'][0]['snippet']['thumbnails']['medium']['url'] . '">';

    return [
        'username' => $channelUsername,
        'description' => $channelDescription,
        'avatar' => $channelAvatar
    ];
}

echo "ID: " . $channelId;
echo "<h1>" . $channelSnippet['username'] . "</h1>";
echo "<p>" . $channelSnippet['description'] . "</p>";
echo $channelSnippet['avatar'];

?>