BtnNodeBundle
==============

sample cms structure tree for menus

=============

### Step 1: Add NodeBundle in your composer.json (private repo)

```js
{
    "require": {
        "bitnoise/node-bundle": "dev-master",
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@github.com:Bitnoise/BtnNodeBundle.git"
        }
    ],
}
```

### Step 2: Enable the bundles

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Symfony\Cmf\Bundle\RoutingBundle\CmfRoutingBundle(),
        new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        new Btn\NodeBundle\BtnNodeBundle(),
    );
}
```

### Step 3: Import NodeBundle routing

``` yaml
# app/config/routing.yml
btn_node:
    resource: "@BtnNodeBundle/Controller/"
    type:     annotation
    prefix:   /
```

### Step 4: Update your database schema

``` bash
$ php app/console doctrine:schema:update --force
```

### Step 5: Set up avalible routes

``` yml
# app/conig/parameters.yml
parameters:
    # ...
    btn_node.availableRoutes:
        homepage: Homepage
        # ...
```

### Step 6: Add BtnNodeBundle to the assetic.bundle config

``` yml
# app/config/config.yml
assetic:
    #...
    bundles:
        - BtnNodeBundle
```

### Add gedmo orm mappings

[docs](https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/symfony2.md#mapping)

### Setup BtnMediaBundle

[docs](https://github.com/Bitnoise/BtnMediaBundle/blob/master/README.md)
