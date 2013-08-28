rabkTwttr
=========

A Twitter API library in PHP


Setup
=========

To get started, you'll need to register your application with Twitter. As of __, this is required ____.


Sample code - Search for tweets by hashtag
=========

```php
<?php

include "rabkTwttr.php";

rabkTwttr::$consumer_key    = ;
rabkTwttr::$consumer_secret = '[CONSUMER SECRET HERE]';

// -- Application-only Authentication -- search for tweets
$api = new rabkTwttr("app", $consumer_key = '[CONSUMER KEY HERE]', $consumer_secret = '[CONSUMER_SECRET HERE]');
$tweets = $api->query('search/tweets.json?q=#bigdata&count=10', 'GET');
foreach($tweets->statuses as $tweet)
{
  echo 'Tweet by ' . $tweet->user->screen_name . ': <br/>';
  echo ' > ' . $tweet->text . '<br/>';
  echo '<hr>';
}

?>
```






// -- User Authentication -- list my tweets
#$r = new rabkTwttr(rabkTwttr::MODE_USER, 'http://127.0.0.1/Temp/Article-Twitter-github/');
#$args = array();
#$args['count'] = '3';
#$args['screen_name']='RobAboukhalil';
#$tweets = $r->query('statuses/user_timeline.json', 'GET', $args);
#foreach($tweets as $tweet)
#{
#  echo 'Tweet by ' . $tweet->user->screen_name . ': <br/>';
#  echo ' > ' . $tweet->text . '<br/>';
#  echo '<hr>';
#}


// -- User Authentication -- list my tweets
#$s = new rabkTwttr(rabkTwttr::MODE_USER, 'http://127.0.0.1/Temp/Article-Twitter-github/');
#$args=array();
#$args['status'] = 'Test #3';
#$s->query('statuses/update.json', 'POST', $args);


