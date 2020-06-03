# TextLocal SMS Sender
An SMS Sender for the TextLocal version 2 API

**This package is currently being updated to a new version, please check issues for progress**

A simple example:

```php
// Accepts API KEY and API MODE as arguements. 
// API MODE set as true means that we are only testing against the API, not making full requests
$key = "1234567890-YOUR-API-KEY-1234567890";
$sender = new Sender($key, true);

// Our instance has been created, time to use it
// Get the balance on your Text Local Account
$balance = $sender->balance();
```
