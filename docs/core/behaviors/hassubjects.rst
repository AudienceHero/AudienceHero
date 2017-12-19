HasSubjects
===========

HasSubjects is one of the core behavior of AudienceHero. It provides a standard way to loosely couple Resources implementing
the ```IdentifiableInterface <doc:/core/behaviors/identifiable>`_`` together.

How to use the HasSubjects behavior?
------------------------------------

The Referenceable behavior is used by describing that your class uses the ``HasSubjectsInterface`` interface and uses the
``HasSubjectsEntity`` trait.

.. code:: php

    <?php

    use AudienceHero\Bundle\CoreBundle\Behavior\HasSubjects\HasSubjectsEntity;
    use AudienceHero\Bundle\CoreBundle\Behavior\HasSubjects\HasSubjectsInterface;

    class Page implements HasSubjectsInterface
    {
        use HasSubjectsEntity;
    }


The ``Page`` class now has this set of methods:

- public function getSubjects(): array;
- public function setSubjects(array $subjects): void;
- public function getSubject(string $key): ?IdentifiableInterface;
- public function addSubject(IdentifiableInterface $identifiable): HasSubjectsInterface;
- public function removeSubject(IdentifiableInterface $identifiable): HasSubjectsInterface;

.. warning::

    As the HasSubjects behavior add fields to your entity, you will need to update your database schema.

Known Limitations
-----------------

The current implementation of the HasSubjects behavior limits the number of resources you can couple with
a class implementing the ``HasSubjects`` interface. You won't be able to couple two resources of the same class.

.. code:: php

    $page = new Page();
    $firstSubject = new Subject(); // imaginary class implementing the IndentifiableInterface
    $secondSubject = new Subject(); // imaginary class implementing the IndentifiableInterface
    $link = new Link(); // imaginary class implementing the IndentifiableInterface

    // not supported
    $page->addSubject($firstSubject);
    $page->addSubject($secondSubject);

    // supported
    $page->addSubject($firstSubject);
    $page->addSubject($thirdSubject);



