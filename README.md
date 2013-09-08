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

// Setup a query: get 10 latest tweets with hashtag #bigdata
$twitter = new rabkTwttr(CONSUMER_KEY, CONSUMER_SECRET);
$query = array('count' => 10, 'q' => "#bigdata");

// Perform query and output results
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

// Setup a query: get 3 latest post from user RobAboukhalil
// Note: you need to authenticate ($auth=true) if you're not doing searches
$twitter = new rabkTwttr(CONSUMER_KEY, CONSUMER_SECRET, $auth = true);
$query = array('count' => 3, 'screen_name' => 'RobAboukhalil');

// Perform query and output results
$tweets = $twitter->query('statuses/user_timeline.json', 'GET', $query);
foreach($tweets as $tweet)
  echo '<li><b>@' . $tweet->user->screen_name . '</b>: ' . $tweet->text . '</li>';

?>
```

### Post on current user's timeline
```php
<?php

include "rabkTwttr.php";

define('CONSUMER_KEY', '[CONSUMER KEY]');
define('CONSUMER_SECRET', '[CONSUMER SECRET]');

// Setup the query
$twitter = new rabkTwttr(CONSUMER_KEY, CONSUMER_SECRET, $auth = true);
$query = array('status' => "My first tweet using the rabkTwttr Twitter library");

// Post on the user's timeline
$twitter->query('statuses/update.json', 'POST', $query);

?>
```


