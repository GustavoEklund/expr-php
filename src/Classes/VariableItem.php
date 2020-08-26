<?php

namespace Classes;

/**
 * Class VariableItem
 * @package Classes
 */
final class VariableItem extends VariableTypes
{
	/** @var string|int|float|bool|null $value */
	private $value;

	/** @var string $display */
	private $display;

	/** @var bool $required */
	private $required;

	/** @var string */
	private $type;

	/** @var int $min_length */
	private $min_length;

	/** @var int $max_length */
	private $max_length;

	/** @var int $min_value */
	private $min_value;

	/** @var int $max_value */
	private $max_value;

	/** @var string $match */
	private $match;

	/**
	 * VariableItem constructor.
	 * @param bool|float|int|string|null $value
	 * @param string $display
	 * @param bool $required
	 * @param string $type
	 * @param int $min_length
	 * @param int $max_length
	 * @param int $min_value
	 * @param int $max_value
	 * @param string $match
	 */
	public function __construct(
		$value,
		string $display = 'Undefined',
		bool $required = false,
		string $type = self::TYPE_STRING,
		int $min_length = 0,
		int $max_length = 2^(32-1),
		int $min_value = -2147483648,
		int $max_value = 2147483647,
		string $match = ''
	) {
		$this->value = $value;
		$this->display = $display;
		$this->required = $required;
		$this->type = $type;
		$this->min_length = $min_length;
		$this->max_length = $max_length;
		$this->min_value = $min_value;
		$this->max_value = $max_value;
		$this->match = $match;
	} // __construct

	/**
	 * Retorna o conteúdo da classe em um array
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'value' => $this->value,
			'display' => $this->display,
			'required' => $this->required,
			'type' => $this->type,
			'min_length' => $this->min_length,
			'max_length' => $this->max_length,
			'min_value' => $this->min_value,
			'max_value' => $this->max_value,
			'match' => $this->match,
		]; // return
	} // toArray
} // VariableItem

