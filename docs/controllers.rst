.. _index-skeleton-controllers-label:

Controllers
===========

Controllers it's `mediator` between all incoming requests and your business logic.
So please keep they simple as possible, don't store any complex logic inside controllers, rather keep you logic in services.

All new controllers must extend the :code:`AbstractController` to get a few helpful methods.

Json Response
-------------

When you need to return a :code:`json` based response from your services you may use the method :code:`jsonResponse` as in example below:

.. code-block:: php

    <?php

        // sourced from: src/Module/User/Controller/UserApiController.php

        namespace Tiny\Skeleton\Module\User\Controller;

        use Tiny\Http\AbstractResponse;

        class UserApiController extends AbstractUserController
        {

            /**
             * @param  AbstractResponse  $response
             */
            public function list(AbstractResponse $response)
            {
                $this->jsonResponse($response, $this->userService->getAllUsers());
            }

        }

The method :code:`jsonResponse` it's just a wrapper for the :code:`Response` object.

.. code-block:: php

    <?php

        // sourced from: src/Module/Base/Controller/AbstractController.php

        protected function jsonResponse(
            AbstractResponse $response,
            array $variables = [],
            int $code = AbstractResponse::RESPONSE_OK
        ): AbstractResponse {
            $response->setResponse(json_encode($variables))
                ->setCode($code)
                ->setResponseType(
                    AbstractResponse::RESPONSE_TYPE_JSON
                );

            return $response;
        }

View Response
-------------

if you need to return an `html` based response you may use the :code:`viewResponse` method;

.. code-block:: php

    <?php

        // sourced from: src/Module/User/Controller/UserController.php

        use Tiny\Http\AbstractResponse;

        class UserController extends AbstractUserController
        {

            /**
             * @param  AbstractResponse  $response
             */
            public function list(AbstractResponse $response)
            {
                $this->viewResponse($response, [ // passing a variable list in a template
                    'users' => $this->userService->getAllUsers()
                ]);

                // don't forget to create a template at: view/UserController/list.phtml
            }

        }

.. code-block:: html

    // sourced from: src/Module/User/view/UserController/list.phtml

    <ul>
        <?php foreach ($this->users as $user): ?>
            <li>
                <?= $user['name'] ?>
            </li>
        <?php endforeach ?>
    </ul>

The method :code:`viewResponse` it's just a wrapper for the :code:`Response` object.

.. code-block:: php

    <?php

        // sourced from: src/Module/Base/Controller/AbstractController.php

        protected function viewResponse(
            AbstractResponse $response,
            array $variables = [],
            int $code = AbstractResponse::RESPONSE_OK
        ): AbstractResponse {
            $response->setResponse(new View($variables))
                ->setCode($code)
                ->setResponseType(
                    AbstractResponse::RESPONSE_TYPE_HTML
                );

            return $response;
        }

You may ask which `html template` is used in this case (I don't see anything related with a path), but the magic is hidden under the hood.
Generally speaking the :code:`application` subscribes to the :code:`ControllerEvent::EVENT_AFTER_CALLING_CONTROLLER`
event and generates a template path dynamically it means you don't need to provide it manually every time (but you still can do that).

Below the some peace of code from :code:`AfterCallingControllerViewInitListener` which is in charge of generating path.

.. code-block:: php

    <?php

        // sourced from: src/Module/Base/EventListener/Application/AfterCallingControllerViewInitListener.php

        // make sure we received an instance of "View" object
        if ($controllerResponse instanceof View) {
            // set both layout and template path (if they are missing)
            // the path generator uses the mask: "view/[ControllerName/ControllerAction"
            if (!$controllerResponse->getTemplatePath()) {
                $controllerResponse->setTemplatePath(
                    $this->getTemplatePath($event->getParams()['route'])
                );
            }

            // by default we setup a layout which is defined in configs "base_layout_path"
            if (!$controllerResponse->getLayoutPath()) {
                // get the View's configs
                $viewConfig = $this->configService->getConfig('view', []);

                $controllerResponse->setLayoutPath(
                    $this->viewHelperUtils->getTemplatePath(
                        $viewConfig['base_layout_path'],
                        'Base'
                    )
                );
            }

            $controllerResponse->setEventManager($this->eventManager);

            // return the modified response
            $response->setResponse(
                $controllerResponse
            );

            // replace the data in event
            $event->setData($response);
        }

To know more about the :code:`View` :ref:`read the chapter <index-view-label>`

To know more about the :code:`Application events` :ref:`read the chapter <index-skeleton-lifecycle-events-label>`

Custom Response
---------------

Some time you need a custom response based on you needs. In that case you can modify the :code:`Response` object directly in controllers.
Let say we need to return an :code:`XML` based response:

.. code-block:: php

    <?php

        use Tiny\Http\AbstractResponse;

        class UserXmlController extends AbstractUserController
        {

            /**
             * @param  AbstractResponse  $response
             */
            public function list(AbstractResponse $response)
            {
                $user = [
                  'name' => 'tester',
                  'country' => 'USA',
                ];

                // convert the array to an xml string
                $xml = new SimpleXMLElement('<root/>');
                array_walk_recursive($user, [$xml, 'addChild']);

                $response->setResponse($xml->asXML())
                    ->setCode(200)
                    ->setResponseType('text/xml');

                return $response;
            }

        }

Exceptions
----------

Sometimes we need to show special pages like `404 (Not found)`, `401 (Unauthorized)`, etc
within our controllers. So how can we achieve that? We can easily trigger an :code:`Exception`.
Then we may subscribe (using the :code:`Event manager` and the :code:`application's lifecycle`) to that :code:`Exception` and show a special page.
Let's check a look how we handle the  `404` page in the app
(using the similar way you can implement handling of any kind of errors):


Let's suppose we have a controller:

.. code-block:: php

    <?php

        namespace Tiny\Skeleton\Module\User\Controller;

        use Tiny\Http\AbstractResponse;
        use Application/Exception/Request/NotFoundException.php;

        class UserApiController extends AbstractUserController
        {

            /**
             * @param  AbstractResponse  $response
             */
            public function list(AbstractResponse $response)
            {
                $users = $this->userService->getAllUsers();

                if (!$users) {
                    // we need to show the "404" page (because we don't have users)
                    throw new NotFoundException();
                }

                $this->jsonResponse($response, $users);
            }

        }

Now lets create a file: :code:`NotFoundException.php` in the :code:`Application/Exception/Request/` folder

.. code-block:: php

    <?php

        namespace Tiny\Skeleton\Application\Exception\Request;

        use Exception;

        class NotFoundException extends Exception implements ExceptionInterface
        {

        }

Now we only to need capture that :code:`Exception` and show a page. Let's create a listener for that:

.. code-block:: php

    <?php

        // sourced from: Application/Exception/Request/NotFoundException.php

        namespace Tiny\Skeleton\Module\Base\EventListener\Application;

        use Tiny\EventManager\EventManager;
        use Tiny\Http\AbstractResponse;
        use Tiny\Router\Route;
        use Tiny\Skeleton\Application\EventManager\ControllerEvent;
        use Tiny\Skeleton\Application\Exception\Request\NotFoundException;
        use Tiny\Skeleton\Module\Base\Utils\ViewHelperUtils;
        use Tiny\View\View;

        class ControllerExceptionNotFoundListener
            extends AbstractControllerExceptionListener
        {

            /**
             * @var AbstractResponse
             */
            private AbstractResponse $response;

            /**
             * @var EventManager
             */
            private EventManager $eventManager;

            /**
             * @var ViewHelperUtils
             */
            private ViewHelperUtils $viewHelperUtils;

            /**
             * ControllerExceptionNotFoundListener constructor.
             *
             * @param  AbstractResponse  $response
             * @param  EventManager      $eventManager
             * @param  ViewHelperUtils   $viewHelperUtils
             */
            public function __construct(
                AbstractResponse $response,
                EventManager $eventManager,
                ViewHelperUtils $viewHelperUtils
            ) {
                $this->response = $response;
                $this->eventManager = $eventManager;
                $this->viewHelperUtils = $viewHelperUtils;
            }

            /**
             * @param  ControllerEvent  $event
             */
            public function __invoke(ControllerEvent $event)
            {
                $eventParams = $event->getParams();
                $exception = $eventParams['exception'] ?? null;

                /** @var Route $route */
                $route = $eventParams['route'] ?? null;

                // we handle only "NotFoundException" exception (all other will be skipped)
                if ($exception && $route && $exception instanceof NotFoundException) {
                    $errorMessage = $exception->getMessage() ?: 'Not found';

                    // we either show a json based "404" response (for the api context) or html based "404" page
                    if ($this->isJsonErrorResponse($route->getContext())) {
                        $this->jsonErrorResponse(
                            $this->response,
                            $errorMessage,
                            AbstractResponse::RESPONSE_NOT_FOUND
                        );
                    } else {
                        $this->viewErrorResponse(
                            $this->response,
                            $this->getView($errorMessage),
                            AbstractResponse::RESPONSE_NOT_FOUND
                        );
                    }

                    $event->setData($this->response);
                }
            }

            /**
             * @param  string  $errorMessage
             *
             * @return View
             */
            private function getView(string $errorMessage): View
            {
                $view = new View(
                    [
                        'message' => $errorMessage,
                    ]
                );
                // set path to the "404" page and its layout
                $view->setTemplatePath(
                    $this->viewHelperUtils->getTemplatePath(
                        'NotFoundController/index', 'Base'
                    )
                )
                    ->setLayoutPath(
                        $this->viewHelperUtils->getTemplatePath(
                            'layout/base', 'Base'
                        )
                    )
                    ->setEventManager($this->eventManager);

                return $view;
            }

        }

And we need to register our listener in the configs:

.. code-block:: php

    <?php

        // sourced from: src/Module/Base/config/listeners.php

        use Tiny\Skeleton\Application\EventManager;
        use Tiny\Skeleton\Module\Base\EventListener;

        return [
            // application
            ...
            [
                // whenever we have an exception in controllers we call our listener to check the error type
                'event'    => EventManager\ControllerEvent::EVENT_CONTROLLER_EXCEPTION,
                'listener' => EventListener\Application\ControllerExceptionNotFoundListener::class,
            ],
            ...
        ];

To read more about the app's :ref:`lifecycle <Controller events>`

**PS:** By default all uncaught errors will be displayed in the `500` error page.