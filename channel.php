<?php
$channelhandle = $_GET['id'];
$apikey = 'YOUR_API_KEY';

function getJSONContent($url) {
    $curl = curl_init($url);
    
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    
    $content = curl_exec($curl);
    
    if ($content === false) {
        echo 'Error loading the channel: ' . curl_error($curl);
    }
    
    curl_close($curl);
    
    return $content;
}

function getChannelId($channelhandle, $apikey){
    $channelJsonUrl = 'https://www.googleapis.com/youtube/v3/channels?part=snippet,statistics,brandingSettings,contentDetails,status&forHandle=' . $channelhandle . '&key=' . $apikey;
    $json = getJSONContent($channelJsonUrl);

    echo $json;
}

getChannelId($channelhandle, $apikey);


?>