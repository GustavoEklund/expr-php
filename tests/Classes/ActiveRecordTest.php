<?php

namespace ClassesTests;

use Classes\ActiveRecord;
use IteratorAggregate;
use JsonException;
use PHPUnit\Framework\TestCase;

class ActiveRecordTest extends TestCase
{
	/** @var ActiveRecord $active_record */
	protected $active_record;

	public function assertPreConditions(): void
	{
		self::assertTrue(class_exists(ActiveRecord::class));
	} // assertPreConditions

	public function setUp(): void
	{
		$this->active_record = $this->getMockForAbstractClass(ActiveRecord::class);
	} // setUp

	public function test_can_magic_set_and_get_values(): void
	{
		$this->active_record->id = 1;
		$this->active_record->name = 'Gustavo Eklund';
		$this->active_record->active = true;

		self::assertEquals(1, $this->active_record->id);
		self::assertEquals('Gustavo Eklund', $this->active_record->name);
		self::assertEquals(true, $this->active_record->active);
	} // test_can_magic_set_values

	public function test_can_magic_check_is_set_values(): void
	{
		$this->active_record->id = 1;

		self::assertTrue(isset($this->active_record->id));
		self::assertNotEmpty($this->active_record->id);
	} // test_can_magic_check_is_set_values

	public function test_can_magic_unset_values(): void
	{
		$this->active_record->id = 1;
		unset($this->active_record->id);

		self::assertFalse(isset($this->active_record->id));
	} // test_can_magic_unset_values

	public function test_can_clone_object(): void
	{
		$clonedActiveRecord = clone $this->active_record;

		self::assertEquals($clonedActiveRecord, $this->active_record);
	} // test_can_clone_object

	public function test_assert_cloned_object_unset_id(): void
	{
		$this->active_record->id = 1;

		$clonedActiveRecord = clone $this->active_record;

		self::assertFalse(isset($clonedActiveRecord->id));
	} // test_assert_cloned_object_unset_id

	public function test_assert_return_content_to_array(): void
	{
		$empty_array = $this->active_record->toArray();
		self::assertEquals([], $empty_array);

		$this->active_record->id = 1;
		$array_with_one_value = $this->active_record->toArray();
		self::assertEquals(['id' => 1], $array_with_one_value);

		$this->active_record->name = 'Gustavo Eklund';
		$array_with_two_values = $this->active_record->toArray();
		self::assertEquals(['id' => 1, 'name' => 'Gustavo Eklund'], $array_with_two_values);

		$this->active_record->active = true;
		$array_with_three_values = $this->active_record->toArray();
		self::assertEquals(['id' => 1, 'name' => 'Gustavo Eklund', 'active' => true], $array_with_three_values);
	} // testConvertContentToArray

	public function test_assert_set_values_from_array(): void
	{
		$this->active_record->fromArray(['id' => 1, 'name' => 'Gustavo Eklund', 'active' => true]);

		self::assertEquals(1, $this->active_record->id);
		self::assertEquals('Gustavo Eklund', $this->active_record->name);
		self::assertEquals(true, $this->active_record->active);
	} // test_assert_set_values_from_array

	/**
	 * @throws JsonException
	 */
	public function test_can_return_content_to_json_format(): void
	{
		$this->active_record->id = 1;
		$this->active_record->name = 'Gustavo Eklund';
		$this->active_record->active = true;

		self::assertEquals('{"id":1,"name":"Gustavo Eklund","active":true}', $this->active_record->toJson());
	} // test_can_return_content_to_json_format

	/**
	 * @throws JsonException
	 */
	public function test_can_set_magic_values_from_json(): void
	{
		$this->active_record->fromJson('{"id":1,"name":"Gustavo Eklund","active":true}');

		self::assertEquals(1, $this->active_record->id);
		self::assertEquals('Gustavo Eklund', $this->active_record->name);
		self::assertEquals(true, $this->active_record->active);
	} // test_can_set_magic_values_from_json

	public function test_assert_active_record_is_instance_of_iterator_aggregate(): void
	{
		self::assertInstanceOf(IteratorAggregate::class, $this->active_record);
	} // test_assert_active_record_is_instance_of_iterator_aggregate

	public function test_assert_is_iterable(): void
	{
		$this->active_record->id = 1;
		$this->active_record->name = 'Gustavo Eklund';
		$this->active_record->active = true;

		$items = [];

		foreach ($this->active_record as $item) {
			$items[] = $item;
		} // foreach

		self::assertCount(3, $items);
	} // test_assert_is_iterable

	public function test_assert_convert_always_return_array_of_strings(): void
	{
		$this->active_record->id = 1;
		$this->active_record->name = 'Gustavo Eklund';
		$this->active_record->active = true;
		$this->active_record->age = null;
		$this->active_record->array = [];
		$this->active_record->email = '';
		$this->active_record->country = ' ';

		$new_content = $this->active_record->convertContent();

		self::assertEquals([
			'id' => '1',
			'name' => '\'Gustavo Eklund\'',
			'active' => 'true',
			'age' => 'null',
			'array' => 'null',
			'email' => 'null',
			'country' => '\' \'',
		], $new_content);
	} // test_assert_format_always_return_string
} // ActiveRecordTest
