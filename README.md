Requirements: docker

Installation : make indocker, followed by cd personal-library && composer install && make preparecs 

If you want a pre-populated database, please comment the lines with the "markTestSkipped" in tests\FixtureTest.php and launch the tests

If you want to use the barcode scanner, please use a HTTPS url.
