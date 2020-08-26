<?php

namespace Classes;

use PDO;
use Exception;
use Traversable;
use ArrayIterator;
use JsonException;
use RuntimeException;
use IteratorAggregate;

/**
 * Class ActiveRecord
 * @package Models
 */
abstract class ActiveRecord implements IteratorAggregate
{
	/** @var array $content */
	private $content;

	/** @var string $table */
	protected $table;

	/** @var string $id_field */
	protected $id_field;

	/** @var bool $force_id_insert */
	protected $force_id_insert = false;

	/** @var bool $log_timestamp */
	protected $log_timestamp = true;

    /** ActiveRecord constructor. */
    public function __construct()
    {
        // Se a marca de tempo não for informada
        if (!is_bool($this->log_timestamp)) {
            // Define a marca de tempo padrão como verdadeiro
            $this->log_timestamp = true;
        } // if

        // Se a tabela não for informada
        if ($this->table === null) {
            // Define a tabela padrão como o nome da classe
            $thisClass = explode('\\', get_class($this));
            $this->table = strtolower(end($thisClass));
        } // if

        // Se o a chave primária não for informada
        if ($this->id_field === null) {
            // Define a chave primária como 'id'
            $this->id_field = 'id';
        } // if
    } // __construct

    /**
     * Método para definir um parâmetro e um valor ao parâmetro em $content
     * @param $parameter
     * @param $value
     */
    public function __set($parameter, $value)
    {
        $this->content[$parameter] = $value;
    } // __set

    /**
     * Método para obeter o valor de um parâmetro de $content.
     * @param $parameter
     * @return mixed
     */
    public function __get($parameter)
    {
        return $this->content[$parameter];
    } // __get

    /**
     * Método para verificação de um parâmetro em $content.
     * @param $parameter
     * @return bool
     */
    public function __isset($parameter): bool
    {
        return isset($this->content[$parameter]);
    } // __isset

    /**
     * Método para remover um parâmetro de $content.
     * @param $parameter
     * @return bool
     */
    public function __unset($parameter): bool
    {
        // Se o parâmetro existir
        if (isset($parameter)) {
            unset($this->content[$parameter]);
            return true;
        } // if

        return false;
    } // _unset

    /**
     * Ao clonar o objeto, não permitir que o id seja clonado também.
     */
    public function __clone()
    {
        // se existir conteúdo com id
        if (isset($this->content[$this->id_field])) {
            unset($this->content[$this->id_field]);
        } // if
    } // __clone

    /**
     * Método para obter o conteúdo de $content em forma de vetor.
     * @return array
     */
    public function toArray(): array
    {
    	if (!$this->content) {
    		return [];
		} //if

        return $this->content;
    } // toArray

    /**
     * Método para sobrescrever $content com conteúdo em formato de vetor.
     * @param array $array
     */
    public function fromArray(array $array): void
    {
        $this->content = $array;
    } // fromArray

    /**
     * Método para obter o conteúdo de $content em formato JSON.
     * @return string
     * @throws JsonException
     */
    public function toJson(): string
    {
        return json_encode($this->content, JSON_THROW_ON_ERROR);
    } // toJson

    /**
     * Método para adicionar contrúdo em formato JSON em $content.
     * @param string $json
     * @throws JsonException
     */
    public function fromJson(string $json): void
    {
        $this->content = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    } // fromJson

	/**
	 * Método que faz com que esta classe se comporte como um objeto iterável, herdando o comportamento de
	 * IteratorAggregate
	 * @return ArrayIterator|Traversable
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->content);
	} // getIterator

    /**
     * Método para impedir um atributo de possuir valor nulo, no momento de se concatenar a string para gerar a query,
     * impedir de fazer algo como $concatenar = “string” . null, ou seja, concatenar uma string com um valor nulo, ou
     * concaternar uma string com um valor booleano. Se a string possuir valoes ecapáveis, tratar devidamente.
     * @param $value
     * @return string
     */
    private static function format($value): string
    {
        // Se o parâmetro conter texto e não estiver vazio
        if (is_string($value) && !empty($value)) {
            // Colocando aspas no valor para evitar concatenação com valor nulo
            return "'" . addslashes($value) . "'";
        } // if

        // Se o parâmetro conter um valor booleano
        if (is_bool($value)) {
            // Transformando valor primitivo booleano em texto
            return $value ? 'true' : 'false';
        } // if

        // Se o valor for um texto ou número que não está vazio
        if ($value !== '') {
            // Apenas retornando o valor
            return (string) $value;
        } // if

        // Em algum caso adverso, retorne o texto nulo
        return 'null';
    } // format

    /**
     * Método para percorrer o atributo $content e verificar com a função is_scalar() se o valor contido para cada
     * elemento é válido, contendo um dos modos primitivos (integer, float, string ou boolean).
     *
     * Se possuir os tipos não escalares (array, object e resource), ignora. Sendo dados escalares, este método irá
     * consumir o método format() para ajustar ao padrão que pode ser concatenado em uma string.
     *
     * @return array
     */
    public function convertContent(): array
    {
        $new_content = [];

        foreach ($this->content as $key => $value) {
            // Se for um tipo primitivo, atribua o valor, se for um vetor, objeto ou resource adicione 'null'
            if (is_scalar($value)) {
                $new_content[$key] = self::format($value);
            } else {
            	$new_content[$key] = 'null';
			} // else
        } // foreach

        return $new_content;
    } // convertContent

    /**
     * Método para transformar as colunas informadas em uma string para ser usado como parâmetro de busca.
     * @param array $columns
     * @param string $table
     * @return string
     */
    private static function arrayToColumns(array $columns, string $table): string
    {
        if (count($columns) < 1) {
            return '*';
        } // if

		// Transformar cada item do vetor e formatar devidamente
        return implode(', ', array_map(static function ($column) use ($table) {
            if ($column[0] === '`') {
                return  $column;
            } // if

            return "`{$table[4]}`.`{$column}`";
        }, $columns));
    } // arrayToColums

	/**
	 * Método para inserir um registro no banco de dados.
	 * Prmeiro converte e filtra os atributos do objeto e depois monta a declaração apropriada.
	 *
	 * @param bool $throw_error Default = false
	 * @return bool
	 * @throws Exception
	 */
    public function create(bool $throw_error = false): bool
    {
        // Formata e converte o conteúdo
        $new_content = $this->convertContent();

        // Se a marca de tempo estiver habilitada, registre o evento de criação e atualização
        if ($this->log_timestamp === true) {
            // Alterando o padrão de horário
            date_default_timezone_set('America/Sao_Paulo');

            $new_content['created_at'] = '\''.date('Y-m-d H:i:s').'\'';
            $new_content['updated_at'] = '\''.date('Y-m-d H:i:s').'\'';
        } // if

        $stmt = 'INSERT INTO '.DATABASE['name'].".`{$this->table}` ";
        $stmt .= '('.implode(', ', array_keys($new_content)).')';
        $stmt .= ' VALUES ('.implode(', ', $new_content).')';
        $stmt .= ';';

		if (Database::getInstance()->exec($stmt) < 1) {
			if ($throw_error) {
				throw new RuntimeException('Não foi fossível registrar os dados!');
			} // if

			return false;
		} // if

		return true;
    } // create

	/**
	 * Atualiza apenas os valores definidos nos atributos desta classe, do registro informado no atributo id.
	 * @param bool $throw_error Default = false
	 * @return bool
	 * @throws Exception
	 */
    public function update(bool $throw_error = false): bool
    {
        // Formata e converte o conteúdo
        $new_content = $this->convertContent();

        // Se o id não for informado
        if (!isset($this->content[$this->id_field])) {
			throw new RuntimeException('Identificador não informado!', 500);
		} // if

		// Parâmetros a serem utilizados em SET
		$sets = [];

		// Para cada parâmetro do vetor
		foreach ($new_content as $key => $value) {
			// Se o parâmetro for o id, retorne para foreach
			if ($key === $this->id_field) {
				continue;
			} // if

			// Se não, coloque o parâmetro na declaração SET
			$sets[] = '`' . $this->table[3] . "`.`{$key}` = {$value}";
		} // foreach

		// Se a marca de tempo estiver habilitada, registre o evento de atualização
		if ($this->log_timestamp === true) {
			// Alterando o padrão de horário
			date_default_timezone_set('America/Sao_Paulo');

			$sets[] = "`{$this->table[3]}`.`updated_at` = '".date('Y-m-d H:i:s').'\'';
		} // if

		// Criando uma declaração de atualização
		$stmt = 'UPDATE '.DATABASE['name'].".`{$this->table}` `{$this->table[3]}`";
		$stmt .= ' SET '.implode(', ', $sets);
		$stmt .= " WHERE `{$this->table[3]}`.`{$this->id_field}` = {$this->content[$this->id_field]}";
		$stmt .= ';';

		$connection = Database::getInstance();

		if ($connection->exec($stmt) < 1) {
			if ($throw_error) {
				throw new RuntimeException('Nenhum registro foi atualizado!');
			} // if

			return false;
		} // if

		return true;
    } // update

    /**
     * Método para fazer a pesquisa de um registro no banco de dados de acordo com um parâmetro definido.
     * @param string $table_columm
     * @param $value
     * @param array $columns
     * @return array|null
     * @throws Exception
     */
    public static function find(string $table_columm, $value, array $columns = []): ?array
    {
        // Recebe a classe que chamou o método
        $class = static::class;
        // Usando o resultado para gerar instancias somente em tempo de execução, a fim de obter os parâmetros.
        $table = (new $class())->table;
        $select = self::arrayToColumns($columns, $table);
        $formatted_value = self::format($value);

        // Gerando a declaração de seleção
        $stmt = "SELECT {$select} FROM ".DATABASE['name'].'.`'.($table ?? strtolower($class))."` `{$table[4]}`";
        $stmt .= " WHERE `{$table[4]}`.`{$table_columm}` = {$formatted_value};";

        $response = Database::getInstance()->query($stmt);

        if (!$response) {
			return null;
        } // if

        // Se nenhum registro for encontrado
        if ($response->rowCount() < 1) {
            return null;
        } // if

        return $response->fetch(PDO::FETCH_ASSOC);
    } // find

	/**
	 * Deleta um registro do banco de dados de acordo com o id informado.
	 * @param bool $throw_error Padrão = false
	 * @return bool
	 * @throws Exception
	 */
    public function delete(bool $throw_error = false): bool
    {
        // Se o id do registro a ser deletado for informado
        if (!isset($this->content[$this->id_field])) {
			throw new RuntimeException('Identificador não informado!');
		} // if

		// Criando uma declaração para apagar um registro
		$stmt = 'DELETE FROM '.DATABASE['name'].".`{$this->table}` `{$this->table[3]}` WHERE";
		$stmt .= " `{$this->table[3]}`.`{$this->id_field}` = {$this->content[$this->id_field]};";

		if (Database::getInstance()->exec($stmt) < 1) {
			if ($throw_error) {
				throw new RuntimeException('Nenhum registro foi removido!');
			} // if

			return false;
		} // if

		return true;
    } // delete

    /**
     * Método para obter todos os registros de uma tabela do banco de dados de acordo com os filtros especificados.
     * @param string $filter
     * @param array $columns
     * @param int $limit
     * @param int $offset
     * @param int $fetch_style
     * @return array|null
     * @throws Exception
     */
    public static function selectAll(
        string $filter = '',
        array $columns = [],
        int $limit = 0,
        int $offset = 0,
        int $fetch_style = PDO::FETCH_ASSOC
    ): ?array {
        // Recebe a classe que chamou o método
        $class = static::class;

        // Usando o resultado para gerar instancias somente em tempo de execução, a fim de obter os parâmetros.
        $table = (new $class())->table;

        $select = self::arrayToColumns($columns, $table);

        // $table === null ? strtolower($class) : $table //// == //// $table ?? strtolower($class)
        // Criando uma declaração de seleção com filtro especificado nos parâmetros
        $stmt = "SELECT {$select} FROM ".DATABASE['name'].'.`'.($table ?? strtolower($class))."` `{$table[4]}` ";
        $stmt .= $filter;
        $stmt .= ($limit > 0) ? " LIMIT {$limit}" : '';
        $stmt .= ($offset > 0) ? " OFFSET {$offset}" : '';
        $stmt .= ';';

        $result = Database::getInstance()->query($stmt);

        // Se algum registro for encontrado
		if ($result->rowCount() < 1) {
			return null;
		} // if

		return $result->fetchAll($fetch_style);
    } // selectAll
} // ActiveRecord
