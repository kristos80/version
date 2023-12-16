# 🚀 Version

Version is a small utility that attempts to find the version of a web app by scanning its `composer.json` file.

It assumes that the `vendor` folder is located at the root of the app and that no other `vendor` folder exists within the entire app. It cannot guarantee that things will work if that's the case: That two or more `vendor` folders exist.

You can, however, alter its behavior by loading several variables in the environment:

- `VERSION_COMPOSER_FILE_PATH` accepts a relative or absolute path to the composer file of the app. Default is `NULL`.
- `VERSION_COMPOSER_FILE_PATH_RELATIVE` complements the `VERSION_COMPOSER_FILE_PATH` variable. Default is `FALSE`.

If the composer file doesn't contain the `version` property, a default value will be used: '1.0.0', which can be altered by passing the parameter `default` when calling the static method `get`.

```php
<?php
declare(strict_types=1);

use Kristos80\Version\Version;

require_once __DIR__ . "/vendor/autoload.php";

echo Version::get();

# Will return '2.0.5' as the default version if no version is found for any reason
echo Version::get("2.0.5");
```
