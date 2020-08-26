<?php

use Dotenv\Dotenv;

// Variáveis de ambiente
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
$dotenv->required([
	'PRODUCTION_MODE',
	'INTERNAL_FOLDER',
	'TOKEN_SECRET_KEY',
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

// Definição do fuso-horário
date_default_timezone_set('America/Sao_Paulo');

// Diretório raiz host
define('DIR_PAGE', "http://{$_SERVER['HTTP_HOST']}/".$_ENV['INTERNAL_FOLDER']);

//Diretório raiz físico
(substr($_SERVER['DOCUMENT_ROOT'], -1) === '/') ? (
	define('DIR_REQ', $_SERVER['DOCUMENT_ROOT'].$_ENV['INTERNAL_FOLDER'])
) : (
	define('DIR_REQ', "{$_SERVER['DOCUMENT_ROOT']}/".$_ENV['INTERNAL_FOLDER'])
); // define

// Diretórios públicos
define('DIR_IMAGES', DIR_REQ.'../images.banco-pdb.life/');

// Configuração de exibição de erros
if (!isset($_ENV['PRODUCTION_MODE']) || (bool) $_ENV['PRODUCTION_MODE'] === true) {
	ini_set('display_errors', 0);
} else {
	ini_set('display_errors', 1);
} // else

error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', DIR_REQ.'log.txt');

// Banco de dados
define('DATABASE', [
	'sgdb' => 'mysql',
	'port' => '3306',

	'host' => '127.0.0.1',
	'name' => 'db_bank_pdb',
	'user' => 'root',
	'pass' => '',

//	'host' => 'mysql1008.mochahost.com',
//	'name' => 'essencia_db_banco_pdb',
//	'user' => 'essencia_root',
//	'pass' => 'eqpoda_123',

	'options' => [
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
		PDO::ATTR_ERRMODE => (bool) $_ENV['PRODUCTION_MODE'] ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_CASE => PDO::CASE_NATURAL
	] // options
]); // define
