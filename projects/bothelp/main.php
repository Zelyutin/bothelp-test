<?php
require "config.php";
require "predis/autoload.php";
Predis\Autoloader::register();

set_time_limit(0);
fillQueue();
executeEvents();

function fillQueue()
{
    exec("php ".__DIR__."/fill_queue.php");
}
function executeEvents()
{
    exec("php ".__DIR__."/execute_events.php");
}