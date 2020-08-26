<?php

namespace Classes;

use stdClass;

class AuthenticationToken
{
    /**
     * "iss" (emissor) identifica quem emitiu o JWT. O processamento desta reivindicação é geralmente
     * específico do aplicativo. O valor "iss" é uma sequência que diferencia maiúsculas de minúsculas
     * que contém um valor StringOrURI.
     * @var string $iss
     */
    private $iss;

    /**
     * "iat" (emitida em) identifica o horário em que a JWT foi emitida. Essa alegação pode ser usada para
     * determinar a idade do JWT. Seu valor DEVE ser um número que contenha um valor NumericDate.
     * @var int $iat
     */
    private $iat;

    /**
     * "exp" (tempo de expiração) identifica o tempo de expiração no qual ou após o qual o JWT NÃO DEVE ser
     * aceito para processamento. O processamento da "exp" exige que a data / hora atual
     * DEVE ser anterior à data / hora de vencimento listada na reivindicação "exp".
     *
     * Os implementadores PODEM prever uma pequena margem de manobra, geralmente não mais do que alguns minutos,
     * para contabilizar a inclinação do relógio. Seu valor DEVE ser um número que contenha um valor NumericDate.
     * @var int $exp
     */
    private $exp;

    /**
     * "sub" (sujeito) identifica o principal que é o sujeito do JWT. O valor do assunto DEVE ter o escopo definido
     * como localmente exclusivo no contexto do emissor ou globalmente exclusivo. O processamento é geralmente
     * específico do aplicativo. O valor "sub" é uma sequência que diferencia maiúsculas de minúsculas
     * que contém um valor StringOrURI.
     * @var string $sub
     */
    private $sub;

    /**
     * AuthenticationToken constructor.
     * @param stdClass $token
     */
    public function __construct(stdClass $token)
    {
        $this->setIss($token->iss);
        $this->setIat($token->iat);
        $this->setExp($token->exp);
        $this->setSub($token->sub);
    } // __construct

	/**
	 * @return string
	 */
	public function getIss(): string
	{
		return $this->iss;
	}

	/**
	 * @param string $iss
	 * @return AuthenticationToken
	 */
	public function setIss(string $iss): AuthenticationToken
	{
		$this->iss = $iss;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getIat(): int
	{
		return $this->iat;
	}

	/**
	 * @param int $iat
	 * @return AuthenticationToken
	 */
	public function setIat(int $iat): AuthenticationToken
	{
		$this->iat = $iat;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getExp(): int
	{
		return $this->exp;
	}

	/**
	 * @param int $exp
	 * @return AuthenticationToken
	 */
	public function setExp(int $exp): AuthenticationToken
	{
		$this->exp = $exp;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSub(): string
	{
		return $this->sub;
	}

	/**
	 * @param string $sub
	 * @return AuthenticationToken
	 */
	public function setSub(string $sub): AuthenticationToken
	{
		$this->sub = $sub;
		return $this;
	}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'iss' => $this->getIss(),
            'iat' => $this->getIat(),
            'exp' => $this->getExp(),
            'sub' => $this->getSub(),
        ]; // return
    } // toArray
} // AuthenticationToken
