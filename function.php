<?php
// get your API key on Google Cloud Console - YouTube Data API v3
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
    $channelHandle = str_replace(" ", "%20", $channelHandle);

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
function textToFlagEmoji($countryCode): string
{
    $codePoints = array_map(function ($char) {
        return 127397 + ord($char);
    }, str_split(strtoupper($countryCode)));

    return mb_convert_encoding('&#' . implode(';&#', $codePoints) . ';', 'UTF-8', 'HTML-ENTITIES');
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
    $channelBannerUrl = $json['items'][0]['brandingSettings']['image']['bannerExternalUrl'] . "=w2120-fcrop64=1,00000000ffffffff-k-c0xffffffff-no-nd-rj" ?? null;

    $nonSubscriberTrailer = $nonSubscriberTrailerID ? "<iframe width='420' title='this is a video featured for non-subscribed viewers' height='315' src='https://www.youtube.com/embed/$nonSubscriberTrailerID'></iframe>" : null;
    $channelCountry = $countryCode ? textToFlagEmoji($countryCode) : null;
    $channelBanner = $channelBannerUrl ? "<img width='30%' src='$channelBannerUrl' alt='Channel Banner'>" : "<img src='./img/nobanner.png' alt='Channel Banner' title='Unable to get the banner'>";

    return [
        'nonSubscriberTrailer' => $nonSubscriberTrailer,
        'channelCountry' => $channelCountry,
        'channelBanner' => $channelBanner,
        'channelBannerUrl' => $channelBannerUrl
    ];
}

// get recent videos, more specifically the last 10 videos, it can be changed up to 50 which I think is the maximum
function getRecentVideos($channelId, $apikey){
    $maxResult = "10";

    $url = "https://www.googleapis.com/youtube/v3/search?&channelId=$channelId&order=date&part=snippet&type=video&maxResults=$maxResult&key=$apikey";
    $json = getJSONContent($url);

    $videos = $json['items'] ?? [];

    $videoList = [];
    foreach ($videos as $video) {
        $videoId = $video['id']['videoId'] ?? null;
        $title = $video['snippet']['title'] ?? 'Untitled';
        $thumbnail = $video['snippet']['thumbnails']['high']['url'] ?? null;

        $videoList[] = [
            'title' => $title,
            'videoId' => $videoId,
            'thumbnail' => $thumbnail,
            'embedUrl' => $videoId ? "https://www.youtube.com/embed/$videoId" : null
        ];
    }

    return $videoList;
    
}

// check if channel id exists when is changed in url
function checkId($channelId, $apikey)
{
    $url = "https://www.googleapis.com/youtube/v3/channels?id=$channelId&key=$apikey";
    $json = getJSONContent($url);

    $channelId = $json['items'][0]['id'] ?? null;

    if ($channelId == null) {
        header("Location: index.php");
        exit();
    }

    return $channelId;
}

