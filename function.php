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

// channel snippet aka getting channel's avatar, about description and username.
function channelSnippet($channelId, $apikey)
{
    $url = "https://www.googleapis.com/youtube/v3/channels?part=snippet&id=$channelId&key=$apikey";
    $json = getJSONContent($url);

    $channelUsername = $json['items'][0]['snippet']['title'];
    $channelDescription = $json['items'][0]['snippet']['description'] ?? null;
    $channelCreationDate = formatDate($json['items'][0]['snippet']['publishedAt']);
    $channelAvatarUrl = $json['items'][0]['snippet']['thumbnails']['medium']['url'] ?? "./img/noavatar.png";
    $channelAvatar = $channelAvatarUrl ? "<img src='$channelAvatarUrl' alt='Channel Avatar'>" : "<img src='./img/noavatar.png' alt='Channel Avatar' title='Unable to get the avatar'>";

    return [
        'username' => $channelUsername,
        'description' => $channelDescription,
        'avatar' => $channelAvatar,
        'avatarUrl' => $channelAvatarUrl,
        'creationDate' => $channelCreationDate
    ];
}

// channel statistics aka getting channel's total number views, subscribers and videos.
function channelStatistics($channelId, $apikey)
{
    $url = "https://www.googleapis.com/youtube/v3/channels?part=statistics&id=$channelId&key=$apikey";
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
function channelbrandingSettings($channelId, $apikey)
{
    $url = "https://www.googleapis.com/youtube/v3/channels?part=brandingSettings&id=$channelId&key=$apikey";
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

// check if channel id exists when is changed in url
function checkId($channelId, $apikey){
    $url = "https://www.googleapis.com/youtube/v3/channels?id=$channelId&key=$apikey";
    $json = getJSONContent($url);

    $channelId = $json['items'][0]['id'] ?? null;

    if ($channelId == null){
        header("Location: index.php");
        exit();
    }

    return $channelId;
}
?>
