.. _index-skeleton-view-helpers-label:

View helpers
============

View helpers extend the :code:`View` functionality adding a possibility to call you own logic inside `html` templates
(:ref:`view more details about views <index-view-label>`).

Config
------

The :code:`config` helper allows you access to any registered configs.
For example the `main layout` displays a site name as following:

.. code-block:: html

    // sourced from: src/Module/Base/view/layout/base.phtml

    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <title><?= $this->config('site')['name'] ?></title>
        </head>
        ...


You only need to pass a config key to get an access to its values. the config file itself:

.. code-block:: php

    <?php

        // sourced from: src/Module/Base/config.php

        return [
            'site' => [
                'name' => 'Test site'
            ],
            ...
        ];

Url
---

Using the the :code:`Url` helper you can display a `url`, based on a some registered route. Lets check a look an example:

.. code-block:: html

    // sourced from: src/Module/Base/view/layout/base.phtml

     <body>
        <nav>
            <a href="<?= $this->url('UserController', 'list', 'User') ?>">Users</a>
        </nav>
        ...

To correctly build an url you need to pass there a :code:`controller` name, a :code:`method` and a :code:`module` name.
If the helper cannot resolve a route based on received parameters it triggers an :code:`Exception`.

Partial View
------------

Some times we need to use already created templates in other templates in order to reduce a duplicate code.

`Lets consider an example:`

We are going to display a user list in many places on the site. So first of all it would be great to create
a user template:

.. code-block:: html

    // sourced from: src/Module/User/view/partial/user.phtml

    <div>
        <b><?= $this->id ?></b>: <?= $this->name ?>
    </div>

Now we are ready to use it everywhere.

.. code-block:: html

    // sourced from: src/Module/User/view/UserController/list.phtml

    <ul>
        <?php foreach ($this->users as $user): ?>
            <li>
                <?= $this->partialView('partial/user', 'User',  $user) ?>
            </li>
        <?php endforeach ?>
    </ul>

To use a partial view you need to pass there a :code:`path` to the template file and a :code:`module`
name where that file is located. And the last parameter is the template's :code:`variables`.

Custom
------

Let's implement a very simple custom helper for demonstration,
let say It would return a random value  (suppose it's a `CustomModule`).

.. code-block:: php

    <?php

        namespace Tiny\Skeleton\Module\CustomModule\EventListener\ViewHelper;

        class ViewHelperRandomListener
        {
            /**
             * @param  Event  $event
             */
            public function __invoke(Event $event)
            {
                // we don't use any arguments in this example
                $arguments = $event->getParams()['arguments'];

                $event->setData(rand());
            }
        }

Then we need to register this listener  class in configs:

.. code-block:: php

    <?php

        // Module/CustomModule/config.php

        use Tiny\Skeleton\Module\Base\EventListener;
        use Tiny\View\View;
        use Tiny\Skeleton\Module\CustomModule;
        use Tiny\ServiceManager\Factory\InvokableFactory;

        return [
            'service_manager' => [
                'shared' => [
                    ...
                    // we don't need any dependencies that's why we are using the "InvokableFactory"
                    CustomModule\EventListener\ViewHelper\ViewHelperRandomListener::class  => InvokableFactory::class,
                ]
            ],
            'listeners' => [
                // view helper
                ...
                [
                    'event'    => View::EVENT_CALL_VIEW_HELPER.'random',
                    'listener' => EventListener\ViewHelper\ViewHelperRandomListener::class,
                ],
                ...
            ]
        ];

Now we can use it in templates, like:

.. code-block:: html

    <div>
        <b><?= $this->random() ?></b>
    </div>
