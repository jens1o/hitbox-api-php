# smashcast-api-php [![Travis](https://img.shields.io/travis/jens1o/smashcast-api-php-unofficial.svg?style=flat-square)](https://travis-ci.org/jens1o/smashcast-api-php-unofficial) [![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat-square)](http://makeapullrequest.com) <img src="https://img.shields.io/badge/composer-jens1o%2Fsmashcast--api--php-brightgreen.svg?style=flat-square"> [![Packagist](https://img.shields.io/packagist/v/jens1o/smashcast-api-php-unofficial.svg?style=flat-square)](https://packagist.org/packages/jens1o/smashcast-api-php-unofficial) [![license](https://img.shields.io/github/license/jens1o/smashcast-api-php-unofficial.svg?style=flat-square)]()
Handles api requests to smashcast nicely!

This library is **actively supported** and I'll update it constantly. If you want to contribute, don't hesitate to do anything. If you have any problems, just ask me.
I'll work on it, but it may take some time. At the moment, I'm facing to creating tests.

#### Implementations may change due to restructure and refactor... Verify that updating the api client may result in a breaking change!


## Installation
If something is missing, or something is not well explained or when you have a question, feel free to [open a issue](https://github.com/jens1o/smashcast-api-php-unofficial/issues/new).

0. Make sure you meet the requirements of this lib:
    - PHP 7.1+
    - Composer
    - `allow_url_fopen` must be enabled
    - You comply with the [MIT License](LICENSE)
    - You installed some root certificates, since all connections this library creates are secured via ssl.

#### Install using composer:
1. Execute `composer require jens1o/smashcast-api-php-unofficial`
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

### Roadmap
- Write some templates making contributions easier
- Explore in chat possibilities => search for a good lib to accomplish this
- Finalize structure
- Make a documentation that everybody understands(I think I need some help when it comes to grammar...) :)

**Working on your first Pull Request?** You can learn how from this *free* series [How to Contribute to an Open Source Project on GitHub](https://egghead.io/series/how-to-contribute-to-an-open-source-project-on-github)
