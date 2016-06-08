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

Activate the Block Manager in your kernel class:

```
...

$bundles[] = new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle();
$bundles[] = new Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle();
$bundles[] = new Netgen\Bundle\BlockManagerBundle\NetgenBlockManagerBundle();
$bundles[] = new Netgen\Bundle\BlockManagerAdminUIBundle\NetgenBlockManagerAdminUIBundle();

return $bundles;
```

If using eZ Platform, you also need to activate `NetgenEzPublishBlockManagerBundle`. Make sure it is activated after the main bundle:

```
...

$bundles[] = new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle();
$bundles[] = new Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle();
$bundles[] = new Netgen\Bundle\BlockManagerBundle\NetgenBlockManagerBundle();
$bundles[] = new Netgen\Bundle\BlockManagerAdminUIBundle\NetgenBlockManagerAdminUIBundle();
$bundles[] = new Netgen\Bundle\EzPublishBlockManagerBundle\NetgenEzPublishBlockManagerBundle();

return $bundles;
```

Import database schema
----------------------

Run the following command to create the database schema:

```
php app/console doctrine:migrations:migrate --configuration=vendor/netgen/block-manager/migrations/doctrine.yml
```

Activate the routes
-------------------

Add the following to your main `routing.yml` file to activate all needed routes:

```
_netgen_block_manager:
    resource: "@NetgenBlockManagerBundle/Resources/config/routing.yml"
    prefix: "%netgen_block_manager.route_prefix%"

_netgen_block_manager_admin:
    resource: "@NetgenBlockManagerAdminUIBundle/Resources/config/routing.yml"
    prefix: "%netgen_block_manager.route_prefix%"

_netgen_content_browser:
    resource: "@NetgenContentBrowserBundle/Resources/config/routing.yml"
    prefix: "%netgen_content_browser.route_prefix%"
```

Adjusting your full views
-------------------------

All of your full views need to extend `NetgenBlockManagerBundle::layout_resolver.html.twig` template. This template will
be used for loading a resolved layout template. In case there is no resolved layout, it will fallback to your base
pagelayout template (the one your full views previously extended).

Adjusting your base pagelayout template
---------------------------------------

To actually display the layout template in your page, you need to modify your base pagelayout template and wrap your
main Twig block in another block named `layout`. For example, if your main Twig block is named `content`, your pagelayout
needs to look like this:

```
{% block layout %}
    {% block content %}{% endblock %}
{% endblock %}
```

There are two goals to wrapping your main block like this:

* If no layout could be resolved for current page, your full view templates will just keep using the main block
  `content` as before
* If layout is resolved, it will use the `layout` block, in which case `content` block will not be used. You
  will of course need to make sure that in this case, all your layouts have a block in one of the zones
  which will display your main Twig block from full view templates

Configuring your base pagelayout template
-----------------------------------------

To configure which template is your base template, use the following semantic configuration somewhere in your
application:

```
netgen_block_manager:
    pagelayout: "NetgenSiteBundle::pagelayout.html.twig"
```

If using eZ Platform, there's no need setting the main pagelayout, since it will be picked up from default eZ Platform config.
