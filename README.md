iresults core
=============

The iresults core package provides fundamental functionality and classes.

The features include:

- Mutable objects (Iresults\Core\Mutable) which read data from different input formats (XML, CSV, YAML,...)
- Key value coding (Iresults\Core\KVCInterface)
- Resolution of property key paths (Iresults\Core\Model)
- Debugging (Iresults\Core\Debug)
- Profiling (Iresults\Core\Profiler)
- Locks (Iresults\Core\System\Lock)
- Tree based data structures (Iresults\Core\Model\DataTree)
- Cache abstraction (Iresults\Core\Cache)
- and many more...


Installation
------------

Merge the following into your composer JSON file:

```json
  "require": {
    "iresults/core": "~3.2"
  },
  "repositories": [
    {
      "type": "git",
      "url": "https://git.iresults.li/git/iresults/core.git"
    }
  ],
```
