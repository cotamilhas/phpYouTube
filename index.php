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
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="phpYouTube" />
    <meta property="og:description" content="See YouTube channels info">
    <meta name="author" content="cotamilhas">
    <meta property="og:image" content="./img/logo.png" />
    <link rel="stylesheet" href="./css/indexstyle.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <title>phpYouTube</title>
</head>
<body>
    <form method="POST">
        <div id="logo"><h2>phpYouTube</h2></div>
        <div class="search-container">
            <div class="search-bar">
                <span class="search-icon">@</span>
                <input type="text" name="id" placeholder="Ex: yomilhas" class="search-input" autocomplete="off" spellcheck="false" required>
                <button type="submit" class="search-button">Search</button>
            </div>
        </div>
        <?php if (!empty($notFound)): ?>
            <h2 id="notfound">CHANNEL NOT FOUND</h2>
        <?php endif; ?>
    </form>
</body>
</html>
