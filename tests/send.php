<?php

require_once __DIR__ . '/../vendor/autoload.php';

use JustSteveKing\SMS\Sender;

$key = ""; // your api key here

$sender = new Sender($key, true);

$groups = $sender->groups();

$group = $groups->groups[0];

$contacts = $sender->contacts($group->id, 5);
$number = $contacts->contacts[0]->number;

$sent = $sender->send([$number], "Test Message");

print_r($sent);