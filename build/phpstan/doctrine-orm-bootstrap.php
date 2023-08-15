<?php
declare(strict_types = 1);

use App\Kernel;

require_once dirname(__DIR__).'/../config/bootstrap.php'; //NOSONAR
$kernel = new Kernel($_SERVER['APP_ENV'], false);
$kernel->boot();

return $kernel->getContainer()->get('doctrine')->getManager();
