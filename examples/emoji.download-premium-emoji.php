<?php
require_once './../vendor/autoload.php';

use jens1o\smashcast\user\SmashcastUser;

// we want the emojis of the channel hatzel666
$user = new SmashcastUser('hatzel666');

// get premium emojis and download each one
$emojis = $user->getChannel()->getChatEmojis()->getEmojis(true/* we want premium emojis*/);

// create dir when it does not exist, yet.
if(!file_exists('./downloads/')) {
    @mkdir('./downloads/');
}

for($i = 0, $count = count($emojis); $i < $count; $i++) {
    $emoji = $emojis[$i];

    echo "Download emoji {$emoji->short} ({$emoji->shortAlt})..." . PHP_EOL;
    $emoji->download("./downloads/emoji-{$i}.png");
}
