<?php
// get your API key on Google Cloud Console - YouTube Data API v3
$apikey = "YOUR_API_KEY";

// getting json content
function getJSONContent($url)
{
    $data = @file_get_contents($url, false);
    if ($data === false) {
        return null;
    }
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

    $username = $json['items'][0]['snippet']['title'];
    $description = isset($json['items'][0]['snippet']['description'])
        ? nl2br($json['items'][0]['snippet']['description'])
        : null;
    $creationDate = formatDate($json['items'][0]['snippet']['publishedAt']);
    $avatarUrl = $json['items'][0]['snippet']['thumbnails']['high']['url'] ?? "./img/noavatar.png";

    $avatarUrl = getChannelPictures($channelId, $avatarUrl, $pictureName = "avatar.png"); // Avatar Download

    return [
        'username' => $username,
        'description' => $description,
        'avatarUrl' => $avatarUrl,
        'creationDate' => $creationDate
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

    $brandingSettings = $json['items'][0]['brandingSettings']['channel'] ?? null;
    $nonSubscriberTrailerID = $brandingSettings['unsubscribedTrailer'] ?? null;
    $countryCode = $brandingSettings['country'] ?? null;
    $bannerUrl = isset($json['items'][0]['brandingSettings']['image']['bannerExternalUrl'])
        ? $json['items'][0]['brandingSettings']['image']['bannerExternalUrl'] . "=w2120-fcrop64=1,00000000ffffffff-k-c0xffffffff-no-nd-rj"
        : "./img/nobanner.png";
    $nonSubscriberTrailer = $nonSubscriberTrailerID ? "https://www.youtube.com/embed/$nonSubscriberTrailerID" : null;
    $countryFlag = $countryCode ? textToFlagEmoji($countryCode) : null;

    $bannerUrl = getChannelPictures($channelId, $bannerUrl, $pictureName = "banner.png"); // Banner Download

    return [
        'nonSubscriberTrailer' => $nonSubscriberTrailer,
        'countryFlag' => $countryFlag,
        'bannerUrl' => $bannerUrl
    ];
}

// get recent videos, more specifically the last 10 videos, it can be changed up to 50 which I think is the maximum
function getRecentVideos($channelId, $apikey)
{
    $url = "https://www.googleapis.com/youtube/v3/search?channelId=$channelId&order=date&part=snippet&type=video&maxResults=10&key=$apikey";
    $json = getJSONContent($url);

    $videoList = [];
    foreach ($json['items'] ?? [] as $item) {
        $videoId = $item['id']['videoId'] ?? null;
        $title = $item['snippet']['title'] ?? 'Untitled';
        $publishDate = $item['snippet']['publishedAt'] ?? 'Unknown';
        $publishDate = formatDate($publishDate);
        $thumbnail = $item['snippet']['thumbnails']['high']['url'] ?? null;

        $videoList[] = [
            'title' => $title,
            'videoId' => $videoId,
            'publishDate' => $publishDate,
            'thumbnail' => $thumbnail
        ];
    }

    return $videoList;
}


// get video snippet
function videoSnippet($videoId, $apikey)
{
    $url = "https://www.googleapis.com/youtube/v3/videos?part=snippet&id=$videoId&key=$apikey";
    $json = getJSONContent($url);

    $creationDate = formatDate($json['items'][0]['snippet']['publishedAt']);
    $channelId = $json['items'][0]['snippet']['channelId'];
    $title = $json['items'][0]['snippet']['title'];
    $description = $json['items'][0]['snippet']['description'];
    $tags = isset($json['items'][0]['snippet']['tags']) ? $json['items'][0]['snippet']['tags'] : [];
    $thumbnail = $item['snippet']['thumbnails']['high']['url'] ?? './img/nothumbnail.png';

    return [
        'creationDate' => $creationDate,
        'channelId' => $channelId,
        'title' => $title,
        'description' => $description,
        'tags' => $tags,
        'thumbnail' => $thumbnail
    ];
}

// tries to download the channel avatar and banner to avoid error 403
function getChannelPictures($channelId, $url, $pictureName)
{
    $defaultPictures = [
        "banner.png" => "./img/nobanner.png",
        "avatar.png" => "./img/noavatar.png"
    ];

    if (isset($defaultPictures[$pictureName]) && $url === $defaultPictures[$pictureName])
        $url = null;

    $channelPath = "channel/$channelId";
    if (!is_dir($channelPath))
        mkdir($channelPath, 0777, true);

    $fullPath = "$channelPath/$pictureName";

    if ($url) {
        $image = file_get_contents($url);
        file_put_contents($fullPath, $image);
        return "./channel/$channelId/$pictureName";
    }

    return $defaultPictures[$pictureName];
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

// check if video id exists when is changed in url
function checkVideoId($videoId, $apikey)
{
    $url = "https://www.googleapis.com/youtube/v3/videos?id=$videoId&key=$apikey";
    $json = getJSONContent($url);

    $videoId = $json['items'][0]['id'] ?? null;

    if ($videoId == null) {
        header("Location: index.php");
        exit();
    }

    return $videoId;
}

