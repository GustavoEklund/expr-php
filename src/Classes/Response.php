<?php

namespace Classes;

use JsonException;

/**
 * Class Response
 * @package Classes
 */
final class Response
{
    /**
     * Retorno do token de autenticação
     * @var string $refresh_token
     */
    private $refresh_token = null;

    /**
     * Retorna a resposta em formato JSON a partir de um array.
     *
     * @param array $value
     * @return string
     * @throws JsonException
     */
    public function json(array $value): string
    {
        return json_encode([
            'error' => false,
            'data' => $value,
            'refreshToken' => $this->refresh_token,
        ], JSON_THROW_ON_ERROR);
    } // append

    /**
     * Retorna a resposta em formato de texto plano.
     *
     * @param $value
     * @return false|string
     * @throws JsonException
     */
    public function send($value = ''): string
    {
        return json_encode([
            'error' => false,
            'data' => $value,
            'refreshToken' => $this->refresh_token,
        ], JSON_THROW_ON_ERROR);
    } // send

    /**
     * Define o código de status HTTP antes do retorno.
     *
     * @param int $code
     * @return $this
     */
    public function status(int $code): Response
    {
        http_response_code($code);
        return $this;
    } // status

    /**
     * Define o token de autenticação para atualização,
     * se nenhum token for informado o token será `null`.
     *
     * @param string $refresh_token
     * @return $this
     */
    public function token(string $refresh_token): Response
    {
        $this->setRefreshToken($refresh_token);
        return $this;
    } // token

	/**
	 * @param string $header_name
	 * @param $value
	 */
	private function setHeader(string $header_name, $value): void
	{
		header("{$header_name}: $value");
	} // setHeader

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refresh_token;
    } // getRefreshToken

    /**
     * @param string $refresh_token
     */
    private function setRefreshToken(string $refresh_token): void
    {
        $this->refresh_token = $refresh_token;
    } // getRefreshToken
} // Response
