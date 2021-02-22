<?php

namespace core\traits;

trait Singleton
{

	private static $instance;

	protected function __construct()
	{
	}

	public static function getInstance() {

		if(!self::$instance) {
			self::$instance = new static();
		}

		return self::$instance;
	}

}