<?php

include "server.php";
$db = new DbConnect();
$conn = $db->connect();

$message = '';

// Add channel
if (isset($_POST['save_name'])) {
    $c_title = $_POST['y_cname'];

    //For youtube user image
    $channel_name = $c_title; // YouTube channel name/title to fetch profile picture
    $channel_id = ""; // initialize an empty variable to store the channel ID
    $profile_picture_url = ""; // initialize an empty variable to store the profile picture URL

    // Step 1: Get the channel ID by channel name
    $api_key = "AIzaSyCPAsXLjf6fTl8QAmCNZaxE6pNdsALXpJM"; //YouTube Data API v3 API key
    $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&type=channel&q=" . urlencode($channel_name) . "&key=" . $api_key;
    $response = file_get_contents($url);

    if ($response) {
        $data = json_decode($response, true);
        $channel_id = $data['items'][0]['snippet']['channelId'];

        $query = $conn->prepare("SELECT * FROM youtube_channels WHERE yc_url = ?");
        $query->execute([$channel_id]);
        $result = $query->rowCount();
        if ($result > 0) {
            $message = '<div class="text-danger"> *Channel already save. Please type other youtube channel. </div>';
        } else {

            // Step 2: Get the profile picture URL by channel ID
            if ($channel_id) {
                $url = "https://www.googleapis.com/youtube/v3/channels?part=snippet&id=" . $channel_id . "&key=" . $api_key;
                $response = file_get_contents($url);

                if ($response) {
                    $data = json_decode($response, true);
                    $tit = $data['items'][0]['snippet']['title'];
                    $profile_picture_url = $data['items'][0]['snippet']['thumbnails']['high']['url'];
                    $description = $data['items'][0]['snippet']['description'];
                    $publish = $data['items'][0]['snippet']['publishedAt'];
                } else {
                    $message = '<div class="text-danger"> Error fetching data url</div>';
                }
            }

            $y_url = $channel_id;
            $profile_url = $profile_picture_url;
            $descript = $description;
            $publish_at = $publish;

            $sql = "INSERT INTO `youtube_channels` (`y_id`, `yc_url`, `profile_pic`, `name`, `description`,  `created_at`) 
            VALUES (NULL, :you_url, :profilepic, :yname, :descr,  :creaat)";

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":you_url", $y_url);
            $stmt->bindParam(":profilepic", $profile_url);
            $stmt->bindParam(":yname", $tit);
            $stmt->bindParam(":descr", $descript);
            $stmt->bindParam(":creaat", $publish_at);


            if ($stmt->execute()) {

                //Inserting 100 latest video to youtube database
                $key = $api_key;
                $channelId = $y_url;

                // Initialize variables for pagination
                $total_videos = 0;
                $page_token = null;

                while ($total_videos < 100) {
                    //YouTube Data API request URL
                    $url = "https://www.googleapis.com/youtube/v3/search?key=$key&channelId=$channelId&maxResults=50&order=date&type=video&part=snippet";

                    if ($page_token) {
                        $url .= "&pageToken=$page_token";
                    }

                    // Make an HTTP request to the YouTube API
                    $responses = file_get_contents($url);

                    // Parse JSON response
                    $datas = json_decode($responses);

                    // Process and save the results to the database
                    foreach ($datas->items as $video) {
                        $chan_id = $video->snippet->channelId;
                        $video_id = $video->id->videoId;
                        $title_desc = $video->snippet->title;
                        $video_desc = $video->snippet->description;
                        $pub_at = $video->snippet->publishedAt;
                        $thumb = $video->snippet->thumbnails->high->url;

                        $link = "https://www.youtube.com/embed/" . $video_id;


                        $sql2 = "INSERT INTO `youtube_channel_videos` (`yc_id`, `video_link`, `title`, `description`, `thumbnail`) 
            VALUES (:ytcid, :vidlink, :ytitle, :descrip, :turl)";

                        $chstmt = $conn->prepare($sql2);
                        $chstmt->bindParam(":ytcid", $chan_id);
                        $chstmt->bindParam(":vidlink", $link);
                        $chstmt->bindParam(":ytitle", $title_desc);
                        $chstmt->bindParam(":descrip", $video_desc);
                        $chstmt->bindParam(":turl", $thumb);

                        $chstmt->execute();
                    }

                    // Update pagination variables
                    $page_token = isset($datas->nextPageToken) ? $datas->nextPageToken : null;
                    $total_videos += count($datas->items);

                    // Sleep for a short duration (e.g., 1-2 seconds) to avoid rate limiting
                    sleep(2);

                    // Putting files to json format
                    try {

                        $results = array();

                        $query = "SELECT * FROM youtube_channels";
                        $values = array();

                        $stmt = $conn->prepare($query);
                        $stmt->execute($values);

                        while ($yc = $stmt->fetch(PDO::FETCH_ASSOC)) {

                            $ycv = null;

                            $query2 = "SELECT * FROM youtube_channel_videos WHERE yc_id = ?";
                            $values2 = array($yc['yc_url']);

                            $stmt2 = $conn->prepare($query2);
                            $stmt2->execute($values2);
                            $ycv = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                            if ($ycv) {
                                $yc['youtube_channel_videos'] = $ycv;
                            } else {
                                $yc['youtube_channel_videos'] = '';
                            }

                            array_push($results, $yc);
                        }

                        $encoded_data = json_encode(array("youtube_channels" => $results), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        file_put_contents('youtube_channel_json.php', $encoded_data);
                    } catch (PDOException $e) {
                        throw new Exception($e->getMessage());
                    }

                    $message = '<div class="text-success"> Channel save successfully and save 100 latest videos</div>';
                }
            } else {

                $message = '<div class="text-danger"> *Data not inserted! </div>';
            }
        }
    } else {
        $message = '<div class="text-danger"> Error fetching data </div>';
    }
}
