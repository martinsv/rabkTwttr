rabkTwttr: A Twitter API Library in PHP
=========

### How to get started

1. [Register your application](https://dev.twitter.com/apps/new) with Twitter 
2. Include the file `rabkTwttr.php` in your code.

### Sample Code

###### Search Twitter by hashtag
```php
<?php

// Initialize
include "rabkTwttr.php";
$twitter = new rabkTwttr('[CONSUMER KEY]', '[CONSUMER SECRET]');

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
// Note: need to do OAuth authentication ($auth=true) to list user's tweets
include "rabkTwttr.php";
$twitter = new rabkTwttr('[CONSUMER KEY]', '[CONSUMER SECRET]', $auth = true);

// Get 3 latest post from user RobAboukhalil
$tweets = $twitter->query('statuses/user_timeline.json', 'GET', array('count'=>3, 'screen_name'=>'RobAboukhalil'));

// Output results
foreach($tweets as $tweet)
  echo '<li><b>@' . $tweet->user->screen_name . '</b>: ' . $tweet->text . '</li>';

?>
```

###### Post on current user's timeline
```php
<?php

// Initialize
// Note: need to do OAuth authentication ($auth=true) to post on my timeline
include "rabkTwttr.php";
$twitter = new rabkTwttr('[CONSUMER KEY]', '[CONSUMER SECRET]', $auth = true);

// Post on user's timeline
$twitter->query('statuses/update.json', 'POST', array('status' => "My first tweet using the rabkTwttr Twitter library! http://github.com/robertaboukhalil/rabkTwttr"));

?>
```
