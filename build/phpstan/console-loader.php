<?php

declare(strict_types = 1);

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;

require_once dirname(__DIR__).'/../config/bootstrap.php'; // NOSONAR
$kernel = new Kernel($_SERVER['APP_ENV'], false);

return new Application($kernel);
