# hitbox-api-php
Handles api requests to hitbox nicely!


## Documentation

0. [Preample](#preample)
1. [Hitbox User](#hitbox-user)

### Preample
**tl;dr**: PHP library, not available via composer, clone it and execute `composer install`

This is a php library making api requests to hitbox quite easy. It's designed to use models(like `HitboxUser`). It's using the composer autoloader, so if you want to init the library, here's how you go:

0. Make sure you meet the requirements of this lib:
    - PHP 7.1+
    - Composer
    - `allow_url_fopen` must be enabled
    - You comply with the [MIT License](LICENSE)
1. Download/Clone this repository
2. Execute `composer install` in your clone
3. In your script, add:
```php
<?php
require_once 'vendor/autoload.php';

// other code...
```
4. Use the lib.

The following chapter will teach you about the differents models this library offers at the moment. If you feel something is missing, feel free to create a pr. Also, if you want to improve the English(fix grammar and spelling mistakes) in this documentation, it would be quite nice!
### Hitbox User
> Members of the hitbox community, owns streams and can broadcast

#### Get information about a user
```php
<?php
use jens1o\hitbox\user\HitboxUser;

$user = new HitboxUser('jens1o');
```
> ⚠️ Instantiating a user immediately executes a http request to hitbox asking for the account data! You should cache users and avoid instantiating them twice in one period of time(e.g. per request)! If you don't want that a http request is being executed, [you need to pass the account data as a row yourself as second parameter and set the first parameter to `null`](#about-row-parameter).

#### getUserId()
Returns the id of this user in the database of hitbox.
```php
<?php
use jens1o\hitbox\user\HitboxUser;

$user = new HitboxUser('jens1o');

$user->getUserId(); // => 1472612
```
> ℹ️ Tip: This can also return `null` when the user does not exist; it is used internally for checking users, see [HitboxUser#exists()](#exists).

#### exists()
Returns wether this user exists. It should be called before requiring any data!
```php
<?php
use jens1o\hitbox\user\HitboxUser;

$user = new HitboxUser('jens1o');

$user->exists(); // => true

$notExistingUser = new HitboxUser('jsopadjasdjofajfp');

$notExistingUser->exists(); // => false
```

#### getLogos()
Todo.

#### isLive()
Todo.

#### hasVerifiedEmail()
Todo.

### Build users from other parameters
Todo.

#### getUserByLogin()
Todo.

#### getUserByToken()
Todo.

#### getUserNameByToken()
Todo.

## About row parameter
> todo...

## Todo
- [ ] Finish with every model
- [ ] Write documentation
- [ ] Create tests