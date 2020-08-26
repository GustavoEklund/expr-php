<?php

namespace ClassesTests;

use Classes\VariableItem;
use PHPUnit\Framework\TestCase;

class VariableItemTest extends TestCase
{
	public function assertPreConditions(): void
	{
		self::assertTrue(class_exists(VariableItem::class));
	} // assertPreConditions

	public function test_assert_to_array(): void
	{
		$variable = new VariableItem(
			'Gustavo Eklund',
			'Nome',
			true,
			VariableItem::TYPE_STRING,
			5,
			64,
		); // VariableItem

		$array = $variable->toArray();

		self::assertArrayHasKey('value', $array);
		self::assertArrayHasKey('display', $array);
		self::assertArrayHasKey('required', $array);
		self::assertArrayHasKey('type', $array);
		self::assertArrayHasKey('min_length', $array);
		self::assertArrayHasKey('max_length', $array);
		self::assertArrayHasKey('min_value', $array);
		self::assertArrayHasKey('max_value', $array);
		self::assertArrayHasKey('match', $array);
	} // test_can_be_instantiated
} // VariableItemTest
