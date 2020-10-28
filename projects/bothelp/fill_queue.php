<?php
require "config.php";
require "predis/autoload.php";
Predis\Autoloader::register();

generateEvents($config);

function generateEvents($config)
{
    $redis = new Predis\Client($config['redis']);

    $currentEventId = 1;

    while(true) // generating events within boundaries set
    {
        for($i = 1; $i <= $config['accounts_count']; $i++)
        {
            $packSize = rand($config['min_pack_size'], $config['max_pack_size']);
            for($j = 0; $j < $packSize; $j++)
            {
                $accountId = $i;

                $redis->rpush("events[{$accountId}]", $currentEventId);
                $redis->hset("accounts", $accountId, true);

                $currentEventId++;

                if($currentEventId > $config['events_limit']) return;
            }
        }
    }
}
function resetAll($redis)
{
    $redis->flushall();
    file_put_contents(__DIR__."/main.log","");
}