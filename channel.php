<?php
$channelhandle = $_GET['id'];
$apikey = 'YOUR_API_KEY';
$channelId = getChannelId($channelhandle, $apikey);

if ($channelId === "Channel not found.") {
    echo $channelId;
    exit;
}

$channelSnippet = channelSnippet($channelhandle, $apikey);
$channelStatistics = channelStatistics($channelhandle, $apikey);
$channelbrandingSettings = channelbrandingSettings($channelhandle, $apikey);

// getting json
function getJSONContent($url)
{
    $data = file_get_contents($url);
    $json = json_decode($data, true);

    return $json;
}

// formatting dates, for example: 2016-05-14T15:43:27Z to 2016/05/14 15:43:27.
function formatDate($date)
{
    $date = new DateTime($date);
    $date = $date->format('Y/m/d H:i:s'); // change it according to your region

    return $date;
}

// receives text, the channel country, and turns it into emoji, maybe not the best idea...
function textToFlag($countryCode): string
{
    $codePoints = array_map(function ($char) {
        return 127397 + ord($char);
    }, str_split(strtoupper($countryCode)));

    return mb_convert_encoding('<p>Country: ' . '&#' . implode(';&#', $codePoints) . ';' . '</p>', 'UTF-8', 'HTML-ENTITIES');
}


// channel id
function getChannelId($channelhandle, $apikey)
{
    $url = "https://www.googleapis.com/youtube/v3/channels?&forHandle=$channelhandle&key=$apikey";
    $json = getJSONContent($url);

    return $json['items'][0]['id'] ?? "Channel not found.";
}

// channel snippet aka getting channel's avatar, about description and username.
function channelSnippet($channelhandle, $apikey)
{
    $url = "https://www.googleapis.com/youtube/v3/channels?part=snippet&forHandle=$channelhandle&key=$apikey";
    $json = getJSONContent($url);

    $channelUsername = $json['items'][0]['snippet']['title'];
    $channelDescription = $json['items'][0]['snippet']['description'] ?? null;
    $channelCreationDate = formatDate($json['items'][0]['snippet']['publishedAt']);
    $channelAvatarURL = $json['items'][0]['snippet']['thumbnails']['medium']['url'] ?? null;
    $channelAvatar = $channelAvatarURL ? "<img src='$channelAvatarURL' alt='Channel Avatar'>" : "<img src='./img/noavatar.png' alt='Channel Avatar' title='Unable to get the avatar'>";

    return [
        'username' => $channelUsername,
        'description' => $channelDescription,
        'avatar' => $channelAvatar,
        'creationDate' => $channelCreationDate
    ];
}

// channel statistics aka getting channel's total number views, subscribers and videos.
function channelStatistics($channelhandle, $apikey)
{
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

// channel branding settings aka getting channel's trailer for people who haven't subscribed yet, country and banner.
function channelbrandingSettings($channelhandle, $apikey)
{
    $url = "https://www.googleapis.com/youtube/v3/channels?part=brandingSettings&forHandle=$channelhandle&key=$apikey";
    $json = getJSONContent($url);
    $nonSubscriberTrailerID = $json['items'][0]['brandingSettings']['channel']['unsubscribedTrailer'] ?? null;
    $countryCode = $json['items'][0]['brandingSettings']['channel']['country'] ?? null;
    $bannerUrl = $json['items'][0]['brandingSettings']['image']['bannerExternalUrl'] ?? null;

    $nonSubscriberTrailer = $nonSubscriberTrailerID ? "<iframe width='420' title='this is a video featured for non-subscribed viewers' height='315' src='https://www.youtube.com/embed/$nonSubscriberTrailerID'></iframe>" : null;
    $channelCountry = $countryCode ? textToFlag($countryCode) : null;
    $channelBanner = $bannerUrl ? "<img src='$bannerUrl' alt='Channel Banner'>" : "<img src='./img/nobanner.png' alt='Channel Banner' title='Unable to get the banner'>";

    return [
        'nonSubscriberTrailer' => $nonSubscriberTrailer,
        'channelCountry' => $channelCountry,
        'channelBanner' => $channelBanner
    ];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link rel="stylesheet" href="./css/channelstyle.css">
    <title>phpYouTube</title>
</head>

<body>
    <?
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
    echo "<p>Total Videos: <u>" . $channelStatistics['totalVideos'] . "</u></p>";

    // brandingSettings
    echo $channelbrandingSettings['channelCountry'];
    echo $channelbrandingSettings['nonSubscriberTrailer'];
    echo $channelbrandingSettings['channelBanner'];

    echo '<hr>';

    ?>

</body>

</html>