<?php

namespace core\db;


class MySql extends DB
{
	protected const TABLE_NAME = null;

	/**
	 * @var \mysqli
	 */
	private \mysqli $link;

	/**
	 * connect to mysql
	 */
	protected function connect(): void {
		static $con;
		if(!isset($con)) {
			$con = new \mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);
		}
		$this->link = $con;
	}

	/**
	 * @param string $query
	 * @return bool|\mysqli_result
	 */
	protected function query(string $query) {
		$this->connect();
		$res = $this->link->query($query);

		if(!empty($this->link->error_list)) {

			$errorList = $this->link->error_list;
			$errorList[0]['full_query'] = $query;
			echo "<pre>";
			print_r($errorList);
			echo "<pre>";
		}

		return $res;
	}

	/**
	 * @param string $query
	 * @return array
	 */
	protected function fetchAssocRows(string $query, string $key): array {

		$result = $this->query($query);
		$data   = [];
		while ($row = $result->fetch_assoc()) {
			$data[$row[$key]] = $row;
		}
		return $data;
	}

	/**
	 * @param string $query
	 * @return array
	 */
	protected function fetchRows(string $query): array {

		$result = $this->query($query);
		$data   = [];
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;
		}
		return $data;
	}

	/**
	 * @param string $query
	 * @return array|null
	 */
	protected function fetchRow(string $query): array {
		return $this->query($query)->fetch_assoc() ?: [];
	}

	/**
	 * @param array $data
	 * @param bool $returnId
	 * @return int|null
	 */
	protected function insert(array $data, bool $returnId = false): ?int {

		$columns = '(';
		$values = 'values (';

		foreach ($data as $key => $value) {
			$columns .= $this->quoteField($key) . ',';
			$values  .= "'" . $this->realEscapeString($value) . "',";
		}
		$columns = rtrim($columns, ',') . ')';
		$values = rtrim($values, ',') . ')';

		$query = 'insert into ' . static::TABLE_NAME . " {$columns} {$values}";

		$this->query($query);

		return $returnId ? $this->lastInsertId() : null;

	}

	/**
	 * @param array $list
	 * @param string $onDuplicateUpdate
	 * @return array
	 */
	protected function insertBulk(array $list, string $onDuplicateUpdate = ''): array {

		$columns = '(';
		$values = 'values ';

		$cols = array_keys($list[0]);

		foreach ($cols as $col) {
			$columns .= $this->quoteField($col) . ',';
		}
		$columns = rtrim($columns, ',') . ')';

		foreach ($list as $data) {
			$values .= '(';
			foreach ($data as $key => $value) {
				$values  .= "'" . $this->realEscapeString($value) . "',";
			}
			$values = rtrim($values, ',') . '),';
		}

		$values = rtrim($values, ',');

		$query = 'insert into ' . static::TABLE_NAME . " {$columns} {$values} {$onDuplicateUpdate}";

		$this->query($query);

		return ['insert_id' => $this->lastInsertId(), 'affected_rows' => $this->affectedRows()];
	}


	protected function update(array $data, string $where): int {

		$keyValue= '';

		foreach ($data as $key => $value) {
			$keyValue .= $this->quoteField($key) . "='" . $this->realEscapeString($value) . "',";
		}
		$keyValue = rtrim($keyValue, ',');

		$query = 'update ' . static::TABLE_NAME . " set {$keyValue} where {$where}";

		$this->query($query);


		return $this->affectedRows();
	}

	protected function updateBulk() {

	}

	/**
	 * @param string $where
	 * @return int
	 */
	protected function delete(string $where): int {

		$query = 'delete from ' . static::TABLE_NAME . " where {$where}";
		$this->query($query);

		return $this->affectedRows();

	}

	/**
	 * @param string $string
	 * @return string
	 */
	protected function realEscapeString(string $string): string {
		$this->connect();
		return $this->link->real_escape_string($string);
	}

	/**
	 * @param $string
	 * @return int|mix
	 */
	protected function quoteField(string $string): string {
		return '`' . $string . '`';
	}

	/**
	 * @param $number
	 * @return int|mix
	 */
	protected function realEscapeNumber($number) {
		return is_numeric($number) ? $number : 0;
	}

	/**
	 * @return mixed
	 */
	protected function lastInsertId() {
		$this->connect();
		return $this->link->insert_id;
	}

	/**
	 * @return int
	 */
	protected function affectedRows(): int {
		$this->connect();
		return $this->link->affected_rows;
	}

	
}