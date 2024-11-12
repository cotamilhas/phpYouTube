<?php
require_once("conn.php");
require_once("function.php");

// get channel id
$channelId = $_GET['id'];

$channelId = checkId($channelId, $apikey); // check channel id
$channelSnippet = channelSnippet($channelId, $apikey); // get channel snippet aka getting channel's avatar, about description and username
$channelStatistics = channelStatistics($channelId, $apikey); // channel statistics aka getting channel's total number views, subscribers and videos
$channelbrandingSettings = channelbrandingSettings($channelId, $apikey); // channel branding settings aka getting channel's trailer for people who haven't subscribed yet, country and banner
$recentVideos = getRecentVideos($channelId, $apikey); // get recent videos

createDB($config); // create database
createTables($config); // create tables
addChannelContent($config, $channelId, $channelSnippet, $channelStatistics, $channelbrandingSettings); // adds content to the database

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="<?php echo $channelSnippet['username']; ?>" />
    <meta property="og:description" content="<?php echo "See more about {$channelSnippet['username']} YouTube channel!"; ?>">
    <meta name="author" content="cotamilhas">
    <meta property="og:image" content="<?php echo $channelSnippet['avatarUrl']; ?>" />
    <link rel="icon" type="image/png" href="./channel/<?php echo $channelId; ?>/avatar.png" />
    <link rel="stylesheet" href="./css/channelstyle.css">
    <title>phpYouTube</title>
</head>
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $handle = $_POST['id'] ?? '';

    $channelId = getChannelId($handle, $apikey);

    if ($channelId) {
        header("Location: channel.php?id=" . urlencode($channelId));
        exit();
    } else {
        echo "<h2 id='notfound'>CHANNEL NOT FOUND</h2>";
    }
}
?>

<body>
    <div class="container">
        <!-- search bar -->
        <h1 class="logo"><a href="index.php">phpYouTube</a></h1>
        <form method="POST">
            <div id="search-box">
                <div id="handlebox">@</div>
                <input id="ch" type="text" autocomplete="off" spellcheck="false" name="id" placeholder="Search" required>
                <button type="submit">Search</button>
            </div>
        </form>
        <!-- banner -->
        <img class="banner" src="./channel/<?php echo $channelId ?>/banner.png" alt="Channel Banner">
        <!-- profile header which contains channal avatar, username and description -->
        <div class="profile-header">
            <img src="./channel/<?php echo $channelId ?>/avatar.png" alt="Channel Avatar">
            <h1><?php echo $channelSnippet['username']; ?></h1>
            <p><?php echo $channelSnippet['description']; ?></p>
        </div>
        <!-- profile info which contains creation date and channel country -->
        <div class="profile-info">
            <p><strong>ID:</strong> <?php echo $channelId; ?></p>
            <p><strong>Created in:</strong> <u><?php echo $channelSnippet['creationDate']; ?></u></p>
            <p><strong>Country:</strong> <?php echo $channelbrandingSettings['channelCountry']; ?></p>
        </div>
        <!-- channel stats which contains total views, subscribers and total videos -->
        <div class="stats">
            <p><strong>Total Views:</strong> <?php echo $channelStatistics['totalViews']; ?></p>
            <p><strong>Subscribers:</strong> <?php echo $channelStatistics['subscribers']; ?></p>
            <p><strong>Total Videos:</strong> <?php echo $channelStatistics['totalVideos']; ?></p>
        </div>
        <!-- video section which contains recent videos -->
        <?php if ($channelStatistics['totalVideos'] > 0): ?>
            <div class="video-section">
                <h3>Recent Videos</h3>
                <?php foreach ($recentVideos as $video): ?>
                    <div class="video-card">
                        <h3><?php echo $video['title']; ?></h3>
                        <p>Posted on: <?php echo $video['publishDate']; ?></p>
                        <a href="https://www.youtube.com/watch?v=<?php echo $video['videoId']; ?>">
                            <img src="<?php echo $video['thumbnail']; ?>" alt="Thumbnail">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <!-- non subscriber trailer -->
        <?php if ($channelbrandingSettings['nonSubscriberTrailer']): ?>
            <iframe src="<?php echo $channelbrandingSettings['nonSubscriberTrailer']; ?>" allowfullscreen></iframe>
        <?php endif; ?>
    </div>
</body>

</html>