Referenceable
=============

Referenceable is one of the core behavior of AudienceHero. It provides a way to add a ``reference`` field in a resource.
The ``reference`` field can be used by the end user as a private internal identifier. It is never disclosed to anybody else
than the owner of the Resource.

Let's take for example the ``Mailing`` resource. Alice wants to send the same mailing
to three different groups, at three different time. She creates 3 differents resources, and uses the ``reference`` field
as a way to discrimante between them, like this:

- Party announcement to friends
- Party announcement to early adopters
- Party announcement to bloggers

This way, she can quickly identify them.

How to use the Referenceable behavior?
--------------------------------------

The Referenceable behavior is used by describing that your class uses the ``ReferenceableInterface`` interface and uses the
``ReferenceableEntity`` trait.

.. code:: php

    <?php

    use AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableEntity;
    use AudienceHero\Bundle\CoreBundle\Behavior\Referenceable\ReferenceableInterface;

    class Page implements ReferenceableInterface
    {
        use ReferenceableEntity;
    }


The ``Page`` class now has a set of methods to set and get the ``reference`` field. Please refer to the interface
and the trait.

.. warning::

    As the Referenceable behavior add fields to your entity, you will need to update your database schema.
