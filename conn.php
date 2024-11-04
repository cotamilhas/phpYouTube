<?php
// change according by your database, since I'm using AMPPS to test this app I use localhost and default username and password.
// also change $dbname if you want.

$config = [
    'servername' => 'localhost',
    'username' => 'root',
    'password' => 'mysql',
    'dbname' => 'phpyoutube'
];

addChannelContent($config, $channelId, $channelSnippet, $channelStatistics, $channelbrandingSettings);


require_once 'function.php';

function connectDB($config)
{
    return new PDO("mysql:host={$config['servername']};dbname={$config['dbname']}", $config['username'], $config['password']);
}

// ----- database creation -----
// have to admit that I don't understand what's here, I'm learning how to use PDO with very carefully...
// no idea how to create tables, maybe changing sql query? HELP :/
function createDB($config)
{
    try {
        $conn = connectDB($config);
    } catch (PDOException $e) {
        if ($e->getCode() === 1049) { // error 1049 is unknown database also 42000 but no used by PDO, I think...
            try {
                $conn = connectDB($config);
                // configures the PDO to throw exceptions in case of an error
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $query = "CREATE DATABASE IF NOT EXISTS {$config['dbname']}";
                $conn->exec($query);
            } catch (PDOException $e) {
                echo "Error envolving database: " . $e->getMessage();
            }
        } else {
            echo "Connection error: " . $e->getMessage();
        }
    } finally {
        $conn = null; // can I close the connection like this?
    }
}

// here we go again ;-;
function createTables($config)
{
	
    // table channel
    try {
        $conn = connectDB($config);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "CREATE TABLE IF NOT EXISTS channels (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    channel_id VARCHAR(50) NOT NULL UNIQUE,
                    name VARCHAR(100) NOT NULL,
                    description TEXT,
                    created_at DATE,
                    country VARCHAR(50),
                    subscriber_count INT,
                    total_views INT,
                    video_count INT,
                    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );";
        $conn->exec($query);
    } catch (PDOException $e) {
        echo "Error creating channels table: " . $e->getMessage();
    } finally {
        $conn = null;
    }

    // table videos
    // useless, at the moment... both tables are useless, like this project :D
    try {
        $conn = connectDB($config);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "CREATE TABLE IF NOT EXISTS videos (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    video_id VARCHAR(50) NOT NULL UNIQUE,
                    channel_id INT NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    published_at DATETIME,
                    view_count INT,
                    like_count INT,
                    comment_count INT,
                    FOREIGN KEY (channel_id) REFERENCES channels(id) ON DELETE CASCADE
        );";
        $conn->exec($query);
    } catch (PDOException $e) {
        echo "Error creating videos table: " . $e->getMessage();
    } finally {
        $conn = null;
    }
}

// adding channel content like subscribers, views, etc...
// there's A LOT to change here since I asked ChatGPT how I can add stuff, I understand what's happening here but I feel like it's wrong
// I'm going to leave this aside for a while and code the video part
function addChannelContent($config, $channelId, $channelSnippet, $channelStatistics, $channelbrandingSettings){
	try {
        $conn = connectDB($config);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "INSERT INTO channels (channel_id, name, description, created_at, country, subscriber_count, total_views, video_count) 
                        VALUES (:channel_id, :name, :description, :created_at, :country, :subscriber_count, :total_views, :video_count)
                        ON DUPLICATE KEY UPDATE 
                            name = :name, 
                            description = :description, 
                            created_at = :created_at, 
                            country = :country, 
                            subscriber_count = :subscriber_count, 
                            total_views = :total_views, 
                            video_count = :video_count,
                            last_updated = CURRENT_TIMESTAMP";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':channel_id', $channelId);
        $stmt->bindParam(':name', $channelSnippet['username']);
        $stmt->bindParam(':description', $channelSnippet['description']);
        $stmt->bindParam(':created_at', $channelSnippet['creationDate']);
        $stmt->bindParam(':country', $channelbrandingSettings['channelCountry']);
        $stmt->bindParam(':subscriber_count', $channelStatistics['subscribers']);
        $stmt->bindParam(':total_views', $channelStatistics['totalViews']);
        $stmt->bindParam(':video_count', $channelStatistics['totalVideos']);

        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error inserting data into channels table: " . $e->getMessage();
    } finally {
        $conn = null;
    }
}


