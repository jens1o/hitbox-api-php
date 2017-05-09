# smashcast-api-php
Handles api requests to smashcast nicely!

### This library is not finished nor complete!
I'll work on it, but it may take some time. At the moment, I'm completing the user object and will go on with implementing the Channel/Media object.

If something is missing, or something is not well explained or when you have a question, feel free to [open a issue](https://github.com/jens1o/smashcast-api-php/issues/new).

## Documentation

0. [Preample](#preample)
1. [smashcast User](#smashcast-user)
    1. [Get information about a user](#get-information-about-a-user)
    2. [Build users from other parameters](#build-users-from-other-parameters)
    3. [SmashcastAuthToken](#SmashcastAuthToken)

### Preamble
**tl;dr**: PHP library, not available via composer yet, clone it and execute `composer install`

This is a php library making api requests to smashcast quite easy. It's designed to use models(like `SmashcastUser`). It's using the composer autoloader, so if you want to init the library, here's how you go:

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

The exception thrown when something went wrong is `jens1o\smashcast\exception\SmashcastApiException`. When authentication goes wrong it is `jens1o\smashcast\exception\SmashcastAuthException`(extends `SmashcastApiException`). You should catch them in any case and handle failures!

The following chapter will teach you about the different models this library offers at the moment. If you feel something is missing, feel free to create a pr. Also, if you want to improve the English(fix grammar and spelling mistakes) in this documentation, it would be quite nice!
### Smashcast User
> Members of the Smashcast community, owns streams and can broadcast

#### Get information about a user
Instantiate a new class with the first parameter being the username as a string.

```php
<?php
use jens1o\smashcast\user\SmashcastUser;

$user = new SmashcastUser('jens1o');
```
> ⚠️ Instantiating a user immediately executes a http request to smashcast asking for the account data! You should cache users and avoid instantiating them twice in one period of time(e.g. per request)! If you don't want that a http request is being executed, [you need to pass the account data as a row yourself as second parameter and set the first parameter to `null`](#about-row-parameter).

##### Get data
There is a `__get()` method implemented, with this you can fetch data. See [the documentation](http://developers.smashcast.tv/#get-user-object) for the exact field names. When you have an auth token set(or login in with private information) `user_email`, `recordings`, `videos`, `teams`, `livestream_count` and `partner_type` will be sent, too.

```php
<?php
use jens1o\smashcast\SmashcastApi;
use jens1o\smashcast\user\smashcastUser;

$user = new SmashcastUser('jens1o');

$user->user_name; // => jens1o

$user->user_email; // => null

// set user auth token
SmashcastApi::setUserAuthToken('HeyItsMyToken');

$newUser = new SmashcastUser('jens1o');

$newUser->user_email; // => someEmail@someHost.tld
```

> ⚠️ Warning: Check first if there is a matching method. Prefer that! Field names may vary on how you created the user. The methods will return consistent values!

> ℹ️ Tip: You get the auth token by [logging in with user credentials](#build-users-from-other-parameters) or [use the OAuth flow](#oauth-flow)[not implemented yet].

#### getUserId()
Returns the id of this user in the database of smashcast.
```php
<?php
use jens1o\smashcast\user\SmashcastUser;

$user = new SmashcastUser('jens1o');

$user->getUserId(); // => 1472612
```
> ℹ️ Tip: This can also return `null` when the user does not exist; it is used internally for checking users, see [SmashcastUser#exists()](#exists).

#### exists()
Returns wether this user exists. It should be called before requiring any data!
```php
<?php
use jens1o\smashcast\user\SmashcastUser;

$user = new SmashcastUser('jens1o');

$user->exists(); // => true

$notExistingUser = new SmashcastUser('jsopadjasdjofajfp');

$notExistingUser->exists(); // => false
```
> ️ℹ️ Note: This uses internally [SmashcastUser#getUserId()](#getUserId) and checks wether it is null or not.

#### getLogos()
Returns an instance of [LogoHandler](#logohandler) when the user exists.

Throws the exception `\BadMethodCallException` when the user does not exist.

```php
<?php
use jens1o\smashcast\user\SmashcastUser;

$user = new SmashcastUser('jens1o');

$user->getLogos(); // => instance of jens1o\smashcast\user\logos\LogoHandler
```

#### isLive()
Returns a bool wether the user is live at the moment.

```php
<?php
use jens1o\smashcast\user\smashcastUser;

$user = new SmashcastUser('jens1o');

$user->isLive(); // => false, user is not streaming
```

#### hasVerifiedEmail()
Returns a bool wether this user had validated their email.

```php
<?php
use jens1o\smashcast\user\smashcastUser;

$user = new SmashcastUser('jens1o');

$user->hasVerifiedEmail(); // => true, user validated their email
```

#### getData()
Returns the raw data this handler fetched from the api, useful for caching purposes.
```php
<?php

use jens1o\smashcast\user\SmashcastUser;

$user = new SmashcastUser('jens1o');

$data = $user->getData(); // $data holds now the data of the user
```

> ️ℹ️ Note: This is useful for runtime caches.

### Build users from other parameters
It is also possible to build users from other parameters. This is supposed to be used as caching method. If you've saved the output you can restore the user from it like this:

```php
<?php
use jens1o\smashcast\user\SmashcastUser;

// first time initiate the user
$user = new SmashcastUser('jens1o');

// save data
$dump = $user->getData();

// ...

// need the user again later
$restoredUser = new SmashcastUser(null, $dump);

// you can work now like before
$user->user_name; // => jens1o

```

> ️ℹ️ Note: This way the handler won't ask the (slow) smashcast api but uses the data you provided. This is useful for runtime caches. Note you should update the cache at least each 10 minutes.

#### static getUserByLogin()
It's possible to get a user (with private information) by login with the user credentials.
```php
<?php
use jens1o\smashcast\exception\SmashcastAuthException;
use jens1o\smashcast\user\SmashcastUser;

$user = null;

try {
    $user = SmashcastUser::getUserByLogin('mycoolusername', 'mycoolpass');
} catch(SmashcastAuthException $e) {
    // something went wrong with logging in
}

// now it's possible to get the user email:
$user->user_email; // => someaddress@somehost.sometld
```
> ️ℹ️ Note: This api throws `SmashcastAuthException` instead of `SmashcastApiException`.

> ️ℹ️ Note: With the third parameter you are able to change the app. (Most likely you don't use that, it defaults to `desktop`).

> ️ℹ️ Tip: When you just want the user authtoken, use [SmashcastAuthToken::getTokenByLogin()](#static-gettokenbylogin) instead!

#### static getUserByToken()
Returns the user that is mapped to that auth token.
```php
<?php

use jens1o\smashcast\user\SmashcastUser;

$authToken = 'SuperSecretAuthToken';

$user = SmashcastUser::getUserByToken($authToken);

$user->user_name; // => jens1o
```
> ️ℹ️ Note: This api throws `SmashcastApiException` when no user is connected to the auth token.

> ️ℹ️ Tip: When you just want the user name, use [smashcastUser::getUserNameByToken($authToken)](#static-getusernamebytoken) instead!

#### static getUserNameByToken()
Returns the user name that is connected to the auth token.
```php
<?php
use jens1o\smashcast\user\SmashcastUser;

$authToken = 'SuperSecretAuthToken';

$user = SmashcastUser::getUserNameByToken($authToken);

// user exists:
echo $user; // => jens1o

// user doesn't exist:
$user; // => null
```

### SmashcastAuthToken
Holds auth token and you can create some.

#### static getTokenByLogin()
Returns an auth token by the user login and password

```php
<?php
use jens1o\smashcast\exception\SmashcastAuthException;
use jens1o\smashcast\token\SmashcastAuthToken;

$token = null;

try {
    $token = SmashcastAuthToken::getTokenByLogin('mycoolusername', 'mycoolpass');
} catch(SmashcastAuthException $e) {
    // something went wrong with logging in
}

echo 'Your auth token ' . $token; // $token is now a instance of SmashcastAuthToken, which provides a __toString() method.
```
> ️ℹ️ Note: This api throws `SmashcastAuthException` instead of `SmashcastApiException`.

> ️ℹ️ Note: With the third parameter you are able to change the app. (Most likely you don't use that, it defaults to `desktop`).

## About row parameter
> todo...

## OAuth flow
> todo... c:

## Todo
- [ ] Implement SmashcastChannel Class
- [ ] Implement OAuth Flow
- [ ] Finish with every model
- [ ] Write documentation
- [ ] Create tests