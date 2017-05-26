<?php
require_once __DIR__ . '/../vendor/autoload.php';

use jens1o\smashcast\SmashcastApi;
use jens1o\smashcast\user\SmashcastUser;

SmashcastApi::setAppName('OAuthTestJens');
$user = SmashcastUser::getUserByLogin('jens1o', PASSWORD);
SmashcastApi::setUserAuthToken($user->getAuthToken());

$user->update([
    'user_display_name' => 'JENs1o' // updates the spelling of the username ;)
]);

var_dump($user->user_name); // => JENs1o