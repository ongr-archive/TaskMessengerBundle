Custom Publisher service
------------------------

One can create custom publisher service.

Class must extend ``AbstractTaskPublisher`` and implement ``publish`` method.

Example can be found `RedisPublisher.php <https://github.com/ongr-io/TaskMessengerBundle/blob/master/Tests/app/fixture/Acme/TestBundle/Publishers/RedisPublisher.php>`_


Publisher must be defined as service:

Example:

.. code-block:: yaml

    services:
        acme_test.publisher.redis:
        class: ONGR\TaskMessengerBundle\Tests\app\fixture\Acme\TestBundle\Publishers\RedisPublisher
        arguments:
            - @acme_test.redis_connection_factory
            - %kernel.environment%
