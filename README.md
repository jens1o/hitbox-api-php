# hitbox-api-php
Handles api requests to hitbox nicely!


## Documentation

0. [Preample](#preample)
1. [Hitbox User](#hitbox-user)
    1. [Get information about a user](#get-information-about-a-user)
    2. [Build users from other parameters](#build-users-from-other-parameters)

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

The exception thrown when something went wrong is `jens1o\hitbox\exception\HitboxApiException`. When authentication goes wrong it is `jens1o\hitbox\exception\HitboxAuthException`(extends `HitboxApiException`). You should catch them in any case and handle failures!

The following chapter will teach you about the different models this library offers at the moment. If you feel something is missing, feel free to create a pr. Also, if you want to improve the English(fix grammar and spelling mistakes) in this documentation, it would be quite nice!
### Hitbox User
> Members of the hitbox community, owns streams and can broadcast

#### Get information about a user
```php
<?php
use jens1o\hitbox\user\HitboxUser;

$user = new HitboxUser('jens1o');
```
> ⚠️ Instantiating a user immediately executes a http request to hitbox asking for the account data! You should cache users and avoid instantiating them twice in one period of time(e.g. per request)! If you don't want that a http request is being executed, [you need to pass the account data as a row yourself as second parameter and set the first parameter to `null`](#about-row-parameter).

##### Get data
There is a `__get()` method implemented, with this you can fetch data. See [the documentation](http://developers.hitbox.tv/#get-user-object) for the exact field names. When you have an auth token set(or login in with private informaation) `user_email`, `livestream_count` and `partner_type` will be sent, too.

```php
<?php
use jens1o\hitbox\HitboxApi;
use jens1o\hitbox\user\HitboxUser;

$user = new HitboxUser('jens1o');

$user->user_name; // => jens1o

$user->user_email; // => null

// set user auth token
HitboxApi::setUserAuthToken('HeyItsMyToken');

$newUser = new HitboxUser('jens1o');

$newUser->user_email; // => someEmail@someHost.tld
```

> ⚠️ Warning: Check first if there is a matching method. Prefer that! Field names may vary on how you created the user. The methods will return consistent values!

> ℹ️ Tip: You get the auth token by [loggin in with user credentials](#build-users-from-other-parameters)

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
> ️ℹ️ Note: This uses internally [HitboxUser#getUserId()](#getUserId) and checks wether it is null or not.

#### getLogos()
Returns an instance of [LogoHandler](#logohandler) when the user exists.

Throws the exception `\BadMethodCallException` when the user does not exist.

```php
<?php
use jens1o\hitbox\user\HitboxUser;

$user = new HitboxUser('jens1o');

$user->getLogos(); // => instance of jens1o\hitbox}user\logos\LogoHandler
```

#### isLive()
Returns a bool wether the user is live at the moment.

```php
<?php
use jens1o\hitbox\user\HitboxUser;

$user = new HitboxUser('jens1o');

$user->isLive(); // => false, user is not streaming
```

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