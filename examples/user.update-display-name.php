<?php

SmashcastApi::setAppName('OAuthTestJens');
$user = SmashcastUser::getUserByLogin('jens1o', PASSWORD);
SmashcastApi::setUserAuthToken($user->getAuthToken());

$user->update([
    'user_display_name' => 'JENs1o' // updates the spelling of the username ;)
]);

$user->user_name; // => JENs1o