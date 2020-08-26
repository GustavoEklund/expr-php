<?php

namespace Classes;

use stdClass;
use JsonException;

/**
 * Class Request
 * @package Classes
 */
final class Request
{
    /**
     * Contém o conteúdo do corpo da requisição. O padrão é um objeto vazio.
     * @var array $body
     */
    private $body;

    /**
     * Contém o host enviado pelo cabeçalho Host HTTP.
     * @var string $port
     */
    private $port;

    /**
     * Contém o endereço ip remoto da requisição.
     * @var string $ip
     */
    private $ip;

    /**
     * Contém uma string correspondente ao método HTTP da requisição: GET, POST, PUT, etc.
     * @var string $method
     */
    private $method;

    /**
     * Esta propriedade é um vetor contendo parâmetros de rota.
     * @var array $params
     */
    private $params;

    /**
     * Contém o protocolo da requisição: http ou (requisições TLS) https.
     * @var string $protocol
     */
    private $protocol;

    /**
     * Esta propriedade é um objeto contendo uma propriedade para cada parâmetro de busca.
     * @var array $query
     */
    private $query;

    /**
     * Contém a rota chamada na requisição.
     * @var $string $route
     */
    private $route;

    /**
     * Request constructor.
     * @throws JsonException
     */
    public function __construct()
    {
    	$php_input = file_get_contents('php://input');
		$request_url = explode('/', filter_var((string)@$_GET['url'], FILTER_SANITIZE_URL));

		if ($php_input === '') {
			$post = $_POST;
		} else {
			$post = array_merge($_POST, json_decode($php_input, true, 512, JSON_THROW_ON_ERROR));
		} // if

		$this->setBody($post);
		$this->setPort((string)@$_SERVER['REMOTE_PORT']);
		$this->setIp((string)@$_SERVER['REMOTE_ADDR']);
		$this->setMethod((string)@$_SERVER['REQUEST_METHOD']);
        $this->setRoute((string)@$_GET['url']);
		$this->setParams((array)$request_url);
        $this->setProtocol((string)@$_SERVER['REQUEST_SCHEME']);
        unset($_REQUEST['url']);
        $this->setQuery($_REQUEST);
    } // __construct

    /**
     * Retorna o campo especificado do cabeçalho enviado na requisição HTTP.
     * @return mixed
     */
    public function getHeader()
    {
        return '';
    } // getHeader

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @param array $body
     */
    public function setBody(array $body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * @param string $port
     */
    public function setPort(string $port): void
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod($method): void
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * @param string $protocol
     */
    public function setProtocol(string $protocol): void
    {
        $this->protocol = $protocol;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @param array $query
     */
    public function setQuery(array $query): void
    {
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute(string $route): void
    {
        $this->route = $route;
    }
} // Request
