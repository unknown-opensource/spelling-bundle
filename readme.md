Features
======================
This bundle provides you with:

- Ability to track typographical errors on page

Issues and feature requests are tracked in the Github issue tracker https://github.com/unknown-opensource/spelling-bundle/issues


Installation
======================

1. To install this bundle with Composer, just add the following to your composer.json file:


    require: {
        ...
        "unknown/spelling-bundle": "1.0.1"
    }


2. Then register the bundle in AppKernel::registerBundles()


    $bundles = array(
        ...
        new Unknown\Bundle\SpellingBundle\UnknownSpellingBundle(),
    );


Custom dictionary
======================

If your project contains specific vocabulary feel free to add needed words to:

    app/dictionary-en.txt
    app/dictionary-lt.txt

Result
======================

Whenever you see Symfony2 toolbar an extra toolbar block will appear reporting on typographical errors found in page.

Good luck!
