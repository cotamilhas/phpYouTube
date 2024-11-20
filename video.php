<?php
require_once("conn.php");
require_once("function.php");

$videoId = $_GET['id'];
$videoId = checkVideoId($videoId, $apikey);

$videoSnippet = videoSnippet($videoId, $apikey);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="<?php echo $videoSnippet['title']; ?>" />
    <meta property="og:description" content="See more about <?php echo $videoSnippet['title']; ?> video...">
    <meta name="author" content="cotamilhas">
    <meta property="og:image" content="<?php echo $videoSnippet['thumbnail']; ?>" />
    <link rel="icon" type="image/png" href="./channel/<?php echo $videoSnippet['channelId']; ?>/avatar.png" />
    <link rel="stylesheet" href="./css/videostyle.css">
    <title>phpYouTube</title>
</head>

<body>
    <p><?php echo $videoId; ?></p>
    <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $videoId; ?>" allowfullscreen></iframe>

    <p>Posted on: <?php echo $videoSnippet['creationDate']; ?></p>
    <p>Title: <?php echo $videoSnippet['title']; ?></p>
    <p>Description: <?php echo $videoSnippet['description']; ?></p>
    <p>Tags: <?php echo implode(", ", $videoSnippet['tags']);?></p>
</body>