Netgen Block Manager installation instructions
==============================================

Use Composer
------------

Run the following command to install Netgen Block Manager:

```
composer require netgen/block-manager:^1.0
```

Activate the bundles
--------------------

Activate the Block Manager in your kernel class together with other required bundles:

```
...

$bundles[] = new Netgen\Bundle\BlockManagerBundle\NetgenBlockManagerBundle();

return $bundles;
```

If using eZ Platform, you also need to activate `NetgenEzPublishBlockManagerBundle`:

```
$bundles[] = new Netgen\Bundle\EzPublishBlockManagerBundle\NetgenEzPublishBlockManagerBundle();
```

Activate the routes
-------------------

Add the following to your main `routing.yml` file to activate Block Manager routes:

```
_netgen_block_manager:
    resource: "@NetgenBlockManagerBundle/Resources/config/routing.yml"
    prefix: /bm
```
