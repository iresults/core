Iresults
========

Introduction
------------

The class \Iresults\Core\Iresults is a general purpose utility. It provides features like the resolution of paths, retrieval of configurations and an interface for debugging and logging - all in a framework agnostic way.


Installation
------------

### via composer

Merge the following into your composer.json

    ```json
    "repositories": [
        {
            "type": "git",
            "url": "http://git.iresults.me/Iresults.Core.git/"
        }
    ],
    "minimum-stability": "dev",
    "require": {
        "iresults/core": "*"
    }
    ```


Path resolution
---------------

### Core

Get the path to the base directory of the installation:

	\Iresults\Core\Iresults::getBasePath();

Get the base URL of the installation:

	\Iresults\Core\Iresults::getBaseURL();

Get the path to the temporary directory:

	\Iresults\Core\Iresults::getTempPath();
	

### Resources

A resource may be a simple string or a `\Iresults\FS\FilesystemInterface` object.

Get the absolute path of a resource:

	\Iresults\Core\Iresults::getPathOfResource($resource);

Get the URL of a resource:

	\Iresults\Core\Iresults::getUrlOfResource($resource);

Creating a versioned file path for a path:

If the file `/a/path/myfile.txt` already exists the method will return the path `/a/path/myfile_1.txt`.

	\Iresults\Core\Iresults::createVersionedFilePathForPath($filePath);
	
	
### Packages

Packages are collections of files (i.e. PHP classes, stylesheet, JavaScript files, etc.) grouped together. In Symfony they are called 'Components', in TYPO3 CMS 'extensions' and in TYPO3 Flow 'packages'.

Retrieve the path to a package:

    \Iresults\Core\Iresults::getPackagePath($package);
    
Retrieve the URL to a package's directory:

    \Iresults\Core\Iresults::getPackageUrl($package);
    


Debugging
---------

### pd()

The pd() function can be a very handy tool. The API documentation simply says "Dumps a given variable (or the given variables) wrapped into a 'pre' tag.", but `pd()` does a lot more.

Example usage:

    \Iresults\Core\Iresults::pd($myVariable);
    
    \Iresults\Core\Iresults::pd($myFirstVariable, $mySecondVariable);


### willDebug()

Before any of the variable will be rendered the method checks if the user (a web user, CLI user, etc.) is allowed to view the debug information. To determine this the method `willDebug()` will be called.

    \Iresults\Core\Iresults::willDebug();
    

### setDebugRenderer()

If the debug information should be rendered the defined renderer is determined. Iresults comes with 3 builtin renderers:

- Render using var_dump(): `\Iresults\Core\Iresults::RENDERER_VAR_DUMP`
- Render using var_export(): `\Iresults\Core\Iresults::RENDERER_VAR_EXPORT`
- Render using iresults debug class: `\Iresults\Core\Iresults::RENDERER_IRESULTS_DEBUG`

If the last option is chosen pd() will utilize the class `\Iresults\Core\Debug` to output the information. 
You can change the default renderer, which will be read from the configuration `debugRenderer`, through

    \Iresults\Core\Iresults::setDebugRenderer($debugRenderer);
    

### forceDebug()

In some cases you may want to enable the debug output, even if `willDebug()` would return `FALSE` (i.e. during development). To achieve this, iresults provides the method `forceDebug()`.
    
    \Iresults\Core\Iresults::forceDebug();

On the other side `forceDebug()` can be used too to disable debugging outputs:

    \Iresults\Core\Iresults::forceDebug(FALSE);
    
    
Utilities
---------

### say()

Outputs the given string and takes care about the current environment. 

	\Iresults\Core\Iresults::say($message);

In an CLI environment (i.e. if called from the command line) an additional ASCII color can be specified.

	\Iresults\Core\Iresults::say($message, \Iresults\Core\Command\ColorInterface::BOLD_RED);


### getEnvironment()

Determine if the current environment is a web request or a command line:

	\Iresults\Core\Iresults::getEnvironment();
	
The method will either return `\Iresults\Core\Iresults::ENVIRONMENT_WEB` or `\Iresults\Core\Iresults::ENVIRONMENT_CLI`.
		

### getProtocol()

Detect if the request is made via `http` or `https`:
    
    \Iresults\Core\Iresults::getProtocol();

    
### isFullRequest()

`isFullRequest()` provides a framework agnostic way to detect AJAX requests. In TYPO3 CMS for example a request with an eID is no full request.

	\Iresults\Core\Iresults::isFullRequest();


### getOutputFormat()

This method may be used to detect the requested output format, allowing you to provide the expected kind of result (i.e. JSON, XML or binary format).

	\Iresults\Core\Iresults::getOutputFormat();
	
Note: In an CLI environment the output format will always be `\Iresults\Core\Iresults::OUTPUT_FORMAT_BINARY`.


### getFramework()

This is one of the most important methods, as it allows the detection of the used framework: 

	\Iresults\Core\Iresults::getFramework();

Currently iresults will recognize 3 different frameworks:

- TYPO3 CMS: `\Iresults\Core\Iresults::FRAMEWORK_TYPO3`
- TYPO3 Flow: `\Iresults\Core\Iresults::FRAMEWORK_FLOW`
- Symfony: `\Iresults\Core\Iresults::FRAMEWORK_SYMFONY`

If none of these apply iresults is used standalone (`\Iresults\Core\Iresults::FRAMEWORK_STANDALONE`).


### getConfiguration() and setConfiguration()	
These methods are used to control the configuration of `\Iresults\Core\Iresults`.

`getConfiguration()` may be used to return the complete configuration array, or the configuration for a given key:

    $allConfiguration = \Iresults\Core\Iresults::getConfiguration();

    $defaultDebugRenderer = \Iresults\Core\Iresults::getConfiguration('debugRenderer');
    
Through `setConfiguration()` the configuration values may be overwritten:

	\Iresults\Core\Iresults::setConfiguration('debugRenderer', \Iresults\Core\Iresults::RENDERER_VAR_DUMP);
	
	
	
	