Setup
=====

Step 1: Install TaskMessenger bundle
------------------------------------

TaskMessenger bundle is installed using `Composer`_.

.. code:: bash

    php composer.phar require ongr/task-messenger-bundle "dev-master"

Step 2: Enable TaskMessenger bundle
-----------------------------------

TaskMessenger bundle depends on ``ConnectionsBundle`` therefore ``ConnectionsBundle`` must be enabled before ``TaskMessengerBundle``.

Enable TaskMessenger bundle in your AppKernel:

.. code:: php

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = return [
           ...
           new ONGR\ConnectionsBundle\ONGRConnectionsBundle(),
           ...
           new ONGR\TaskMessengerBundle\ONGRTaskMessengerBundle(),
           ...
       ];;
    }

Step 3: Add configuration
-------------------------

Add minimal configuration for TaskMessenger bundle to use with ConnectionsBundle.

.. code:: yaml

    #app/config/config.yml
    ongr_task_messenger:
        publishers:
            default:
                amqp: ~
                beanstalkd: ~

This will register event listener for ``ongr_connections.sync_task_complete`` event and publish message to AMQP and beanstalkd.
More about `publishers configuration <configuration.rst>`_ and `custom publisher <custom_publisher_service.rst>`_ can be found here.

Step 4: Use your new bundle
---------------------------

Usage documentation for the TaskMessenger bundle is available in `<usage.rst>`_.

.. _Composer: https://getcomposer.org