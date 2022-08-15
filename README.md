
# Symfony Demo App

Simple pplication build with Symfony 6 framework.

## Features:

- Registration, password reset and login features
- Simple blog section
- Fixture generation
- Pagination (KnpPager)
- Single/Multiple file uploads (VichUploader)
- EasyAdmin

## Requirements:

- Php 8.0+
- npm

## Installation and configuration:

```sh
git clone sirielua/symfony-demo-app
composer install
nmp install
npm run dev
php bin/console doctrine:migrations:migrate
php bin/console 
php bin/console doctrine:fixtures:load
```

## Further steps:

You can access admin backends (admin:admin):

- /admin for generic admin panel (architect-ui)
- /ea easy admin
