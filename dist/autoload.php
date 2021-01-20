<?php
/**
 * A PSR-4 conformant class autoloader.
 * 
 * This is a modified version of the PHP-FIG PSR-4 autoloader example from
 * https://www.php-fig.org/psr/psr-4/examples/
 *
 * @param string $FQCN The fully-qualified class name.
 * @return void
 */
spl_autoload_register(function (string $FQCN): void {
    $basePath = __DIR__."/src/";

    $filePath = $basePath.str_replace("\\", "/", $FQCN).".php";

    // if the file exists, require it
    if (file_exists($filePath)) {
        require $filePath;
    }
});
