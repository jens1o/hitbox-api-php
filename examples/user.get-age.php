<?php
require_once __DIR__ . '/../vendor/autoload.php';

use jens1o\smashcast\media\live\SmashcastLiveMedia;

$liveMedia = new SmashcastLiveMedia('jens1o');

// `getTimeCreated()` returns an \DateTime, as long as the api is nice c:
$date = $liveMedia->getTimeCreated();

// it MAY return null.
if($date === null) {
    exit('I don\'t know how old the channel is. :/');
}

// basic php things, not api related.
$date->diff(new DateTime/* diff to now */);
$age = $diff->format('%y years, %m months, %d days');

echo 'The channel is ' . $age . ' old';