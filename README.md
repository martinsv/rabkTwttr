rabkTwttr
=========

A Twitter API 1.1 library in PHP.

### Setup

To get started, you'll need to register your application with Twitter:

1. Login to Twitter's [Create a New Application](https://dev.twitter.com/apps/new) page.
2. For the name, description and website, enter whatever comes to mind for now (the name must be unique across Twitter).
3. Once done, you'll see a table under **OAuth settings** with your `Consumer key` and `Consumer secret`.

The values should look like this (**keep your keys safe and do not share them**):

Key | Value
--- | ---
Consumer key | `G42UnplCbOF51eJqRRjQSQ`
Consumer secret | `tFbkOleLwMwH5hSFgEuQw9mqjgpHkSdxNi8a2OXXjY`

**Note:** As of June 11, 2013, Twitter requires authentication for all queries, even searching for tweets.

### Sample code - Search for tweets

```php
<?php

include "rabkTwttr.php";

?>
```

