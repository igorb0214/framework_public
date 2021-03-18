<?php

namespace library\restApi;


class Curl
{

	/**
	 * @param string $url
	 * @return bool|string
	 */
	public static function getWithCurl(string $url) {

		$options = [
			CURLOPT_HTTPHEADER => Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15"),
			CURLOPT_NOBODY => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "gzip",
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_SSL_VERIFYPEER => false,
		];

		return RestApi::get($url, $options)->getResult();

	}

	/**
	 * @param string $url
	 * @param array $params
	 */
	public static function postAsync(string $url, array $params)
	{
		$postParams = [];

		foreach ($params as $key => &$val) {

			if (is_array($val)) {
				$val = implode(',', $val);
			}
			$postParams[] = $key . '=' . urlencode($val);

		}

		$postString = implode('&', $postParams);

		$parts = parse_url($url);

		//add all get data also
		if(isset($parts['query'])) {
			$parts['path'] .= '?' . $parts['query'];
		}

		$fp = fsockopen($parts['host'], $parts['port'] ?? 80, $errno, $errstr, 30);

		$out  = "POST {$parts['path']} HTTP/1.1\r\n";
		$out .= "Host: {$parts['host']}\r\n";
		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out .= "Content-Length: " . strlen($postString) . "\r\n";
		$out .= "Connection: Close\r\n\r\n";

		if (isset($postString)) {
			$out .= $postString;
		}

		fwrite($fp, $out);
		fclose($fp);
	}

}