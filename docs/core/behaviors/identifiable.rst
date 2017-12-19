Identifiable
============

Identifiable is one of the core behaviors of AudienceHero. It consists of a set of interface and trait, used to
add an ``id`` field to entities, in a standard way.

The standard way includes:

- Adding the ``id`` field to the entity.
- Adding the Doctrine mapping and the GUID generation strategy to the field.
- Setting the correct serializer groups

.. tip::

    It is highly recommended to use the ``IdentifiableInterface`` and ``IdentifiableEntity`` when creating an entity.
    It reduces boilerplate and ensure that your entity will play well in the AudienceHero ecosystem.

How to use the Identifiable behavior?
-------------------------------------

The Identifiable behavior is used by describing that your class uses the ``IdentifiableInterface`` and use the ``IdentifiableEntity``.

.. code:: php

    <?php

    namespace App\Entity;

    use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableEntity;
    use AudienceHero\Bundle\CoreBundle\Behavior\Identifiable\IdentifiableInterface;

    class Page implements IdentifiableInterface
    {
        use IdentifiableEntity;
    }

The ``Page`` class now has a ``getId()`` method and a ``id`` field.

.. warning::

    As the Identifiable behavior add a field to your entity, you will need to update your database schema.
