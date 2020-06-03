<?php

require_once __DIR__ . '/../vendor/autoload.php';

use JustSteveKing\SMS\Sender;

$key = ""; // your api key here

$sender = new Sender($key, true);

$groups = $sender->groups();

print_r($groups->groups);