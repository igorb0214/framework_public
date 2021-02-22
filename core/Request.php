<?php


namespace core;


use core\traits\Singleton;

class Request
{
	use Singleton;

	/**
	 * @var array
	 */
	public array $get;
	/**
	 * @var array
	 */
	public array $post;
	/**
	 * @var array
	 */
	public array $header;
	/**
	 * @var array
	 */
	public array $cookie;
	/**
	 * @var string
	 */
	public string $rawPostData;
	/**
	 * @var array
	 */
	public array $rawPostDataArray;
	/**
	 * @var string
	 */
	public string $requestMethod;


	protected function __construct() {
		$this->get              = $_GET;
		$this->post             = $_POST;
		$this->header           = apache_request_headers();
		$this->cookie           = $_COOKIE;
		$this->rawPostData      = file_get_contents("php://input") ?? "";
		$this->rawPostDataArray = json_decode($this->rawPostData ?: "{}" , true) ?? [];
		$this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? "";
	}


}