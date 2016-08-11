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

Activate the Block Manager in your kernel class together will all other required bundles:

```
...

$bundles[] = new Knp\Bundle\MenuBundle\KnpMenuBundle();
$bundles[] = new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle();
$bundles[] = new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle();
$bundles[] = new Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle();
$bundles[] = new Netgen\Bundle\ContentBrowserUIBundle\NetgenContentBrowserUIBundle();
$bundles[] = new Netgen\Bundle\BlockManagerBundle\NetgenBlockManagerBundle();
$bundles[] = new Netgen\Bundle\BlockManagerUIBundle\NetgenBlockManagerUIBundle();
$bundles[] = new Netgen\Bundle\BlockManagerAdminBundle\NetgenBlockManagerAdminBundle();

return $bundles;
```

If using eZ Platform, you also need to activate `NetgenEzPublishBlockManagerBundle`. Make sure it is activated after all other bundles:

```
...

$bundles[] = new Knp\Bundle\MenuBundle\KnpMenuBundle();
$bundles[] = new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle();
$bundles[] = new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle();
$bundles[] = new Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle();
$bundles[] = new Netgen\Bundle\ContentBrowserUIBundle\NetgenContentBrowserUIBundle();
$bundles[] = new Netgen\Bundle\BlockManagerBundle\NetgenBlockManagerBundle();
$bundles[] = new Netgen\Bundle\BlockManagerUIBundle\NetgenBlockManagerUIBundle();
$bundles[] = new Netgen\Bundle\BlockManagerAdminBundle\NetgenBlockManagerAdminBundle();
$bundles[] = new Netgen\Bundle\EzPublishBlockManagerBundle\NetgenEzPublishBlockManagerBundle();

return $bundles;
```

Import database schema
----------------------

Run the following command to create the database schema:

```
php app/console doctrine:migrations:migrate --configuration=vendor/netgen/block-manager/migrations/doctrine.yml
```

Install assets
--------------

Run the following from your repo root to install Block Manager assets:

```
php app/console assets:install --symlink --relative
```

Activate the routes
-------------------

Add the following to your main `routing.yml` file to activate all needed routes:

```
netgen_block_manager:
    resource: "@NetgenBlockManagerBundle/Resources/config/routing.yml"
    prefix: "%netgen_block_manager.route_prefix%"

netgen_content_browser:
    resource: "@NetgenContentBrowserBundle/Resources/config/routing.yml"
    prefix: "%netgen_content_browser.route_prefix%"
```

Disable short alias in JMS Serializer
-------------------------------------

If using JMS Serializer, you will need to disable aliasing its' serializer service to Symfony's `@serializer` service.

Place the following config in your `app/config/config.yml` file to disable the alias:

```
jms_serializer:
    enable_short_alias: false
```

Adjusting your full views
-------------------------

All of your full views need to extend `ngbm.layoutTemplate` variable. If layout was resolved,
this variable will hold the name of the template belonging to the resolved layout. In case when
layout was not resolved, it will hold the name of your main pagelayout template (the one your
full views previously extended: see below for configuring the main pagelayout template). This
makes it possible for your full view templates to be fully generic, that is, not depend whether
there is a resolved layout or not.

Configuring your main pagelayout template
-----------------------------------------

To configure which template is your main pagelayout, use the following semantic configuration
somewhere in your application:

```
netgen_block_manager:
    pagelayout: "NetgenSiteBundle::pagelayout.html.twig"
```

If using eZ Platform 1.3 or later, there's no need setting the main pagelayout, since it will be
picked up automatically from your pagelayout siteaccess config.

Adjusting your base pagelayout template
---------------------------------------

To actually display the resolved layout template in your page, you need to modify your main pagelayout
template to include a Twig block named `layout` which wraps a `content` Twig block and looks like this:

```
{% block layout %}
    {% block content %}{% endblock %}
{% endblock %}
```

There are two goals to achieve with the above Twig block:

* If no layout could be resolved for current page, your full view templates will just keep using the `content`
  Twig block as before
* If layout is resolved, it will use the `layout` block, in which case `content` Twig block will not be used. You
  will of course need to make sure that in this case, all your layouts have a full view block in one of the zones
  which will display your `content` Twig block from full view templates
