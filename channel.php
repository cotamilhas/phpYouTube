<?php
$channelhandle = $_GET['id'];
$apikey = 'YOUR_API_KEY';
$channelId = getChannelId($channelhandle, $apikey);

if($channelId === "Channel not found."){
    echo $channelId;
    exit;
}

$channelSnippet = channelSnippet($channelhandle, $apikey);
$channelStatistics = channelStatistics($channelhandle, $apikey);

// getting json
function getJSONContent($url) {
    $data = file_get_contents($url);
    $json = json_decode($data, true);

    return $json;
}

// channel id
function getChannelId($channelhandle, $apikey){
    $url = "https://www.googleapis.com/youtube/v3/channels?&forHandle=$channelhandle&key=$apikey";
    $json = getJSONContent($url);

    return $json['items'][0]['id'] ?? "Channel not found.";
}

// channel snippet aka getting channel's avatar, about description and username
function channelSnippet($channelhandle, $apikey){
    $url = "https://www.googleapis.com/youtube/v3/channels?part=snippet&forHandle=$channelhandle&key=$apikey";
    $json = getJSONContent($url);

    $channelUsername = $json['items'][0]['snippet']['title'];
    $channelDescription = $json['items'][0]['snippet']['description'];
    // medium because I think it is enough, change to high or default if you want...
    $channelAvatar = '<img src="' . $json['items'][0]['snippet']['thumbnails']['medium']['url'] . '" alt="Channel Icon">'; 

    return [
        'username' => $channelUsername,
        'description' => $channelDescription,
        'avatar' => $channelAvatar
    ];
}

// channel statistics aka getting channel's total views, number of subscriber and number of total videos.
function channelStatistics($channelhandle, $apikey){
    $url = "https://www.googleapis.com/youtube/v3/channels?part=statistics&forHandle=$channelhandle&key=$apikey";
    $json = getJSONContent($url);

    $totalViews = $json['items'][0]['statistics']['viewCount'];
    $hiddenSubCount = $json['items'][0]['statistics']['hiddenSubscriberCount'];
    // this is already impossible to do, hiding youtube subscribers, but if one day youtube come back with this idea...
    if ($hiddenSubCount == false)
        $totalSubscribers = $json['items'][0]['statistics']['subscriberCount'];
    $totalVideos = $json['items'][0]['statistics']['videoCount'];

    return [
        'totalViews' => $totalViews,
        'subscribers' => $totalSubscribers,
        'totalVideos' => $totalVideos
    ];
}

// channel id
echo "ID: " . $channelId;

// snippet
echo "<h1>" . $channelSnippet['username'] . "</h1>";
echo "<p>" . $channelSnippet['description'] . "</p>";
echo $channelSnippet['avatar'];

// statistics
echo "<p>Total View: " . $channelStatistics['totalViews'] . "</p>";
echo "<p>Subscribers: " . $channelStatistics['subscribers'] . "</p>";
echo "<p> Total Videos: " . $channelStatistics['totalVideos']. "</p>";

?>