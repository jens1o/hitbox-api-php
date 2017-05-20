# smashcast-api-php [![Build Status](https://travis-ci.org/jens1o/smashcast-api-php.svg?branch=master)](https://travis-ci.org/jens1o/smashcast-api-php)
Handles api requests to smashcast nicely!

### This library is not finished nor complete!
I'll work on it, but it may take some time. At the moment, I'm completing the user object and will go on with implementing the Channel/Media object.

If something is missing, or something is not well explained or when you have a question, feel free to [open a issue](https://github.com/jens1o/smashcast-api-php/issues/new).

0. Make sure you meet the requirements of this lib:
    - PHP 7.1+
    - Composer
    - `allow_url_fopen` must be enabled
    - You comply with the [MIT License](LICENSE)
    - You installed some root certificates, since all connections this library creates are secured via ssl.
1. Download/Clone this repository
2. Execute `composer install` in your clone
3. In your script, add:
```php
<?php
require_once 'vendor/autoload.php';

// other code...
```
4. Use the lib.

### I recommend head over to the non-existing examples folder, or just dig through the code.