# php-ices2

If you don't already know, Xiph.org maintains a crucial set of open source audio technologies that make it possible for people to roll their own solutions, even enterprise solutions, for audio broadcasting and distribution. The Icecast2 server serves up audio and sometimes video, and the IceS2 source server provides a variety of input modules for getting the source audio into the Icecast2 server for broadcasting.

This package is my first attempt at a PHP wrapper around the IceS2 source server, so that I can implement an IceS 2 functionality in a PHP application. There is no input validation and no graphical interface. You will need to clean input values yourself, and you are free to write unsafe code if you want to!

## API

### Ices2\Ices2

    (void) clearLog()

Self-explanatory

    (void) configure(array $options)

Takes a multidimensional, associative array as input
Re-initializes the configuration with the supplied values

    (Ices2\Ices2) ::get()

Static method for getting a singleton instance of the Ices2 object

    (array) getErrors()

Returns an array of error messages, if any

    (array) getHistory($reverse = true)

Takes an optional boolean indicating the direction to sort the list. By default, the list is returned in reverse order, such that the currently playing song is the first element in the array. By passing a boolean false to the method, you can the list in the order that they were played, with the currently playing song as the last element in the array.

Returns an array of absolute paths to all song files that were played

    (array) getLog()

Returns the parsed log as an array of objects, one for each entry in the log. The entries are returned in reverse order, such that the most recent entry is the first element in the array

    (integer) getPid()

Returns the process ID of the running IceS 2 binary program, if it is in fact running

    (boolean) hasErrors()


Indicates whether any errors have been generated

    (boolean) isRunning()

Indicates whether the IceS 2 binary program is running

    (boolean) loadPreset($name)

Takes a string naming a stored configuration preset to load values from the filesystem into the current configuration

Returns a boolean indicating success or failure

    (void) next()

If the Playlist input module is used, calling this method will signal the IceS 2 binary program to skip to the next track in the list.

    (boolean) savePreset($name)

Takes a string naming the current configuration, and writing it in the /data/presets subdirectory for later retrieval

Returns a boolean indicating success or failure

    (integer|boolean) start()

Attempts to start the IceS 2 binary program

Returns an integer process ID on success, boolean false otherwise

    (void) stop()

Attempts to stop the IceS 2 binary program

    (void) updateMetadata()

If a metadata file is used by the input module, this method signals the IceS 2 binary program to reload the file data

    (string) version()

Returns the version information from the IceS 2 binary program

### Ices2\Configuration

    (string|array|mixed[][]) export($as_xml = false)

Returns the entire configuration as a nested, associative array by default

Takes an optional boolean indicating that the object should be exported as a string representing the XML document.

    (array) getErrors()

Returns an array of error messages if any, empty array otherwise

    (Presets) getPresetsManager($dir)

Takes a string path to the directory for containing configuration presets

Return the Ices2\Configuration\Presets object

    (boolean) hasErrors()

Indicates whether any errors have been generated

    (Configuration) import(array $options = [])

Takes an optional, multi-dimensional array of configuration parameter => value pairs, and uses them to populate Configuration property values

Returns itself to allow for fluent notation

    (Configuration) read($source, $format = 'xml')

Takes a string representinig either the full path to a configuration file, or the contents of a configuration file

Optionally takes a string argument specifying the file format. By default the format is 'xml', but 'json' is available.

Returns a Configuration object

    (Configuration|boolean) write($destination, $format = 'xml', $overwrite = true)

Takes a path representing the destination file

Optionally takes a string representing the file format to write. By default the format is 'xml', but 'json' is available.

Optionally takes a boolean indicating whether to overwrite an existing configuration file by the same name

Returns itself to allow for fluent notation

### Ices2\Log

    (void) clear()

Clears out all log entries

    (array) getErrors()

Returns an array of generated error messages, if any

    (boolean) hasErrors()

Indicates whether any errors have been generated

    (array|boolean) parse()

Returns the contents of the log file as an array of simple objects, each representing one entry. The log entry direction is reversed, so that the latest log entry is the first element in the array

Returns a boolean false if the log cannot be parsed

### Ices2\Process

    (boolean) isRunning()

Indicates whether the ices2 source server is running

    (boolean) start($configfile)

Takes a string path to the configuration file to use
Returns a boolean true on success, false otherwise

    (void) stop()

Stops the running ices2 source server

    (void) playlistNext()

Sends a signal to the ices2 program to skip to the next track if * the playlist input module is used

    (void) reloadMetadata()

Sends a signal to the ices2 program to reload the external * metadata file, if one is being used

## How to implement the Ices2 package

### Namespaces

Since PHP version 5.3.0, namespaces have been a way to neatly separate packages, avoid class naming collisions, and superimpose a logical order on the physical, where the class namespaces correspond to nested directories and source files.

### Class Autoloading

Namespaces, alone will not be enough, however, and the script from which you call the Ices2\Ices2 object will also need a class autoloader, which takes advantage of the fact that the logical and physical layout of the package is identical, and thus can locate the script in which a needed class is defined using the name of that class.

### A Simple Example

In the simplest possible example, imagine a single PHP script and a directory named "Ices2". The Ices2 directory contains this package:

    ..
    Ices2/
    index.php

The index.php script must contain the following code at the top:

    <?php 
    // custom class autoloading function
    spl_autoload_register(function ($class) {
        $class = str_replace("\\", "/", $class);
        include $class . '.php';
    });

    // require the main class
    require_once('Ices2/Ices2.php');


### Get an instance of Ices2\Ices2

Ices2\Ices2 is a hybrid static class, which is a way to get an instance that is a guaranteed singleton because the instance is initialized once, when fetched through a static method for the first time, and then stored in a static property. Subsequent calls for an instance will return that static instance.

    $ices2 = Ices2\Ices2::get();
