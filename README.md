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
Todo... Soon...

## Todo
- [ ] Finish with every model
- [ ] Write documentation
- [ ] Create tests