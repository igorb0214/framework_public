<?php

namespace core\db;


abstract class DB
{

	protected const TABLE_NAME = null;

	/**
	 *
	 */
	abstract protected function connect();

	/**
	 * @param string $query
	 */
	abstract protected function query(string $query);

	/**
	 * @param string $query
	 * @param string $key
	 * @return array
	 */
	abstract protected function fetchAssocRows(string $query, string $key): array;

	/**
	 * @param string $query
	 * @return array
	 */
	abstract protected function fetchRows(string $query): array;

	/**
	 * @param string $query
	 * @return array
	 */
	abstract protected function fetchRow(string $query): array;

	abstract protected function insert(array $data, bool $returnId = false): ?int;
	protected function insertIgnore() {

	}
	protected function insertBulk(array $list, string $onDuplicateUpdate = ''): array {

	}
	abstract protected function update(array $data, string $where): int;
	protected function updateBulk() {

	}
	abstract protected function delete(string $where): int;
	protected function deleteBulk() {

	}

	/**
	 * @param string $string
	 * @return string
	 */
	abstract protected function realEscapeString(string $string): string;

	/**
	 * @param array $list
	 * @return string
	 */
	protected function realEscapeListString(array $list): string {
		$this->connect();
		$stringList = '(';
		foreach ($list as $value) {
			$stringList .= "'" . $this->realEscapeString($value) . "',";
		}
		return rtrim($stringList, ',') . ')';
	}

	/**
	 * @param string $string
	 * @return string
	 */
	abstract protected function quoteField(string $string): string;

	/**
	 * @param $number
	 * @return int|mix
	 */
	abstract protected function realEscapeNumber($number);


	/**
	 * @param array $list
	 * @return string
	 */
	protected function realEscapeListNumber(array $list): string {
		$this->connect();
		$stringList = '(';
		foreach ($list as $value) {
			$stringList .= $this->realEscapeNumber($value) . ',';
		}
		return rtrim($stringList, ',') . ')';
	}

	/**
	 * @return mixed
	 */
	protected function lastInsertId() {
		return 0;
	}

	/**
	 * @return int
	 */
	protected function affectedRows(): int {
		return 0;
	}

	
}