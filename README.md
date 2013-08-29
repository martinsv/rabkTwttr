rabkTwttr: a Twitter API library in PHP
=========

### Setup

To get started, you'll need to register your application with Twitter:

1. Login to Twitter's [Create a New Application](https://dev.twitter.com/apps/new) page.
2. Enter a name, description and website.
3. Once done, you'll be given a `Consumer key` and `Consumer secret`.

### Search Twitter by hashtag
```php
<?php

include "rabkTwttr.php";

define('CONSUMER_KEY', '[CONSUMER KEY]');
define('CONSUMER_SECRET', '[CONSUMER SECRET]');

// Create Twitter object; no need for user authentication through Twitter
$twitter = new rabkTwttr(CONSUMER_KEY, CONSUMER_SECRET);

// Setup a query: get 10 latest tweets with hashtag #bigdata
$query = array('count' => 10, 'q' => "#bigdata");

// Do the query and output results
$tweets = $twitter->query('search/tweets.json', 'GET', $query);
foreach($tweets->statuses as $tweet)
  echo '<li><b>@' . $tweet->user->screen_name . '</b>: ' . $tweet->text . '</li>';

?>
```

### List a user's latest tweets
```php
<?php

include "rabkTwttr.php";

define('CONSUMER_KEY', '[CONSUMER KEY]');
define('CONSUMER_SECRET', '[CONSUMER SECRET]');

// Create Twitter object; need user authentication through Twitter
$twitter = new rabkTwttr(CONSUMER_KEY, CONSUMER_SECRET, $auth = true);

// Setup a query: get 3 latest post from user RobAboukhalil
$query = array('count' => 3, 'screen_name' => 'RobAboukhalil');

// Do the query and output results
$tweets = $twitter->query('statuses/user_timeline.json', 'GET', $query);
foreach($tweets as $tweet)
  echo '<li><b>@' . $tweet->user->screen_name . '</b>: ' . $tweet->text . '</li>';

?>
```

