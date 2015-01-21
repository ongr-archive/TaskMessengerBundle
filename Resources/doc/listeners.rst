Event Listener configuration
----------------------------

To register an event listener add it to your bundles ``Resources\config\services.yml``.

.. code-block:: yaml

      acme_test.viewed_item_listener:
        class: %acme_test.viewed_item_listener.class%
        arguments:
          - @ongr_task_messenger.task_publisher.default
          #Assuming you have task publisher configured under `default` name.
        tags:
            - { name: kernel.event_listener, event: acme_test.viewed_item_event, method: handleEvent }

You should pass ``TaskMessenger`` service to listener to be able publish messages.


**SyncTaskListener**

Bundle has `SyncTaskListener <https://github.com/ongr-io/TaskMessengerBundle/blob/master/Service/SyncTasksListener.php>`_ created to work with ConnectionsBundle ``ongr_connections.sync_task_complete`` event.

To use it in your bundle simply add it to your bundles ``Resources\config\services.yml``.

.. code-block:: yaml

   acme_test.sync_task_complete_listener:
        class: %ongr_task_messenger.sync_task_complete_listener.class%
        arguments:
            - @ongr_task_messenger.task_publisher.default
            #Assuming you have task publisher configured under `default` name.
        tags:
            - { name: kernel.event_listener, event: ongr_connections.sync_task_complete, method: handleEvent }
