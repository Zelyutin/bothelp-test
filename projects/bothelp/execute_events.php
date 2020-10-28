<?php
require "config.php";
require "predis/autoload.php";
Predis\Autoloader::register();

set_time_limit(0);

executeEvents($config);

function executeEvents($config)
{
    $redis = new Predis\Client($config['redis']);

    while(true)
    {
        $maxExecutions = $redis->hlen("accounts");
        if(!$config['endless_events_execution'] && !$maxExecutions) return;

        for($i = 0; $i < $maxExecutions; $i++)
        {
            executeBackgroundEvent();
        }
        usleep(50);
    }
}
function executeBackgroundEvent()
{
    $cmd = "php ".__DIR__."/execute_event.php";
    if(substr(php_uname(), 0, 7) == "Windows")
    {
        pclose(popen("start /B ".$cmd, "r")); 
    }
    else
    {
        exec($cmd." > /dev/null &");  
    }
}