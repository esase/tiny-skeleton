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
