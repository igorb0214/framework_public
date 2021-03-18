<?php

namespace library\restApi;


class RestApiResult {

	private $info;
	private $result;


	public function __construct($result, $info) {
		$this->result = $result;
		$this->info   = $info;
	}

	/**
	 * @return mixed
	 */
	public function getResult() {
		return $this->result;
	}

	public function getJSONResult($assoc = true) {
		return json_decode($this->result, $assoc);
	}

	/**
	 * @return int|null
	 */
	public function getHttpStatus(): ?int {
		return $this->info['http_code'] ?? null;
	}
}