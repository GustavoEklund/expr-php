<?php

require_once('./bootstrap.php');

use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\DependencyFactory;

$migrations_config = new PhpFile('migrations.php');

$entity_factory = DependencyFactory::fromEntityManager($migrations_config, new ExistingEntityManager($entity_manager));
return ConsoleRunner::createHelperSet($entity_manager);
