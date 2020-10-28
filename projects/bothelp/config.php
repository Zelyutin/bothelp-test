<?php
$config = [
	"accounts_count" => 1000,
	"events_limit" => 10000,
	"min_pack_size" => 1,
	"max_pack_size" => 10,
	"endless_events_execution" => false,
	"redis" => [
        "scheme" => "tcp",
        "host" => "redis",
        "port" => 6379
    ],
];
?>