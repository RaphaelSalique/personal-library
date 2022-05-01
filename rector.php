<?php

declare(strict_types=1);

use Rector\Symfony\Set\SymfonySetList;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->symfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml');

    $rectorConfig->phpVersion(PhpVersion::PHP_81);

    $rectorConfig->sets([
      SymfonySetList::SYMFONY_60,
      SymfonySetList::SYMFONY_CODE_QUALITY,
      SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES
    ]);
};
