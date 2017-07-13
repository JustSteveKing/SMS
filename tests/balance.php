<?php

require_once __DIR__ . '/../vendor/autoload.php';

use JustSteveKing\SMS\Sender;

$key = ""; // your api key here

$sender = new Sender($key, true);

$balance = $sender->balance();

print_r($balance);
