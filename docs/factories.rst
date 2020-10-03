.. _index-skeleton-factories-label:

Factories
=========

Whenever you need a  :code:`Class file` (`a listener`, `a service`, `a controller`, etc) you have to register that class in configs,
aside from that each class should have its own :code:`factory` which initializes the class object and resolves its dependencies.

Lets consider we need to have a service :code:`TestService` (suppose it’s a `CustomModule`), let's create a a file:

.. code-block:: php

    <?php

    namespace Tiny\Skeleton\Module\CustomModule\Service;

    use Tiny\Skeleton\Module\AnotherCustomModule\Service\AnotherTestService;

    class TestService
    {
        /**
         * @var AnotherTestService
         */
        private $anotherTestService;

        /**
         * TestService constructor.
         *
         * @param  AnotherTestService   $anotherTestService
         */
        public function __construct(AnotherTestService $anotherTestService)
        {
            // this dependency will be resolved in a factory below
            $this->anotherTestService = $anotherTestService;
            ...
        }

        ...
    }

Now we need a  :code:`factory` for that:

.. code-block:: php

    <?php

        namespace Tiny\Skeleton\Module\CustomModule\Service\Factory;

        use Tiny\Skeleton\Module\AnotherCustomModule\Service\AnotherTestService;

        class TestServiceFactory
        {

            /**
             * @param  ServiceManager  $serviceManager
             *
             * @return TestService
             */
            public function __invoke(ServiceManager $serviceManager): TestService
            {
                return new TestService(
                    // the 'Service manager' helps us to resolve dependencies
                    $serviceManager->get(AnotherTestService::class)
                );
            }

        }

And the final thing, we need to register those both classes in the `config`:

.. code-block:: php

    <?php

        // Module/CustomModule/config/service-manager.php

        use Tiny\Skeleton\Module\CustomModule;

        return [
            'shared' => [
                // service
                CustomModule\Service\TestService::class => CustomModule\Service\Factory\TestServiceFactory::class,
            ],
        ];

You can make that service either :code:`Singleton` or :code:`Discrete` :ref:`view more details <index-service-manager-label>`.
