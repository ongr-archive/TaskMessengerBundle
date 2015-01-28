Setup
=====

Step 1: Install TaskMessenger bundle
------------------------------------

TaskMessenger bundle is installed using `Composer`_.

.. code:: bash

    php composer.phar require ongr/task-messenger-bundle "dev-master"

Step 2: Enable TaskMessenger bundle
-----------------------------------

Enable TaskMessenger bundle in your AppKernel:

.. code:: php

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = return [
           ...
           new ONGR\TaskMessengerBundle\ONGRTaskMessengerBundle(),
           ...
       ];;
    }

Step 3: Add configuration
-------------------------

TaskMessenger has two publishers to work with ``ConnectionsBundle`` ``ongr_connections.sync_task_complete`` event.
Add minimal configuration for TaskMessenger bundle to use with ConnectionsBundle.

.. code:: yaml

    #app/config/config.yml
    ongr_task_messenger:
        publishers:
            default:
                amqp: ~
                beanstalkd: ~

This will register event listener for ``ongr_connections.sync_task_complete`` event and publish message to AMQP and beanstalkd.
More about `publishers configuration <configuration.html>`_ and `custom publisher <custom_publisher_service.html>`_ can be found here.

Step 4: Use your new bundle
---------------------------

Usage documentation for the TaskMessenger bundle is available `here <usage.html>`_.

.. _Composer: https://getcomposer.org
