<?php
require_once __DIR__ . '/../vendor/autoload.php';

use jens1o\smashcast\user\SmashcastUser;

$username = 'jens1o';
$user = new SmashcastUser($username);

var_dump($user);