<?php
require_once __DIR__ . '/../vendor/autoload.php';

use jens1o\smashcast\media\live\SmashcastLiveMedia;

$liveMedia = new SmashcastLiveMedia('jens1o');

$diff = $liveMedia->getTimeCreated()->diff(new DateTime/* diff to now */);
$age = $diff->format('%y years, %m months, %d days');

echo 'The channel is ' . $age . ' old';