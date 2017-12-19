Terminology
===========

Feeling unsure about what we mean with some words? You will find an answer here.

Back-Office
    The Back-Office is where a user can create/update/delete their resources. The Back-Office is protected by a login/password.

Extension
    AudienceHero is a `modular system <https://en.wikipedia.org/wiki/Modular_design>`_. An extension is a tight set of features,
    coupled with some part of AudienceHero, designed to be installed easily. The ``ContactBundle`` and ``FileBundle`` are
    extensions, depending on the ``CoreBundle``. The ``AcquisitionFreeDownloadBundle`` is an extension with a dependency
    on ``ContactBundle``, ``FileBundle`` and the ``CoreBundle``.

Front-Office
    The Front-Office is what the public sees and interacts with. It's the public facing part of any AudienceHero system.

Owner
    AudienceHero is a `multi-tenant system <https://en.wikipedia.org/wiki/Multitenancy>`_. It means that multiple users
    share the same database. A resource created within AudienceHero is always attached to the user whom created it. If
    Alice creates a new ``Contact`` resource, we say that Alice is the owner of this resource.

Resource
    A resource is a record in the database. Every resource must be owned by an Owner. A resource can usually be
    accessed through the API, though it's not mandatory.
