<?php

namespace core;


use core\traits\Singleton;

/**
 * Class Response
 * @package core
 */
class Response
{

	use Singleton;

	private const KEY_DATA       = 'data';
	private const KEY_MESSAGE    = 'message';
	private const KEY_STATUS     = 'status';
	public const  STATUS_SUCCESS = 'success';
	public const  STATUS_ERROR   = 'error';

	/**
	 * @var array
	 */
	private array $response;

	protected function __construct()
	{
		$this->response = [
			self::KEY_DATA   => [],
			self::KEY_STATUS => self::STATUS_SUCCESS
		];
	}

	/**
	 * @param array $data
	 * @return $this
	 */
	public function setData(array $data): self {
		$this->response[self::KEY_DATA] = $data;
		return $this;
	}

	/**
	 * @param string $message
	 * @return $this
	 */
	public function setMessage(string $message): self {
		$this->response[self::KEY_MESSAGE] = $message;
		return $this;
	}

	/**
	 * @param string $status
	 * @return $this
	 */
	public function setStatus(string $status): self {
		$this->response[self::KEY_STATUS] = $status;
		return $this;
	}

	/**
	 * @param array $data
	 */
	public function returnJsonResponse(array $data = []): void {

		if(!empty($data)) {
			$this->setData($data);
		}

		echo json_encode($this->response);
	}


}