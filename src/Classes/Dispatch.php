<?php

namespace Classes;

use stdClass;
use Exception;
use JsonException;
use PDOException;
use TypeError;

/**
 * Class Dispatch
 * @package Classes
 */
final class Dispatch
{
	/**
	 * Parâmetros a serem usados na chamada do método.
	 * @var array $params
	 */
	private $params = [];

	/**
	 * Métodos passados na url.
	 * @var string $method
	 */
	private $method;

	/**
     * Controlador a ser instanciado
     * @var stdClass $controller
     */
    private $controller;

	/**
	 * Dispatch constructor.
	 * Chama o Controller requisitado na url
	 *
	 * @param string $controller
	 * @param string $function
	 * @param array $params
	 * @param string $next_controller
	 * @param string $next_function
	 */
	public function __construct(
		string $controller,
		string $function,
		array $params = [],
		string $next_controller = '',
		string $next_function = ''
	) {
		try {
			$request = new Request();
			$response = new Response();

			// Sobrescrever os parâmetros com as configurações da rota
			$request->setParams($params);

			// Se existir um middleware
			if ($next_controller !== '' && $next_function !== '') {
				$middleware = null;
				$this->setController($controller);
				$this->setMethod($function);

				// Execute o método contido no Controller ([Controller, método], [parâmetros do método])
				$middleware = call_user_func([$this->getController(), $this->getMethod()], $request, $response);

				if ($middleware) {
					$this->setController($next_controller);
					$this->setMethod($next_function);

					// Execute o método contido no Controller ([Controller, método], [parâmetros do método])
					echo call_user_func([$this->getController(), $this->getMethod()], $request, $response, $middleware);
				} // if

				exit();
			} // if

			$this->setController($controller);
			$this->setMethod($function);

			// Execute o método contido no Controller ([Controller, método], [parâmetros do método])
			echo call_user_func([$this->getController(), $this->getMethod()], $request, $response);
			exit();
		} catch (PDOException $pdo_exception) {
			// http_response_code(503);

			try {
				if ((bool) $_ENV['PRODUCTION_MODE']) {
					$error_message = "Erro código [{$pdo_exception->getCode()} - {$pdo_exception->getLine()} - {$pdo_exception}]";
				} else {
					$error_message = "Erro código [{$pdo_exception->getCode()} - {$pdo_exception->getLine()}]";
				} // else

				error_log(
					date('[Y-m-d H:i:s] ').$pdo_exception."\n\n\n",
					3,
					DIR_REQ.'log.txt'
				); // error_log

				echo json_encode([
					'error' => [
						'code' => $pdo_exception->getCode(),
						'message' => $error_message
					], // error
					'data' => null
				], JSON_THROW_ON_ERROR); // echo

				exit();
			} catch (JsonException $json_exception) {
				// http_response_code(500);
				echo "{\"error\":{\"code\":500,\"message\":\"{$json_exception->getMessage()}\"},\"data\":null}";
				exit();
			} catch (Exception $exception) {
				// http_response_code(500);
				echo "{\"error\":{\"code\":500,\"message\":\"{$exception->getMessage()}\"},\"data\":null}";
				exit();
			} // catch
		} catch (Exception $exception) {
			// if ($exception->getCode()) {
			//     http_response_code($exception->getCode());
			// } else {
			//    http_response_code(501);
			// } // else

			try {
				if ((bool) $_ENV['PRODUCTION_MODE']) {
					$error_message = json_encode($exception, JSON_THROW_ON_ERROR);
				} else {
					$error_message = $exception->getMessage();
				} // else

				error_log(
					date('[Y-m-d H:i:s] ').$exception."\n\n\n",
					3,
					DIR_REQ.'log.txt'
				); // error_log

				echo json_encode([
					'error' => [
						'code' => $exception->getCode(),// http_response_code(),
						'message' => $error_message,
					], // error
					'data' => null
				], JSON_THROW_ON_ERROR); // echo

				exit();
			} catch (JsonException $json_exception) {
				// http_response_code(500);
				echo "{\"error\":{\"code\":500,\"message\":\"{$json_exception->getMessage()}\"},\"data\":null}";
				exit();
			} catch (Exception $exception) {
				// http_response_code(500);
				echo "{\"error\":{\"code\":500,\"message\":\"{$exception->getMessage()}\"},\"data\":null}";
				exit();
			} // catch
		} // catch
    } // __construct

    /**
	 * @return mixed
	 */
    private function getMethod()
    {
        return $this->method;
    } // getMethod

    /**
	 * @param string $method
	 * @throws TypeError
	 */
    public function setMethod(string $method): void
    {
        // Se o método que etiver na url existir, execute-o
        if (!method_exists($this->getController(), $method)) {
			$class_name = get_class($this->getController());
			throw new TypeError("The method \"{$method}\" doesn't exist on controller {$class_name}.", 501);
		} // if

		// Atribui o índice 1 da url ao método
		$this->method = $method;
	} // setMethod

    /** @return mixed */
    public function getController()
    {
        return $this->controller;
    } // getController

    /**
	 * @param string $controller
	 * @throws TypeError
	 */
    public function setController(string $controller): void
    {
		// Prepara a string da rota
		$autoload_controller = "Controllers\\{$controller}";

		// Se a classe não existir
		if (!class_exists($autoload_controller, true)) {
			throw new TypeError("The class \"{$controller}\" doesn't exist on Controllers folder.'", 501);
		}  // if

        // Instancia o Controller requisitado na url passando a autenticação como parâmetro
        $this->controller = new $autoload_controller((object)[]);
    } // setController
} // Dispatch
