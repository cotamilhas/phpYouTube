<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="phpYouTube" />
    <meta property="og:description" content="See YouTube channels info">
    <meta name="author" content="cotamilhas">
    <meta property="og:image" content="./img/logo.png" />
    <meta http-equiv="refresh" content="300">
    <link rel="stylesheet" href="./css/indexstyle.css">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <title>phpYoutube</title>
</head>
<body>
    <div id="logo">phpYouTube</div>
    <div id="search-box">
        <div id="handlebox">@</div>
        <input id="ch" type="text" autocomplete="off" spellcheck="false" placeholder="Ex: yomilhas">
        <button onclick="gotoSearch()">Search</button>
    </div>
    <!-- NOT THE BEST SEARCH METHOD! but it does the same, I will change this in the future using PHP -->
    <script>
        document.getElementById('ch').addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                gotoSearch();
            }
        });

        function gotoSearch(){
            channelhandle = document.getElementById('ch').value;

            window.open("channel.php?id=@" + channelhandle, "_parent");
        }
    </script>
</body>
</html>
