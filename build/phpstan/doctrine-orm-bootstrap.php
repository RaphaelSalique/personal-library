<?php
declare(strict_types = 1);

use App\Kernel;

require dirname(__DIR__).'/../config/bootstrap.php';
$kernel = new Kernel($_SERVER['APP_ENV'], false);
$kernel->boot();

return $kernel->getContainer()->get('doctrine')->getManager();
