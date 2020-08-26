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
