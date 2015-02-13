Usage
=====

Out of the box TaskMessenger bundle works with ConnectionsBundle ``ongr_connections.sync_task_complete`` event.
To register listener for this event to publish messages to AMQP use configuration like this:

.. code-block:: yaml

    #app/config/config.yml
    ongr_task_messenger:
        publishers:
            default:
                amqp: ~

See `configuration <configuration.html>`_ for more details on publisher configuration.

**Usage example using custom publisher (Redis)**

1. Create Event class
---------------------

.. code-block:: php

    class ItemImportEvent extends Event
    {
        const EVENT_NAME = 'acme_test.item_import_event';
        /**
         * @var DocumentInterface
         */
        private $document;

        /**
         * @param DocumentInterface $document
         */
        public function __construct(DocumentInterface $document)
        {
            $this->document = $document;
        }

        /**
         * @return DocumentInterface
         */
        public function getDocument()
        {
            return $this->document;
        }
    }

Event object will be passed to publisher.

2. Create Event listener
------------------------

.. code-block:: php

    class ItemImportListener
    {
        /**
         * @var TaskPublisher
         */
        protected $publisher;

        /**
         * @param TaskPublisher $publisher
         */
        public function __construct($publisher)
        {
            $this->publisher = $publisher;
        }

        /**
         * Handles item import event.
         *
         * @param ViewedItemEvent $event
         */
        public function handleEvent($event)
        {
            $this->publisher->publish($event);
        }
    }

3. Create custom publisher
--------------------------

3.1 Create custom connection factory
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You can create custom factory service `see more <custom_factory_service.html>`_

.. code-block:: php

    class RedisConnectionFactory implements ConnectionFactoryInterface
    {
        /**
         * @var string
         */
        protected $class;

        /**
         * @var string
         */
        protected $host;

        /**
         * @var string
         */
        protected $port;

        /**
         * @var string
         */
        protected $user;

        /**
         * @var string
         */
        protected $password;

        /**
         * @var array
         */
        protected $arguments;

        /**
         * @param string $class
         * @param string $host
         * @param string $port
         * @param string $user
         * @param string $password
         * @param array  $arguments
         */
        public function __construct(
            $class = null,
            $host = null,
            $port = null,
            $user = null,
            $password = null,
            $arguments = []
        ) {
            $this->class = $class;
            $this->host = $host;
            $this->port = $port;
            $this->user = $user;
            $this->password = $password;
            $this->arguments = $arguments;
        }

        /**
         * Connection factory create method.
         *
         * @return ConnectionFactoryInterface
         */
        public function create()
        {
            $class = $this->class;

            return new $class(
                [
                    'scheme' => $this->arguments['scheme'],
                    'host' => $this->host,
                    'port' => $this->port,
                ]
            );
        }
    }

If you do not need custom factory, you can use ``ongr_task_messenger.simple_connection_factory`` service.

.. code-block:: php

    class SimpleConnectionFactory implements ConnectionFactoryInterface
    {
        /**
         * @var string
         */
        protected $class;

        /**
         * @var string
         */
        protected $host;

        /**
         * @var string
         */
        protected $port;

        /**
         * @var string
         */
        protected $user;

        /**
         * @var string
         */
        protected $password;

        /**
         * @param string $class
         * @param string $host
         * @param string $port
         * @param string $user
         * @param string $password
         */
        public function __construct($class = null, $host = null, $port = null, $user = null, $password = null)
        {
            $this->class = $class;
            $this->host = $host;
            $this->port = $port;
            $this->user = $user;
            $this->password = $password;
        }

        /**
         * Creates publisher connection.
         *
         * @return object
         */
        public function create()
        {
            $class = $this->class;

            return new $class($this->host, $this->port, $this->user, $this->password);
        }
    }

3.2 Create Redis publisher
~~~~~~~~~~~~~~~~~~~~~~~~~~

.. code-block:: php

    class RedisPublisher extends AbstractTaskPublisher
    {

        /**
         * Publishes event.
         *
         * @param ItemImportEvent $event
         *
         * @return void
         */
        public function publish($event)
        {
            if (!$this->enabled) {
                return;
            }

            if (!$this->connection) {
                $this->connection = $this->connectionFactory->create();
            }
            $this->send($event);
        }

        /**
         * {@inheritdoc}
         */
        private function send($event)
        {
            $document = $event->getDocument();

            $content = json_encode(
                [
                    'action' => 'ongr.item_import',
                    'document' => [
                        'id' => $document->id,
                        'title' => $document->title,
                    ]
                ]
            );

            $this->connection->lpush('item_import_event', $content);
        }
    }

4. Define services
------------------

.. code-block:: yaml

    services:
        # Only needed if you use custom connection factory
        acme_test.redis_connection_factory:
            class: Acme\TestBundle\Publishers\RedisConnectionFactory

        acme_test.publisher.redis:
            class: Acme\TestBundle\Publishers\RedisPublisher
            arguments:
                - @acme_test.redis_connection_factory
                - %kernel.environment%

        acme_test.item_import_listener:
            class: Acme\TestBundle\EventListener\ItemImportListener
            arguments:
                - @ongr_task_messenger.task_publisher.default
            tags:
                - { name: kernel.event_listener, event: acme_test.item_import_event, method: handleEvent }

5. Add configuration
--------------------

.. code-block:: yaml

    ongr_task_messenger:
        log_level: debug
        publishers:
            default:
                custom:
                    publisher: acme_test.publisher.redis
                    factory: acme_test.redis_connection_factory
                    class: 'Predis\Client'
                    host: 127.0.0.1
                    port: 6379
                    user: guest
                    password: guest
                    arguments:
                        scheme: tcp

6. Dispatch event
-----------------

Dispatch event where needed.

.. code-block:: php

    $event = new ItemImportEvent($document);
    $ev = $this->container->get('event_dispatcher');
    $ev->dispatch($event::EVENT_NAME, $event);
