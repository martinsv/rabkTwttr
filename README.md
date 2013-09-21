rabkTwttr: A Twitter API Library in PHP
=========

### Get started

1. [Register your application](https://dev.twitter.com/apps/new) with Twitter and obtain a `Consumer Key` and a `Consumer Secret`.
2. Include the file `rabkTwttr.php` in your code.

### Sample Code





###### Search Twitter by hashtag
```php
<?php

// Initialize
include "rabkTwttr.php";
$twitter = new rabkTwttr('[Consumer Key]', '[Consumer Secret]');

// Query Twitter for 10 latest tweets with hashtag #bigdata
$tweets = $twitter->query('search/tweets.json', 'GET', array('count'=>10, 'q'=>"#bigdata"));

// Output results
foreach($tweets->statuses as $tweet)
  echo '<li><b>@' . $tweet->user->screen_name . '</b>: ' . $tweet->text . '</li>';

?>
```

###### List a user's latest tweets
```php
<?php

// Initialize
include "rabkTwttr.php";
$twitter = new rabkTwttr('[Consumer Key]', '[Consumer Secret]');

// Get 3 latest post from user RobAboukhalil
$tweets = $twitter->query('statuses/user_timeline.json', 'GET', array('count'=>3, 'screen_name'=>'RobAboukhalil'));

// Output results
foreach($tweets as $tweet)
  echo '<li><b>@' . $tweet->user->screen_name . '</b>: ' . $tweet->text . '</li>';

?>
```

###### Write a tweet on user's timeline
```php
<?php

// Initialize
include "rabkTwttr.php";
$twitter = new rabkTwttr('[Consumer Key]', '[Consumer Secret]');

// Write a tweet
$twitter->query('statuses/update.json', 'POST', array('status' => "My first tweet using the rabkTwttr Twitter library! http://github.com/robertaboukhalil/rabkTwttr"));

?>
```
