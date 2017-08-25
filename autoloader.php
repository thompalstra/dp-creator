<?php
spl_autoload_register(function ($name) {
    $p = __DIR__ . DIRECTORY_SEPARATOR . $name . '.php';
    if(file_exists($p)){
        include_once($p);
    } else {
        echo "Want to load $name.\n";
        throw new Exception("Unable to load $name.");
    }

});

?>
