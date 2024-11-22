<?php
require_once("conn.php");
require_once("function.php");

$domainUrl = "localhost/"; // change this to your domain, so it can show the meta image, localhost do not work.

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
    <meta property="og:image" content="<?php echo $domainUrl . $channelSnippet['avatarUrl']; ?>" />
    <link rel="icon" type="image/png" href="./channel/<?php echo $channelId; ?>/avatar.png" />
    <link rel="stylesheet" href="./css/channelstyle.css">
    <title>phpYouTube</title>
</head>
<?php
require_once 'function.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $handle = $_POST['id'] ?? null;

    $channelId = getChannelId($handle, $apikey);

    if ($channelId) {
        header("Location: channel.php?id=" . urlencode($channelId));
        exit();
    } else
        $notFound = true;
}
?>

<body>
    <div class="container">
        <!-- search bar -->
        <h1 class="logo"><a href="index.php">phpYouTube</a></h1>
        <form method="POST">
            <div class="search-container">
                <div class="search-bar">
                    <span class="search-icon">@</span>
                    <input type="text" name="id" placeholder="Ex: yomilhas" class="search-input" autocomplete="off" spellcheck="false" required>
                    <button class="search-button">Search</button>
                </div>
            </div>
        </form>
        <?php if (!empty($notFound)): ?>
            <h2 id="notfound">CHANNEL NOT FOUND</h2>
        <?php endif; ?>
        <!-- banner -->
        <img class="banner" src=".<?php echo $channelbrandingSettings['bannerUrl'] ?>" alt="Channel Banner">
        <!-- profile header which contains channal avatar, username and description -->
        <div class="profile-header">
            <img src=".<?php echo $channelSnippet['avatarUrl'] ?>" alt="Channel Avatar">
            <h1><?php echo $channelSnippet['username']; ?></h1>
            <p><?php echo $channelSnippet['description']; ?></p>
        </div>
        <!-- profile info which contains creation date and channel country -->
        <div class="profile-info">
            <p><strong>ID:</strong> <?php echo $channelId; ?></p>
            <p><strong>Created in:</strong> <u><?php echo $channelSnippet['creationDate']; ?></u></p>
            <?php if ($channelbrandingSettings['countryFlag']): ?>
                <p><strong>Country:</strong> <?php echo $channelbrandingSettings['countryFlag']; ?></p>
            <?php endif; ?>
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
                        <a href="video.php?id=<?php echo $video['videoId']; ?>">
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
<?php echo $domainUrl . $channelSnippet['avatarUrl']; ?>
</html>
