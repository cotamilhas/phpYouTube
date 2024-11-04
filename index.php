<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="phpYouTube" />
    <meta property="og:description" content="See YouTube channels info">
    <meta name="author" content="cotamilhas">
    <meta property="og:image" content="./img/logo.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/indexstyle.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <title>phpYoutube</title>
</head>
<body>
    <form method="POST">
        <div id="logo">phpYouTube</div>
        <div id="search-box">
            <div id="handlebox">@</div>
            <input id="ch" type="text" autocomplete="off" spellcheck="false" name="id" placeholder="Ex: yomilhas" required>
            <button type="submit">Search</button>
        </div>
    </form>
</body>
</html>
<!-- PHP Code here because 'notfound' element do not want turn red when the php is on top, makes sense why -->
<?php
require_once 'function.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $handle = $_POST['id'] ?? ''; 

    $channelId = getChannelId($handle, $apikey);

    if ($channelId) {
        header("Location: channel.php?id=" . urlencode($channelId));
        exit();
    } else {
        echo "<p id='notfound'>CHANNEL NOT FOUND</p>";
    }
}
?>
