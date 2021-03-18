<?php

namespace library\restApi;

class RestApi
{
	private const METHOD_GET    = "GET";
	private const METHOD_POST   = "POST";
	private const METHOD_PUT    = "PUT";
	private const METHOD_DELETE = "DELETE";

	/**
	 * @param string $url
	 * @param array $customOptions
	 * @return mixed
	 */
	public static function get(string $url, array $customOptions = []) {
		return self::exec($url, self::METHOD_GET, [], $customOptions);
	}

	/**
	 * @param string $url
	 * @param array $postFields
	 * @param array $customOptions
	 * @return mixed
	 */
	public static function post(string $url, array $postFields, array $customOptions = [])
	{
		return self::exec($url, self::METHOD_POST, $postFields, $customOptions);
	}

	/**
	 * @param string $url
	 * @param array $postFields
	 * @param array $customOptions
	 * @return mixed
	 */
	public static function postJSON(string $url, array $postFields, array $customOptions = []) {
		return self::execJSON($url, self::METHOD_POST, $postFields, $customOptions);
	}

	/**
	 * @param string $url
	 * @param array $postFields
	 * @param array $customOptions
	 * @return mixed
	 */
	public static function put(string $url, array $postFields, array $customOptions = []) {
		return self::exec($url, self::METHOD_PUT, $postFields, $customOptions);
	}

	/**
	 * @param string $url
	 * @param array $postFields
	 * @param array $customOptions
	 * @return mixed
	 */
	public static function putJSON(string $url, array $postFields, array $customOptions = []) {
		return self::execJSON($url, self::METHOD_PUT, $postFields, $customOptions);
	}

	/**
	 * @param string $url
	 * @param array $customOptions
	 * @return mixed
	 */
	public static function delete(string $url, array $customOptions = []) {
		return self::exec($url, self::METHOD_DELETE, [], $customOptions);
	}

	/**
	 * @param string $url
	 * @param string $reqMethod
	 * @param array $postFields
	 * @param array $customOptions
	 * @return mixed
	 */
	private static function execJSON(string $url, string $reqMethod, array $postFields, array $customOptions) {

		$postFieldsEncoded = json_encode(json_decode($postFields, true));
		if (!$postFieldsEncoded) {
			$postFieldsEncoded = $postFields;
		}

		$headers = [
			'Content-Type: application/json',
			'Content-Length: ' . strlen($postFieldsEncoded)
		];

		if (isset($customOptions[CURLOPT_HTTPHEADER])) {
			$customOptions[CURLOPT_HTTPHEADER] = array_merge($customOptions[CURLOPT_HTTPHEADER], $headers);
		} else {
			$customOptions[CURLOPT_HTTPHEADER] = $headers;
		}

		return self::exec($url, $reqMethod, $postFields_encoded, $customOptions);
	}

	/**
	 * @param string $url
	 * @param string $reqMethod
	 * @param array $postFields
	 * @param array $customOptions
	 * @return mixed
	 */
	private static function exec(string $url, string $reqMethod, array $postFields, array $customOptions) {

		$ch = self::initCurl($url, $reqMethod, $postFields, $customOptions);

		$result = curl_exec($ch);
		$info   = curl_getinfo($ch);
		curl_close($ch);

		return new restApiResult($result, $info);
	}

	/**
	 * @param string $url
	 * @param string $reqMethod
	 * @param array $postFields
	 * @param array $customOptions
	 * @return false|resource
	 */
	private static function initCurl(string $url, string $reqMethod, array $postFields, array $customOptions) {

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $reqMethod);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		switch ($reqMethod) {
			case 'POST':
			case 'PUT':
				if (!empty($postFields)) {
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
				}
				break;
		}

		foreach ($customOptions as $k => $v) {
			curl_setopt($ch, $k, $v);
		}

		return $ch;
	}
}
