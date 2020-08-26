<?php

namespace Classes;

use PDO;
use Exception;
use RuntimeException;

/**
 * Class Database
 * @package Models
 */
final class Database
{
	/** @var PDO $connection */
    private static $connection;

	/**
     * Singleton: Método construtor privado para impedir classe de gerar instâncias.
     * Database Constructor.
     */
    private function __construct()
    {}

	private function __clone()
	{}

	/**
     * Método retorno da insância estática de conexão com o banco.
     * @return PDO
     * @throws Exception
     */
    public static function getInstance(): PDO
    {
        if (self::$connection === null) {
            self::$connection = self::dsn();
		} // if

        return self::$connection;
    } // getInstance

    /**
     * Médodo de criação dinâmica do dns correto para cada banco de dados
     * @return PDO
     * @throws Exception
	 * @throws RuntimeException
     */
    private static function dsn(): PDO
    {
        // Configuração do banco de dados
        $sgdb = DATABASE['sgdb'] ?? NULL;
        $host = DATABASE['host'] ?? NULL;
        $port = DATABASE['port'] ?? NULL;
        $name = DATABASE['name'] ?? NULL;
        $user = DATABASE['user'] ?? NULL;
        $pass = DATABASE['pass'] ?? NULL;
        $options = DATABASE['options'] ?? NULL;

        // Se o parâmetro "sgdb" for informado
        if ($sgdb === null) {
			throw new RuntimeException('Tipo de banco de dados não informado!', 500);
		} // if

		// Seleciona o banco e cria string de conexão de acordo com o sistema gerenciador de banco de dados
		switch (strtoupper($sgdb)) {
			case 'MYSQL':
				$port = $port ?? 3306;
				return new PDO("mysql:host={$host};port={$port};dbname={$name}", $user, $pass, $options); break;
			case 'MSSQL':
				$port = $port ?? 1433;
				return new PDO("mssql:host={$host},{$port};dbname={$name}", $user, $pass, $options); break;
			case 'PGSQL':
				$port = $port ?? 5432;
				return new PDO("pgsql:dbname={$name};user={$user};password={$pass},host={$host};port={$port}"); break;
			case 'SQLITE': return new PDO("sqlite:{$name}"); break;
			case 'OCI8': return new PDO("oci:dbname={$name}", $user, $pass); break;
			case 'FIREBIRD': return new PDO("firebird:dbname={$name}", $user, $pass); break;
			default: throw new RuntimeException('Tipo de banco de dados inválido!', 500);
		} // switch
    } // dsn
} // Database
