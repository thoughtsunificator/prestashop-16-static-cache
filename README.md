# prestashop-1-6-static-cache

Static cache for Prestashop 1.6

## Getting started

## How does it work ?

Each path (``$_SERVER["REQUEST_URI"]``) is mapped to a secific controller and a specific set of query/get parameters.
A key is generated from a mapEntry.

Example :

Request URI : /index.php?controller=category&id_category=5

Map entry :

```php
[
  "controller-slug" => "category", 
  "controller" => "category", 
  "targetQueryParameter" => "id_category",
]
```

We retrieve controller using Prestashop's own method :

```php
Dispatcher::getInstance()->getController()
```

controller is ``category``.

Each cached entry for this map should take into account the query parameter ``id_category`` in order not to mix up categories.

So the key will be ``my_site_/index.php?controller=category&id_category=5``.

## Docker

Update the environment variables in ``docker-compose.yml`` accordingly.

### Configuration

Update cache configuration values in ``config/static-cache.php``.

- ``$SERVER_NAME``
- ``$HTTP_HOST``

#### $MAP

See "How does it work".

#### $DEFAULT_URLS

List of URLs to cache beside categories and products.

#### blacklistQueryParameters

Do not cache the entry is it has one of the given query parameter.

Used mainly for logout (index.php?mylogout=). 

## Build cache

``php bin/build-cache.php``

## Cache specific URL

``php bin/cahe-url.php index.php?controller=category&id_category=1``

