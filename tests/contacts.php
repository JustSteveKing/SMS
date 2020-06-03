<?php

require_once __DIR__ . '/../vendor/autoload.php';

use JustSteveKing\SMS\Sender;

$key = ""; // your api key here

$sender = new Sender($key, true);

$groups = $sender->groups();

$group = $groups->groups[0];

$contacts = $sender->contacts($group->id, 5);

print_r($contacts->contacts);