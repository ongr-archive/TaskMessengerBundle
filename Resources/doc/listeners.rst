Event Listener configuration
----------------------------

To register an event listener add it to your services.yml and load it via your bundles ``Extension`` class.

.. code-block:: yaml

    acme_test.sync_task_complete_listener:
        class: %ongr_task_messenger.sync_task_complete_listener.class%
        arguments:
            - @ongr_task_messenger.task_publisher.foo_publisher
        tags:
            - { name: kernel.event_listener, event: ongr_connections.sync_task_complete, method: handleEvent }

To use different event than ``ongr_connections.sync_task_complete``
one have to create event listener and `custom event publisher <custom_publisher_service.html>`_
