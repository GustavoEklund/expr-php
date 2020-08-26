<?php

namespace Classes;

use RangeException;
use TypeError;
use UnexpectedValueException;

/**
 * Class Variable
 * @package Classes
 */
class Variable extends VariableTypes
{
    /**
     * @param VariableItem[] $variable_list
     * @return array
     * @throws UnexpectedValueException
	 * @throws RangeException
     */
    public static function test(array $variable_list): array
    {
        $sanitized_variable_array = [];

        foreach ($variable_list as $key => $variable_list_class) {
        	$item = $variable_list_class->toArray();

			$value = $item['value'] ?? null;
			$is_required = $item['required'];
			$value_name_display = $item['display'] ?? 'Unexpected';
			$min_length = $item['min_length'] ?? false;
			$max_length = $item['max_length'] ?? false;
			$min_value = $item['min_value'] ?? false;
			$max_value = $item['max_value'] ?? false;
			$match = $item['match'] ?? false;
			$type = $item['type'] ?? false;

			$valid_values = [false, 0, '0'];

            if ($is_required && (!$value && !in_array($value, $valid_values, true))) {
                throw new UnexpectedValueException("{$value_name_display} não informado(a)!", 412);
            } // if

            // Tratar strings
            if (
            	($is_required &&( $type === self::TYPE_STRING))
				&& (str_replace(' ', '', $value) !== '')
			) {
                $filtered_string = filter_var($value, FILTER_SANITIZE_STRING);

                if ($filtered_string === false) {
					$sanitized_variable_array[$key] = null;
					continue;
				} // if

                if ($min_length && strlen(utf8_decode($filtered_string)) < $min_length) {
                    throw new RangeException("O(a) {$value_name_display} deve ter pelo menos {$min_length} caracteres!", 412);
                } // if

                if ($max_length && strlen(utf8_decode($filtered_string)) > $max_length) {
                    throw new RangeException("O(a) {$value_name_display} deve ter no máximo {$max_length} caracteres!", 412);
                } // if

                $sanitized_variable_array[$key] = $filtered_string;
                continue;
            } // if

			// Tratar inteiros
			if ($is_required && ($type === self::TYPE_INTEGER)) {
				$filtered_int = filter_var($value, FILTER_VALIDATE_INT);

				if ($filtered_int === false) {
					$sanitized_variable_array[$key] = null;
					continue;
				} // if

				if ($min_value > $filtered_int) {
					throw new RangeException("O(a) {$value_name_display} deve ter o valor mínimo de {$min_value}!");
				} // if

				if ($max_value < $filtered_int) {
					throw new RangeException("O(a) {$value_name_display} deve ter o valor máximo de {$max_value}!");
				} // if

				$sanitized_variable_array[$key] = $filtered_int;
				continue;
			} // if

			// Tratar números reais
			if ($is_required && ($type === self::TYPE_FLOAT)) {
				$filtered_float = filter_var($value, FILTER_VALIDATE_FLOAT);

				if ($filtered_float === false) {
					$sanitized_variable_array[$key] = null;
					continue;
				} // if

				if ($min_value > $filtered_float) {
					throw new RangeException("O(a) {$value_name_display} deve ter o valor mínimo de {$min_value}!");
				} // if

				if ($max_value < $filtered_float) {
					throw new RangeException("O(a) {$value_name_display} deve ter o valor máximo de {$max_value}!");
				} // if

				$sanitized_variable_array[$key] = $filtered_float;
				continue;
			} // if

			// Tratar booleanos
            if ($is_required && ($type === self::TYPE_BOOLEAN)) {
                $sanitized_variable_array[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                continue;
            } // if

			if ($is_required) {
				$sanitized_variable_array[$key] = null;
				continue;
			} // if

            if (!$is_required) {
                $sanitized_variable_array[$key] = $value;
                continue;
            } // if

            throw new TypeError('Erro interno ao processar os parâmetros.', 500);
        } // foreach

        return $sanitized_variable_array;
    } // testString
} // TraitTestVar
