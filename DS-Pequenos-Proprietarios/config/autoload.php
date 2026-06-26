<?php
spl_autoload_register(function($nome) {
    $paths = ["controllers", "models", "dao", "lib"];

    foreach($paths as $path) {
        $file = dirname(__FILE__) . "/../{$path}/class.{$nome}.php";

        if (file_exists($file)) {
            require_once $file;
            break;
        }
    }
});
?>