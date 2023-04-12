Requirements
============

1. [symfony-cli](https://symfony.com/download)
2. pdo_sqlite
3. composer
4. yarn

Install
=======

1. clone the repository
2. Run the following commands :

```bash
$ symfony composer install
$ yarn install
$ yarn dev
$ symfony console doctrine:migration:migrate -n
$ symfony console doctrine:fixture:load -n
$ symfony serve -d 
```
