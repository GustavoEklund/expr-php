<?php

require_once('./vendor/autoload.php');

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Dotenv\Dotenv;

// Variáveis de ambiente
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required([
	'PRODUCTION_MODE',
	'DB_DRIVER',
	'DB_HOST',
	'DB_PORT',
	'DB_NAME',
	'DB_USER',
	'DB_CHARSET',
])->notEmpty(); // required
$dotenv->required('DB_PASSWORD');
$dotenv->required('PRODUCTION_MODE')->isInteger();
$dotenv->required('PRODUCTION_MODE')->allowedValues(['0', '1']);

$is_dev_mode = (bool) $_ENV['PRODUCTION_MODE'];
$proxy_dir = null;
$cache = null;
$use_simple_annotation_reader = false;

$config = Setup::createAnnotationMetadataConfiguration(
	[__DIR__ . '/src/Entities'],
	$is_dev_mode,
	$proxy_dir,
	$cache,
	$use_simple_annotation_reader,
); // createAnnotationMetadataConfiguration

$connection = [
	'dbname' => $_ENV['DB_NAME'],
	'user' => $_ENV['DB_USER'],
	'password' => $_ENV['DB_PASSWORD'],
	'host' => $_ENV['DB_HOST'],
	'driver' => $_ENV['DB_DRIVER'],
	'charset' => $_ENV['DB_CHARSET'],
]; // connection

Type::addType('uuid', Ramsey\Uuid\Doctrine\UuidType::class);

$entity_manager = EntityManager::create($connection, $config);
