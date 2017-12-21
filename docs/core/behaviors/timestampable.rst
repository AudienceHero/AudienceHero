Timestampable
=============

Timestampable is one of the core behaviors of AudienceHero. It is used to track creation and update time of a resource.
It also overrides the ``datetime`` `Doctrine Type <http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html>`_
and converts all stored date and time in the UTC timezone.

.. tip::

    **Best pratice**: It is recommended to use AudienceHero's ``TimestampableEntity`` trait. It you have to deal with date and time,

Store date and time in the UTC time zone
----------------------------------------

Timestampable override the ``datetime`` Doctrine Type. This type converts all date to UTC before storing it to the database. It also
converts all UTC date to the current server timezone when fetching dates from the database. It ensures that date and times will
always be consistent, no matter the current Daylight Saving Time or the current Timezone.

.. code:: php

    <?php

    namespace App\Entity;

    use Doctrine\ORM\Mapping as ORM;

    class Page
    {
        /**
         * @ORM\Column(type="datetime", nullable=true)
         */
        private $lockedAt;

        public function setLockedAt(?\DateTime $lockedAt): void
        {
            $this->lockedAt = $lockedAt;
        }

        public function getLockedAt(): ?\DateTime
        {
            return $this->lockedAt;
        }
    }

It consists of a set of interface and trait, used to know
when entities have been created and updated, in a standard way.

Track creation and update of a resource
---------------------------------------

To track creation or update time of a resource, you have to use the ``TimestampableEntity`` trait.

.. code:: php

    <?php

    namespace App\Entity;

    use AudienceHero\Bundle\CoreBundle\Behavior\Timestampable\TimestampableEntity;

    class Page
    {
        use TimestampableEntity;
    }

The ``TimestampableEntity`` traits adds:

- A ``createdAt`` field, which is automatically populated when the resource is persisted in the database.
- An ``updatedAt`` field, which is automatically updated when the resource is updated.
- A ``setCreatedAt`` method to override the creation date.
- A ``setUpdatedAt`` method to override the update date.
- Two ``getCreatedAt`` and ``getUpdatedAt`` methods.

The ``createdAt`` and ``updatedAt`` fields are not writable from the API. They are readable **only** by their owner.

.. warning::

    As the Timestampable behavior add a field to your entity, you will need to update your database schema.
