<?php

namespace Classes;

/**
 * Class ClassRoutes
 * @package Src\Classes
 */
final class Router
{
    /**
	 * @var Request $request
	 */
    private $request;

	/**
	 * @var Response $response
	 */
    private $response;

	/**
	 * @param string $uri Uri para conversão de parâmertros em variáveis
	 * @param string $action Define o controller ou middleware
	 * @param string $next Define o controlador a ser executado após o middleware.
	 * Se o método do middleware retornar falso ou null, $next não será executado.
	 * Se o método retornar um valor válido, então é passado como terceiro parâmetro para o controller.
	 * @return void
	 */
	public function post(string $uri, string $action, string $next = ''): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			return;
		} // if

		$this->addRoute($uri, $action, $next);
	} // post

	/**
	 * @param string $uri Uri para conversão de parâmertros em variáveis
	 * @param string $action Define o controller ou middleware
	 * @param string $next Define o controlador a ser executado após o middleware.
	 * Se o método do middleware retornar falso ou null, $next não será executado.
	 * Se o método retornar um valor válido, então é passado como terceiro parâmetro para o controller.
	 * @return void
	 */
	public function get(string $uri, string $action, string $next = ''): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
			return;
		} // if

		$this->addRoute($uri, $action, $next);
	} // get

	/**
	 * @param string $uri Uri para conversão de parâmertros em variáveis
	 * @param string $action Define o controller ou middleware
	 * @param string $next Define o controlador a ser executado após o middleware.
	 * Se o método do middleware retornar falso ou null, $next não será executado.
	 * Se o método retornar um valor válido, então é passado como terceiro parâmetro para o controller.
	 * @return void
	 */
	public function put(string $uri, string $action, string $next = ''): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
			return;
		} // if

		$this->addRoute($uri, $action, $next);
	} // put

	/**
	 * @param string $uri Uri para conversão de parâmertros em variáveis
	 * @param string $action Define o controller ou middleware
	 * @param string $next Define o controlador a ser executado após o middleware.
	 * Se o método do middleware retornar falso ou null, $next não será executado.
	 * Se o método retornar um valor válido, então é passado como terceiro parâmetro para o controller.
	 * @return void
	 */
	public function delete(string $uri, string $action, string $next = ''): void
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
			return;
		} // if

		$this->addRoute($uri, $action, $next);
	} // put

	/**
	 * Retorna uma rota assim que for executado.
	 * @param string $action Define o controller ou middleware
	 * @param string $next Define o controlador a ser executado após o middleware.
	 * Se o método do middleware retornar falso ou null, $next não será executado.
	 * Se o método retornar um valor válido, então é passado como terceiro parâmetro para o controller.
	 * @return void
	 */
	public function any(string $action, string $next = '@'): void
	{
		[$controller, $function] = explode('@', $action);
		[$next_controller, $next_function] = explode('@', $next);

		$dispatch = new Dispatch($controller, $function, [], $next_controller, $next_function);
	} // get

	/**
	 * @param string $uri Uri para conversão de parâmertros em variáveis
	 * @param string $action Define o controller ou middleware
	 * @param string $next Define o controlador a ser executado após o middleware
	 * Se o método do middleware retornar falso ou null, $next não será executado.
	 * Se o método retornar um valor válido, então é passado como terceiro parâmetro para o controller.
	 */
	private function addRoute(string $uri, string $action, string $next): void
	{
		$parsed_route = $this->parseRoute($uri, $_GET['url']);
		$next_controller = $next_function = '';

		if ($parsed_route === null) {
			return;
		} // if

		if ($next !== '') {
			[$next_controller, $next_function] = explode('@', $next);
		} // else

		[$controller, $function] = explode('@', $action);

		if (!file_exists(DIR_REQ."src/Controllers/{$controller}.php")) {
			return;
		} // if

		$dispatch = new Dispatch($controller, $function, $parsed_route, $next_controller, $next_function);
	} // addRoute

	/**
	 * @param string $path
	 * @param string $request_path
	 * @return array|null
	 */
	private function parseRoute(string $path, string $request_path): ?array
	{
		$path_array = explode('/', ltrim($path, '/'));
		$request_path_array = explode('/', $request_path);

        if (count($path_array) !== count($request_path_array)) {
            return null;
        } // if

        $parsed_route = [];

        // Url de requisição é igual a esta rota?
		foreach ($path_array as $key => $item) {
			$new_key = ltrim($item, ':');

			// Se o primeiro caractere de path for ':' -> parâmetro de rota
			$parsed_route[$new_key] = ((strpos($item, ':') === 0) ? $request_path_array[$key] : null);

			if ($parsed_route[$new_key] === null && $path_array[$key] !== $request_path_array[$key]) {
				return null;
			} // if
		} // foreach

		return $parsed_route;
	} // parseRoute
} // ClassRoutes
