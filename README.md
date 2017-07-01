# smashcast-api-php [![Build Status](https://travis-ci.org/jens1o/smashcast-api-php.svg?branch=master)](https://travis-ci.org/jens1o/smashcast-api-php)
Handles api requests to smashcast nicely!

### This library is not finished nor complete!
I'll work on it, but it may take some time. At the moment, I'm facing to creating tests.

#### Implementations may change due to restructure and refactor... Verify that updating the api client may result in a breaking change!

If something is missing, or something is not well explained or when you have a question, feel free to [open a issue](https://github.com/jens1o/smashcast-api-php/issues/new).

0. Make sure you meet the requirements of this lib:
    - PHP 7.1+
    - Composer
    - `allow_url_fopen` must be enabled
    - You comply with the [MIT License](LICENSE)
    - You installed some root certificates, since all connections this library creates are secured via ssl.

#### Install using composer:
1. Execute `composer require jens1o/smashcast-api-php`
2. In your script, add:
```php
<?php
require_once './vendor/autoload.php';

// other code...
```
3. Use the lib.


##### Download without composer
1. Download/Clone this repository
2. Execute `composer install` in your clone
3. In your script, add:
```php
<?php
require_once './vendor/autoload.php';

// other code...
```
4. Use the lib.

### I recommend head over to the examples folder, or just dig through the code.
If you have any questions, do not hesitate to file an issue or ask me via twitter.
I plan to do a documentation, but first I want to make sure I don't have any breaking changes.