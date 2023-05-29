<?php

namespace Mythic\Abstracts;

abstract class MC2_Vue
{
	public static $fields = [];

	/**
	 * MC2_Vue constructor.
	 * @param $data
	 */
	public function __construct($data)
	{
		$object_data = $this->prepareObjectData($data);
		$this->setObjectData($object_data);
	}


	/**
	 * @param $property
	 * @param $value
	 */
	public function __set($property, $value)
	{
		if(!in_array($property, static::$fields)) $fields[] = $property;

		$this->$property = $value;
	}


	/**
	 * @param $property
	 * @return |null
	 */
	public function __get($property)
	{
		if (property_exists($this, $property)) return $this->$property;

		return null;
	}


	/**
	 * @param $data
	 */
	public function setObjectData($data)
	{
		if (empty(static::$fields) || empty($data)) return;

		foreach (static::$fields as $field) {
			if (!isset($data[$field])) continue;
			$this->$field = $data[$field];
		}
	}


	/**
	 * @param bool $json
	 * @return array|false|mixed|string|void
	 */
	public function returnObjectData($json = false)
	{
		if (empty(static::$fields)) return;

		$data = [];
		foreach (static::$fields as $field) $data[$field] = $this->$field;

		if (!$json) return $data;

		return $this->convertToJsonObjectData($data);
	}


	/**
	 * @param $data
	 * @return array
	 */
	public static function prepareObjectData($data)
	{
		$result = [];
		if (empty(static::$fields) || empty($data)) return $result;

		if (is_array($data)) {
			foreach (static::$fields as $field) {
				if (array_key_exists($field, $data)) $result[$field] = $data->$field;
			}
		} else {
			foreach (static::$fields as $field) {
				if (property_exists($data, $field)) $result[$field] = $data->$field;
			}
		}

		return $result;
	}


	/**
	 * @param $data
	 * @param bool $multiple
	 * @param bool $json
	 * @return array|false|mixed|string|void
	 */
	public static function generateObjectData($data, $multiple = false, $json = false)
	{
		if ($multiple) {
			$result = [];
			foreach ($data as $data_single) {
				$result[] = static::prepareObjectData($data_single);
			}
		} else {
			$result = static::prepareObjectData($data);
		}

		if($json) return static::convertToJsonObjectData($result);

		return $result;
	}


	/**
	 * @param $data
	 * @return false|mixed|string|void
	 */
	public static function convertToJsonObjectData($data)
	{
		return json_encode($data);
	}

}