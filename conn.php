<?php
// change according by your database, since I'm using AMPPS to test this app I use localhost and default username and password.
// also change $dbname if you want.
require_once("function.php");

$config = [
    'servername' => 'localhost',
    'username' => 'root',
    'password' => 'mysql',
    'dbname' => 'phpyoutube'
];

// ----- database related -----
// database string connection
function connectDB($config)
{
    return new PDO("mysql:host={$config['servername']};dbname={$config['dbname']}", $config['username'], $config['password']);
}

// create database
function createDB($config)
{
    try {
        $conn = new PDO("mysql:host={$config['servername']}", $config['username'], $config['password']);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $conn->exec("CREATE DATABASE IF NOT EXISTS {$config['dbname']}");
    } catch (PDOException $e) {
        echo "Error creating database: " . $e->getMessage();
    } finally {
        $conn = null;
    }
}

// table creations
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

function addChannelContent($config, $channelId, $channelSnippet, $channelStatistics, $channelbrandingSettings)
{
    try {
        $conn = connectDB($config);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // check if channel id is already there maybe not the best idea 
        $checkQuery = "SELECT COUNT(*) FROM channels WHERE channel_id = :channel_id";
        $stmtCheck = $conn->prepare($checkQuery);
        $stmtCheck->bindParam(':channel_id', $channelId);
        $stmtCheck->execute();
        $updateId = $stmtCheck->fetchColumn() > 0;

        if ($updateId) {
            $query = "UPDATE channels 
                      SET name = :name, 
                          description = :description, 
                          created_at = :created_at, 
                          country = :country, 
                          subscriber_count = :subscriber_count, 
                          total_views = :total_views, 
                          video_count = :video_count,
                          last_updated = CURRENT_TIMESTAMP 
                      WHERE channel_id = :channel_id";
        } else {
            $query = "INSERT INTO channels (channel_id, name, description, created_at, country, subscriber_count, total_views, video_count) 
                      VALUES (:channel_id, :name, :description, :created_at, :country, :subscriber_count, :total_views, :video_count)";
        }

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
        echo "Error inserting/updating data into channels table: " . $e->getMessage();
    } finally {
        $conn = null;
    }
}
