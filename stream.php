<?php

include 'src/VideoStreamExample.php';

$video = $_GET['path'] ?? 'video.mp4';
$streamer = new \premiumwebtechnologies\streamingvideo\VideoStreamExample($video);
$streamer->open();
$streamer->streamVideo();
