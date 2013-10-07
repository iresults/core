Locale\Environment
==================

Introduction
------------

The class `\Iresults\Core\Locale\Environment` is the core of iresults' localization mechanism. It is used to define and retrieve the current locale.

Usage
-----

Get the singleton instance:

	\Iresults\Core\Locale\Environment::getSharedInstance()
	
Retrieve the current locale:

	\Iresults\Core\Locale\Environment::getSharedInstance()->getLocale();
	
Set the current locale:
	
	\Iresults\Core\Locale\Environment::getSharedInstance()->setLocale('de_DE.UTF-8');

