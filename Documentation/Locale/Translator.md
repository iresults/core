Locale\Translator
=================

Introduction
------------

The iresults localization mechanism is based on the concept of a Translator utilizing different Translation Providers as "Backends" for translated messages. The factory class \Iresults\Core\Locale\TranslatorFactory provides functions to create a Translator instance in different ways:

### Translator with package
Let the Translator Factory look for a supported file in the given path.
	
	\Iresults\Core\Locale\TranslatorFactory::translatorWithSourcePath('path/to/supported/files/');


### Translator with package
Let the Translator Factory look for a supported file in the given packages resource path (`PackageDir/Resources/Private/Language/`).
	
	\Iresults\Core\Locale\TranslatorFactory::translatorWithPackage('MyPackage');


Binding to locale
-----------------

A Translator (and it's Translation Providers) may be bound to a specific locale. A bound Translator will not depend on the locale environment anymore. It will always translate messages to the bound locale.

	$translator->bindToLocale('de_DE.UTF-8')
