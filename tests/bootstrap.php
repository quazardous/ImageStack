<?php

if (!is_file(__DIR__ . '/config.php')) {
    throw new \RuntimeException('Please copy and adapt the file tests/config-dist.php to tests/config.php !');
}

include_once __DIR__ . '/config.php';

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = include_once __DIR__ . '/../vendor/autoload.php';

// Execute the command and store the process ID
$output = array();
$command = sprintf(
    'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!',
    WEB_SERVER_HOST,
    WEB_SERVER_PORT,
    WEB_SERVER_DOCROOT
);

exec($command, $output);
$pid = (int) $output[0];

echo sprintf(
    '%s - Web server started on %s:%d with PID %d (%s) !', 
    date('r'),
    WEB_SERVER_HOST, 
    WEB_SERVER_PORT, 
    $pid,
    WEB_SERVER_DOCROOT
) . PHP_EOL;

echo 'Waiting 0.5 second for server to land...' . PHP_EOL;
usleep(500);
 
// Kill the web server when the process ends
register_shutdown_function(function() use ($pid) {
    echo sprintf('%s - Killing process with ID %d', date('r'), $pid) . PHP_EOL;
    exec('kill ' . $pid);
});
 