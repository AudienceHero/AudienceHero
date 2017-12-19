Core Concepts
=============

The architecture of AudienceHero uses different paradigms that you have to learn when you want to develop an extension.
By nature, AudienceHero is multi-tenant. It means that from a single installation, the software can handle multiple
user accounts. There user accounts do not share any data.

AudienceHero's architecture is organized around a Core that provides all the basic features that an extension should
need for basic features. Other extensions (such as the ActivityBundle) provide advanced features.

.. toctree::
   :maxdepth: 1

   mailer
   textstore

Behaviors
---------

Behaviors are provided by the Core. They are a set of building blocks you can use to add features to your own entities,
without reinventing the wheel. Behaviors are usually a set of interface and traits, with sometimes added tools.

.. toctree::
   :maxdepth: 2

   behaviors/hassubjects
   behaviors/identifiable
   behaviors/linkable
   behaviors/metadata
   behaviors/ownable
   behaviors/publishable
   behaviors/referenceable
   behaviors/timestampable
