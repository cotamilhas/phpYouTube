<?php
require 'function.php';
$channelId = $_GET['id'];

$channelId = checkId($channelId, $apikey);
$channelSnippet = channelSnippet($channelId, $apikey);
$channelStatistics = channelStatistics($channelId, $apikey);
$channelbrandingSettings = channelbrandingSettings($channelId, $apikey);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="<?php echo $channelSnippet['username']; ?>" />
    <meta property="og:description" content="<?php echo "See more about " . $channelSnippet['username']  . " YouTube channel!"; ?>">
    <meta name="author" content="cotamilhas">
    <meta property="og:image" content="<?php echo $avatarUrl; ?>" />
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

    ?>

</body>

</html>