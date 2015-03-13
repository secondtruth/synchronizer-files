Contributing Guidelines
=======================

* Please check your code for typos and spelling mistakes before committing!

* Always document your code, at least with the most important information.

* If you introduce a significant code change, always run the tests.


Coding Standard
---------------

* We use the [PSR-2 coding style][1] but without the line length limit.


Running tests
-------------

1. Copy the file `phpunit.xml.dist` to `phpunit.xml`

2. Then run the following commands:

    $ cd path/to/FilesSynchronizer/
    $ composer install
    $ phpunit


  [1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md