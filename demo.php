<?php
// only terminal
if (php_sapi_name() !== 'cli') {
    die('Non CLI');
};

require_once(dirname(__FILE__) . '/progress.class.php');

$tasks = 5;
$progress = new Progress("progress bar demo", $tasks);

for ($i = 0; $i < $tasks; $i++) {
    // do some task - start
    sleep(1);
    // do some task - end

    $progress->increase();
}

$progress->end();