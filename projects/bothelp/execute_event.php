<?php
require "config.php";
require "predis/autoload.php";
Predis\Autoloader::register();

set_time_limit(0);
executeEvent($config);

function executeEvent($config)
{
    $redis = new Predis\Client($config['redis']);

    $accounts = $redis->hgetall("accounts"); // getting all available accounts
    $accountsIds = array_keys($accounts);
    
    while($accountId = array_pop($accountsIds))
    {
        if($redis->hsetnx("accounts_mutex", $accountId, 1)) // mutex-like value for preventing simultanious access to same account's events
        {
            $eventId = $redis->lpop("events[{$accountId}]"); // taking first event for current account
            if($eventId)
            {
                processEvent($accountId, $eventId);
            }
            
            $redis->hdel("accounts_mutex", $accountId); // removing blocking mutex
            
            if(!$redis->llen("events[{$accountId}]")) // removing account from listings if there's no event for it
            {
                $redis->hdel("accounts", $accountId);
            }

            break;
        }
    }
}
function processEvent($accountId, $eventId)
{
    sleep(1);
    logEvent($accountId, $eventId);
}
function logEvent($accountId, $eventId)
{
    $time = date("d.m.Y H:i:s");
    file_put_contents(__DIR__."/main.log", "[{$time}] Executed event {$eventId} for account {$accountId}".PHP_EOL, FILE_APPEND);
}