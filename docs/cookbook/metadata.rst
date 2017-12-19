How to add Metadata to my entity?
=================================

Metadata is a common need when dealing with Resources. AudienceHero provides an easy-to-implement way to
add both private and public metadata to a Resource.

Private and Public Metadata
---------------------------

AudienceHero deals with two types of visibility for metadata. Either **public** or **private**. This concept is
relative to the owner of a Resource.

- Private metadata are **only** readable by the owner of a Resource. They are not serialized and sent to an unauthenticated
    client.

- Public metadata are readable by **both** owner and the general public. It is a common information, available to anyone
    requesting a Resource.

- **Both public and private** metadata are writable **only** by the Resource owner.

The metadata interfaces and traits
----------------------------------

AudienceHero provides the ``HasPrivateMetadataInterface`` and ``HasPublicMetadataInterface``. It also provides the
related traits ``HasPrivateMetadataTrait`` and ``HasPublicMetadataTrait``. These traits define a set of method you can
use to set and get metadata. It is recommended to use the corresponding trait to implements the interfaces.



Let's imagine we are creating a basic CMS, and that we want to be able to store public and private metadata to a ``Page``
resource. We will write this piece of code:

.. note::

    For the sake of brevity, we skip every information not related to the current topic.

.. code:: php

    <?php

    namespace Acme\Bundle\Entity;

    use AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPrivateMetadataInterface;
    use AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPrivateMetadataTrait;
    use AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPublicMetadataInterface;
    use AudienceHero\Bundle\CoreBundle\Behavior\Metadata\HasPublicMetadataTrait;

    class Page implements HasPrivateMetadataInterface, HasPublicMetadataInterface
    {
        use HasPrivateMetadataTrait;
        use HasPublicMetadataTrait;
    }


After describing the interfaces and using the traits, our Page class will have these methods:

.. code:: php

    public function setPrivateMetadata(array $metadata): void;
    public function getPrivateMetadata(): array;
    public function setPrivateMetadataValue(string $key, $value): void;
    public function getPrivateMetadataValue(string $key);
    public function setPublicMetadata(array $metadata): void;
    public function getPublicMetadata(): array;
    public function setPublicMetadataValue(string $key, $value): void;
    public function getPublicMetadataValue(string $key);

We can then store **private** metadata using the ``setPrivateMetadataValue`` method, and store **public** metadata using
the ``setPublicMetadataValue`` method.

.. tip::

    You will need to update your database schema as ``HasPrivateMetadataTrait`` and ``HasPublicMetadataTrait`` add
    persisted fields in your entity.

Accessing the metadata from the API response
--------------------------------------------

Given we set a ``secret_id`` private metadata with value ``1234``, and a ``acme`` public metadata with value ``foobar``,
our resource returned by the API will look like that:

.. code:: json

    {
        "@id": "/api/pages/994c67d1-eb89-496d-8bd9-47b381959198",
        "private_metadata": {
            "secret_id": 1234
        },
        "public_metadata": {
            "acme": "foobar"
        }
    }

In the case of an anonymous request, the response will look like this:

.. code:: json

    {
        "@id": "/api/pages/994c67d1-eb89-496d-8bd9-47b381959198",
        "public_metadata": {
            "acme": "foobar"
        }
    }

