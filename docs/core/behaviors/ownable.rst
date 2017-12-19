Ownable
=======

Ownable is one of the core behaviors of AudienceHero. It consists of a set of interface and trait, used to take profit
of the multi-tenant system, in a standard way.

The standard way includes:

- Adding the ``owner`` field to the entity.
- Adding the Doctrine mapping.
- Setting the correct serializer groups.
- Setting the validator assertions.

.. tip::

    It is highly recommended to use the ``OwnableInterface`` and ``OwnableEntity`` when creating an entity.
    It reduces boilerplate and ensure that your entity will play well in the AudienceHero ecosystem.

How to use the Ownable behavior?
--------------------------------

The Ownable behavior is used by describing that your class uses the ``OwnableInterface`` and use the ``OwnableEntity`` trait.

.. code:: php

    <?php

    namespace App\Entity;

    use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableEntity;
    use AudienceHero\Bundle\CoreBundle\Behavior\Ownable\OwnableInterface;

    class Page implements OwnableInterface
    {
        use OwnableEntity;
    }

The ``Page`` class now has ``getOwner()`` and ``setOwner()`` methods and a ``owner`` field.

.. warning::

    As the Ownable behavior add a field to your entity, you will need to update your database schema.
