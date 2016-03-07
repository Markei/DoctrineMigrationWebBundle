README
======

About this bundle
-----------------

This bundle provides the Doctrine Migrations migrate command in a controller/web setting.

Installation
------------

**1** Add to composer.json to the `require` key

``` shell
    $composer require markei/doctrinemigrationwebbundle
``` 

**2** Register the bundle in ``app/AppKernel.php``

``` php
    $bundles = array(
        // ...
        new Markei\DoctrineMigrationWebBundle\MarkeiDoctrineMigrationWebBundle(),
    );
```

**3** Configure your routing ``app/config/routing.php``, change the prefix in something you like

``` yaml
    markei_doctrine_migration_web:
        resource: "@MarkeiDoctrineMigrationWebBundle/Controller/"
        type:     annotation
        prefix:   /migration
```

**4** Configure security for the prefix you have defined http://symfony.com/doc/current/book/security.html

**5** Configure Doctrine Migrations http://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html

**6** Visit the page http://my-site/migration and follow the instructions

Configuration
-------------

Configure the prefix via ``app/config/routing.yml``

Configure the common Doctrine Migrations settings via ``app/config/config.yml``

Configure database connections via the doctrine section in ``app/config/config.yml``

Configure security via ``app/config/security.yml``
