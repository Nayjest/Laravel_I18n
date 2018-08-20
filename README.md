Laravel I18n
=====

Multilanguage support for Laravel framework

[![Codacy Badge](https://www.codacy.com/project/badge/3deb0ba075e846889a22af675c9fea61)](https://www.codacy.com/public/mail_2/Laravel_I18n)
[![Code Climate](https://codeclimate.com/github/Nayjest/Laravel_I18n/badges/gpa.svg)](https://codeclimate.com/github/Nayjest/Laravel_I18n)
[![Circle CI](https://circleci.com/gh/Nayjest/Laravel_I18n.svg?style=svg)](https://circleci.com/gh/Nayjest/Laravel_I18n)
![Release](https://img.shields.io/packagist/v/nayjest/i18n.svg)

## Features
* Traits for multilanguage models
* Language switcher
## Requirements

* Laravel 4.X
* php 5.3+

## Installation

#### Installation using [Composer](https://getcomposer.org)

##### Step 1: Declare dependency
Add nayjest/grids to "require" section of your composer.json
```javascript
"require": {
    "nayjest/i18n": "~1"
},
```

##### Step 2: Update dependencies
Run following command:
```bash    
php composer.phar update
```

##### Step 3: Register service provider in Laravel application
Add following line:
```php
'Nayjest\I18n\ServiceProvider'
```
to 'providers' section of app/config/app.php file.


## Usage

## License


© 2014&mdash;2018 Vitalii Stepanenko

Licensed under the MIT License.
