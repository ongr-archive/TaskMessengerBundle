Configuration
-------------

Enable bundle
=============

Enable Task Messenger bundle in your ``AppKernel.php``:

``ONGRConnectionsBundle`` must be enabled before ``ONGRTaskMessengerBundle``.

.. code-block:: php

   public function registerBundles()
   {
       return [
           ...
           new ONGR\ConnectionsBundle\ONGRConnectionsBundle(),
           ...
           new ONGR\MonitoringBundle\ONGRMonitoringBundle(),
       ];
   }

Publishers configuration
========================

TaskPublisher can have multiple publishers configured.
When event is triggered it will loop trough each configured publisher and execute publishers ``publish`` method.

**Publishers**
==============

AMQP publisher
==============

To enable AMQP publisher with default values:

.. code-block:: yaml

    ongr_task_messenger:
        publishers:
            default:
                amqp: ~


With custom values:

.. code-block:: yaml

    ongr_task_messenger:
        publishers:
            default:
                amqp:
                    host: 192.168.1.1
                    ...



========== ============================================= ===========================
**Option** **Default value**                              **Description**
---------- --------------------------------------------- ---------------------------
factory    ongr_task_messenger.simple_connection_factory Factory service
publisher  ongr_task_messenger.publisher.amqp            Publisher service
class      PhpAmqpLib\Connection\AMQPConnection          Class used to create connection object
host       127.0.0.1                                     AMQP host
port       5672                                          AMQP port
user       guest                                         Login user
password   guest                                         Login password
========== ============================================= ===========================


Beanstalkd publisher
====================

To enable beanstalkd publisher with default values:

.. code-block:: yaml

    ongr_task_messenger:
        publishers:
            default:
                beanstalkd: ~


With custom values:

.. code-block:: yaml

    ongr_task_messenger:
        publishers:
            default:
                beanstalkd:
                    host: 192.168.1.1
                    ...


========== ============================================= ===========================
**Option** **Default value**                              **Description**
---------- --------------------------------------------- ---------------------------
factory    ongr_task_messenger.simple_connection_factory Factory service
publisher  ongr_task_messenger.publisher.beanstalkd      Publisher service
class      Pheanstalk\Pheanstalk                         Class used to create connection object
host       127.0.0.1                                     beanstalkd host
port       11300                                         beanstalkd port
user       null                                          Login user
password   null                                          Login password
========== ============================================= ===========================



Custom publisher
================

One can configure custom publisher and pass additional parameters to constructor.
Publishers name node must be ``custom``.

.. code-block:: yaml

    ongr_task_messenger:
        publishers:
            foo_publisher:
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


========== ============================================= ======== ======================================
**Option** **Default value**                             Required **Description**
---------- --------------------------------------------- -------- --------------------------------------
factory                                                  yes      Factory service
publisher                                                yes      Publisher service
class                                                    yes      Class used to create connection object
host       127.0.0.1                                     no       host address
port                                                     yes      host port
user       null                                          no       Login user
password   null                                          no       Login password
========== ============================================= ======== ======================================



Example bundle configuration
============================

For full bundle configuration example see `config_test.yml <https://github.com/ongr-io/TaskMessengerBundle/blob/master/Tests/app/config/config_test.yml>`_

