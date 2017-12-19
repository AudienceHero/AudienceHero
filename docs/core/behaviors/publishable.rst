Publishable
===========

Publishable is one of the core behaviors of AudienceHero. It consists of a set of interface and trait, used to be able to
manage the visibility of a resource (private / public / visibile only with a link) and to schedule when a resource can
be switch to public.

.. tip::

    It is highly recommended to use the ``PublishableInterface`` and ``PublishableEntity`` trait when creating an entity.
    It reduces boilerplate and ensure that your entity will play well in the AudienceHero ecosystem.

Visibility
----------

The Publishable behavior handles 4 visiblity states: ``private``, ``unlisted``, ``public``, or ``scheduled``.

- ``private`` means that the resource is only viewable by its owner.
- ``public`` means that everybody can access the resource.
- ``unlisted`` means that the resource is accessible only to those who have the link. It is not publicly listed in sitemaps.
- ``scheduled`` means that the resource is in the ``private`` state and at the given time, it will be automatically changed
  to the ``public`` state by the system.

How to enable the Publishable behavior on a resource?
-----------------------------------------------------

The Publishable behavior is used by describing that your class uses the ``PublishableInterface`` and use the ``PublishableEntity`` trait.

.. code:: php

    <?php

    namespace App\Entity;

    use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableEntity;
    use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\PublishableInterface;

    class Page implements PublishableInterface
    {
        use PublishableEntity;
    }

The ``Page`` class now has a set of methods to manipulate the visiblity of the entity. Please refer to the interface
and the trait.

.. warning::

    As the Publishable behavior add fields to your entity, you will need to update your database schema.

How to safely change a resource visibility to ``public``?
---------------------------------------------------------

The Publishable behavior provides a ``Publisher`` class that handle this case perfectly. If you need to change the
visiblity of a resource, you **SHOULD NOT** call directly the ``PublishableEntity::publish``, but you should call the
``Publisher::publish`` method because in the future, changing a resource visibility might trigger some side effects
(like dispatching an event).

.. code:: php

    <?php

    use AudienceHero\Bundle\CoreBundle\Behavior\Publishable\Security\Authorization\Voter\PublishableVoter;

    /** @var PublishableInterface $object
    $object = new MyPublishableObject();

    $object->publish(); // Do not do that

    /** @var Publisher $publisher */
    $publisher->publish($object); // do that

Integration of the Publishable behavior with the Symfony Security component
---------------------------------------------------------------------------

A voter is available to determine if a resource can be viewed.

.. code:: php

    <?php

    /** @var AuthorizationCheckerInterface $authorizationChecker */
    /** @var PublishableInterface $object */
    if ($authorizationChecker->isGranted(PublishableVoter::SEE, $object)) {
        // do stuff if the authorization is granted
    } else {
        // do stuff if the resource is not viewable
    }

Publish all scheduled resources
-------------------------------

In order to publish resources that are scheduled for ``public`` visibility, a command is available.

.. code:: shell

    ./bin/console audiencehero:core:publish

