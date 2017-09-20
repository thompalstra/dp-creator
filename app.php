<?php


class App{
    public function init(){
        include(__DIR__ . DIRECTORY_SEPARATOR . 'autoloader.php');

    }
    public function run(){
        $this->init();
        session_start();

    }
}

 ?>
