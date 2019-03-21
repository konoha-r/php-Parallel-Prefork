<?php

use \Parallel\Prefork;

require_once __DIR__ . '/../vendor/autoload.php';

main();
exit();

function main()
{
    $pp = new Prefork(array(
        'max_workers'  => 5,
        'trap_signals' => array(
            SIGHUP  => SIGTERM,
            SIGTERM => SIGTERM,
        ),
    ));

    $n = 0;
    while ($pp->signalReceived() !== SIGTERM) {
        loadConfig();
        if ($pp->start(function($obj, $pid) use(&$n){
            $n++;
            }) ) {
            continue;
        }
        workChildren($n);
        $pp->finish();
    }
    $pp->waitAllChildren();
}

function loadConfig()
{
    echo "Load configuration\n";
}

function workChildren($n)
{
    for ($i = 1; $i <= 3; $i++) {
        $mypid = getmypid();
        echo "[{$n}][{$mypid}] Sleep $i seconds\n";
        sleep($i);
    }
}
