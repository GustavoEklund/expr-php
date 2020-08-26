<?php

namespace ClassesTests;

use Classes\Variable;
use Classes\VariableItem;
use RangeException;
use UnexpectedValueException;
use PHPUnit\Framework\TestCase;

class VariableTest extends TestCase
{
	public function assertPreConditions(): void
	{
		self::assertTrue(class_exists(Variable::class));
		self::assertTrue(class_exists(VariableItem::class));
	} // assertPreConditions

	public function test_assert_test_string(): void
	{
		// Arrange Act
		$array = Variable::test([
			'name' => new VariableItem(
				'Gustavo Eklund',
				'Nome',
				true,
				VariableItem::TYPE_STRING,
				5,
				64,
			), // name
			'age' => new VariableItem(
				20,
				'Idade',
				true,
				VariableItem::TYPE_STRING,
			), // age
			'active' => new VariableItem(
				true,
				'Ativo',
				true,
				VariableItem::TYPE_STRING,
			), // age
		]); // test

		// Assert
		self::assertArrayHasKey('name', $array);
		self::assertArrayHasKey('age', $array);
		self::assertArrayHasKey('active', $array);
		self::assertEquals('Gustavo Eklund', $array['name']);
		self::assertEquals('20', $array['age']);
		self::assertEquals('1', $array['active']);
	} // test_assert_test_string

	public function test_assert_test_integer(): void
	{
		// Arrange Act
		$array = Variable::test([
			'age' => new VariableItem(
				20,
				'Idade',
				true,
				VariableItem::TYPE_INTEGER,
				0,
				0,
				0,
				1000,
			), // age
			'units' => new VariableItem(
				'Gustavo Eklund',
				'Unidades',
				true,
				VariableItem::TYPE_INTEGER,
			), // units
			'length' => new VariableItem(
				true,
				'Largura',
				true,
				VariableItem::TYPE_INTEGER,
			), // length
			'width' => new VariableItem(
				'50',
				'Comprimento',
				true,
				VariableItem::TYPE_INTEGER,
			), // width
		]); // test

		// Assert
		self::assertArrayHasKey('age', $array);
		self::assertArrayHasKey('units', $array);
		self::assertArrayHasKey('length', $array);
		self::assertArrayHasKey('width', $array);
		self::assertEquals(20, $array['age']);
		self::assertEquals(0, $array['units']);
		self::assertEquals(1, $array['length']);
		self::assertEquals(50, $array['width']);
	} // test_assert_test_integer

	public function test_assert_test_boolean(): void
	{
		// Arrange Act
		$array = Variable::test([
			'active' => new VariableItem(
				true,
				'Ativo',
				true,
				VariableItem::TYPE_BOOLEAN,
			), // active
			'has_access' => new VariableItem(
				'true',
				'Acesso',
				true,
				VariableItem::TYPE_BOOLEAN,
			), // has_access
			'write_permission' => new VariableItem(
				'Gustavo Eklund',
				'Permissão de escrita',
				true,
				VariableItem::TYPE_BOOLEAN,
			), // write_permission
			'execute_permission' => new VariableItem(
				57,
				'Permissão de execução',
				true,
				VariableItem::TYPE_BOOLEAN,
			), // execution_permission
		]); // test

		// Assert
		self::assertArrayHasKey('active', $array);
		self::assertArrayHasKey('has_access', $array);
		self::assertArrayHasKey('write_permission', $array);
		self::assertArrayHasKey('execute_permission', $array);
		self::assertEquals(true, $array['active']);
		self::assertEquals(true, $array['has_access']);
		self::assertEquals(false, $array['write_permission']);
		self::assertEquals(false, $array['execute_permission']);
	} // test_assert_test_boolean

	public function test_assert_display_error_required_value_string_empty(): void
	{
		// Assert
		$this->expectException(UnexpectedValueException::class);

		// Arrange Act
		$array = Variable::test([
			'name' => new VariableItem(
				'',
				'Nome',
				true,
				VariableItem::TYPE_STRING,
			), // name
		]); // test
	} // test_assert_display_error

	public function test_assert_display_error_required_when_value_is_null(): void
	{
		// Assert
		$this->expectException(UnexpectedValueException::class);

		// Arrange Act
		$array = Variable::test([
			'name' => new VariableItem(
				null,
				'Nome',
				true,
				VariableItem::TYPE_STRING,
			), // name
		]); // test
	} // test_assert_display_error

	public function test_assert_throw_error_min_lenght(): void
	{
		// Assert
		$this->expectException(RangeException::class);

		// Arrange Act
		$array = Variable::test([
			'name' => new VariableItem(
				'João',
				'Nome',
				true,
				VariableItem::TYPE_STRING,
				5,
				64,
			), // name
		]); // test
	} // test_assert_display_error_min_lenght

	public function test_assert_throw_error_max_length(): void
	{
		// Assert
		$this->expectException(RangeException::class);

		// Arrange Act
		$array = Variable::test([
			'name' => new VariableItem(
				'Gustavo Eklund',
				'Nome',
				true,
				VariableItem::TYPE_STRING,
				5,
				10,
			), // name
		]); // test
	} // test_assert_throw_error_max_length

	public function test_assert_throw_error_min_value(): void
	{
		// Assert
		$this->expectException(RangeException::class);

		// Arrange Act
		$array = Variable::test([
			'age' => new VariableItem(
				17,
				'Idade',
				true,
				VariableItem::TYPE_INTEGER,
				0,
				0,
				18,
				60,
			), // age
		]); // test
	} // test_assert_throw_error_min_value

	public function test_assert_throw_error_max_value(): void
	{
		// Assert
		$this->expectException(RangeException::class);

		// Arrange Act
		$array = Variable::test([
			'age' => new VariableItem(
				61,
				'Idade',
				true,
				VariableItem::TYPE_INTEGER,
				0,
				0,
				18,
				60,
			), // age
		]); // test
	} // test_assert_throw_error_min_value

	public function test_assert_not_required_value(): void
	{
		// Arrange Act
		$array = Variable::test([
			'name' => new VariableItem(
				'Gustavo Eklund',
				'Nome',
				false,
				VariableItem::TYPE_STRING,
			), // name
			'age' => new VariableItem(
				null,
				'Idade',
				false,
				VariableItem::TYPE_INTEGER,
			), // age
		]); // test

		self::assertArrayHasKey('name', $array);
		self::assertArrayHasKey('age', $array);
		self::assertEquals('Gustavo Eklund', $array['name']);
		self::assertEquals(null, $array['age']);
	} // test_assert_not_required_value
} // VariableTest
