How to add custom links to a Resource?
======================================

When dealing with a resource, you might want to be able to add custom links
to the API response. These links can then be used by a client in order
to redirect a user to another represantation of this resource. This is a common
use case and is integrated right into the core of AudienceHero thanks to the Linkable
behavior.

Let's take the exemple of ``PodcastChannel`` resource. This resource has a public page,
accessible from anybody on the internet. Our user wants to be able to share the link
to the public page, right from the Admin section of AudienceHero. To achieve that,
we need to add to the JSON representation of a ``PodcastChannel`` resource the public page URL.

We will achieve our goal very quickly by:

1. Declaring that the ``PodcastChannel`` class implements the ``LinkableInterface`` interface.
2. Declaring that the ``PodcastChannel`` class use the ``LinkableEntity`` trait.
3. Creating a simple ``LinkablePopulator`` implementation.

Updating the ``PodcastChannel`` Entity
--------------------------------------

As stated above, we want to leverage the Linkable behavior for our ``PodcastChannel`` entity.
To do that, we make our class implement the ``LinkableInterface`` and use the ``LinkableEntity`` trait.

.. note::

    For the sake of brevity, we skip every information not related to the current topic.

.. code:: php

    <?php

    namespace AudienceHero\Bundle\PodcastBundle\Entity;

    use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableEntity;
    use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableInterface;

    class PodcastChannel implements LinkableInterface
    {
        use LinkableEntity;
    }

The ``LinkableEntity`` trait implements all the ``LinkableInterface`` methods in a standard way.

Creating the ``PodcastChannelPopulator``
-----------------------------------------

Now that our ``PodcastChannel`` entity is ready. We need to create our Populator. This populator
will create and store inside each entity the links you wants them to have returned.

.. code:: php

    <?php

    namespace AudienceHero\Bundle\PodcastBundle\Linkable;

    use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\LinkableInterface;
    use AudienceHero\Bundle\CoreBundle\Behavior\Linkable\Populator\LinkablePopulatorInterface;
    use AudienceHero\Bundle\PodcastBundle\Entity\PodcastChannel;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

    class PodcastChannelPopulator implements LinkablePopulatorInterface
    {
        /**
         * @var UrlGeneratorInterface
         */
        private $generator;

        public function __construct(UrlGeneratorInterface $generator)
        {
            $this->generator = $generator;
        }

        public function supports(LinkableInterface $object): bool
        {
            return $object instanceof PodcastChannel;
        }

        public function populate(LinkableInterface $object)
        {
            $object->setURL('public', $this->generator->generate('podcast_channels_listen', ['id' => $object->getId()], UrlGeneratorInterface::ABSOLUTE_URL));
        }
    }

What is happening here? The ``PodcastChannelPopulator`` implements the ``LinkablePopulatorInterface``.
This interface describes 2 methods: ``supports`` and ``populate``. The Linkable behavior, after each entity is loaded,
makes sure that they contain their respective links.

The ``supports`` method check whether the populator supports the given object. The method returns ``true`` if the given entity is supported, or ``false`` otherwise.
If the entity is not supported, the ``populate`` method will not be called.

The ``populate`` method adds the links to the entity by calling the ``setURL`` method on the object.

Accessing the links from the API response
-----------------------------------------

When fetching a Resource, the links will be available under the ``urls`` key.
In our case with the ``PodcastChannel`` Resource, the API response will look like this:

.. code:: json

    {
        "@id": "/api/podcast_channels/34eedf60-2ef5-41a8-9d62-3ba553309bd4",
        "urls": {
            "public": "https://our.domain/podcasts/34eedf60-2ef5-41a8-9d62-3ba553309bd4",
        }
    }

Next steps
----------

That's all. We don't have to do anything else. The ``PodcastChannelPopulator`` will be loaded, registered, and called automatically.
You can issue a ``GET`` request to a ``PodcastChannel`` resource and you will be able to see the links in the JSON representation.

