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

// formatting dates, for example: 2016-05-14T15:43:27Z to 2016/05/14 15:43:27
function formatDate($date){
    $date = new DateTime($date);
    $date = $date->format('Y/m/d H:i:s'); // change it according to your region

    return $date;
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
    $channelCreationDate = $json['items'][0]['snippet']['publishedAt'];
    $channelCreationDate = formatDate($channelCreationDate);
    // medium because I think it is enough, change to high or default if you want...
    $channelAvatar = '<img src="' . $json['items'][0]['snippet']['thumbnails']['medium']['url'] . '" alt="Channel Icon">'; 

    return [
        'username' => $channelUsername,
        'description' => $channelDescription,
        'avatar' => $channelAvatar,
        'creationDate' => $channelCreationDate
    ];
}

// channel statistics aka getting channel's total number views, subscribers and videos.
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
echo "<p>Created in: <u>" . $channelSnippet['creationDate'] . "</u></p>";

// statistics
echo "<p>Total View: <u>" . $channelStatistics['totalViews'] . "</u></p>";
echo "<p>Subscribers: <u>" . $channelStatistics['subscribers'] . "</u></p>";
echo "<p> Total Videos: <u>" . $channelStatistics['totalVideos']. "</u></p>";

?>