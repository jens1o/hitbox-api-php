<?php
require_once './../vendor/autoload.php';

use jens1o\smashcast\channel\SmashcastChannel;

$channelName = 'jens1o';

$channel = new SmashcastChannel($channelName);

printf('The channel %s got %d views', $channelName, $channel->getTotalViews());