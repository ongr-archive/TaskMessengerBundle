Custom Connection factory service
---------------------------------

One can create custom connection factory service.

Class must implement ``ConnectionFactoryInterface``.

Example can be found `RedisConnectionFactory.php <https://github.com/ongr-io/TaskMessengerBundle/blob/master/Tests/app/fixture/Acme/TestBundle/Publishers/RedisConnectionFactory.php>`_


Connection factory must be defined as service:

Example:

.. code-block:: yaml

    services:
      acme_test.redis_connection_factory:
         class: ONGR\TaskMessengerBundle\Tests\app\fixture\Acme\TestBundle\Publishers\RedisConnectionFactory
