<?php


namespace core\models;


class Curl
{

	/**
	 * @param string $url
	 * @return bool|string
	 */
	public static function getWithCurl(string $url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") );
		curl_setopt($ch, CURLOPT_NOBODY, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_ENCODING , "gzip");
		if(getenv('ENV') === 'DEV') {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		}

		if(!$result = curl_exec($ch))
		{
			trigger_error(curl_error($ch));
		}

		return $result;
	}

	/**
	 * Used to send posts data asyncronic mode.
	 *
	 * @param string $url
	 * @param $params
	 * @param string $postFormat
	 */
	public static function postAsync(string $url, $params, string $postFormat = '')
	{
		if ($postFormat == 'json')
		{
			$postString= 'data=' . json_encode($params);
		}
		else if ($postFormat == 'xml')
		{
			$postString= 'data=' . $params;
		}
		else
		{
			foreach ($params as $key => &$val)
			{
				if (is_array($val)) {
					$val = implode(',', $val);
				}
				$postParams[] = $key . '=' . urlencode($val);
			}

			$postString = implode('&', $postParams);
		}

		$parts = parse_url($url);

		//add all get data also
		if(isset($parts['query']))
			$parts['path'].='?'.$parts['query'];

		$fp = fsockopen($parts['host'],isset($parts['port'])?$parts['port']:80,$errno, $errstr, 30);

		$out  = "POST "  . $parts['path'] . " HTTP/1.1\r\n";
		$out .= "Host: " . $parts['host'] . "\r\n";
		$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$out .= "Content-Length: " . strlen($postString) . "\r\n";
		$out .= "Connection: Close\r\n\r\n";

		if (isset($postString))
		{
			$out .= $postString;
		}

		fwrite($fp, $out);
		fclose($fp);
	}

	/**
	 * Gets data as an associative array or a JSON string and posts it to a specified URL
	 * via CURLPosts data in JSON format via CURL in a synchronic way.
	 *
	 * @param string $url
	 * @param array $data
	 * @param array $customHeader
	 * @return bool|string
	 */
	public static function curlPostJSON(string $url, array $data, array $customHeader = [])
	{
		$postFieldsString = 'data=' . json_encode($data);
		$ch               = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, self::arrayToHeaders($customHeader));
		curl_setopt($ch, CURLOPT_POST, TRUE);					// Set HTTP request method to POST
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFieldsString);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);			// Return the transfer

		//execute post
		$result = curl_exec($ch);

		//close connection
		curl_close($ch);

		return $result;
	}

	/**
	 * Used to send posts data in an synchronic way.
	 * 
	 * @param $url
	 * @param $fields
	 * @param array $customHeader
	 * @return bool|string
	 */
	public static function curlPost(string $url, array $fields, array $customHeader = [])
	{
		$fieldsString = http_build_query($fields);
		$ch           = curl_init();

		//set the url, number of POST vars, POST data
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, self::arrayToHeaders($customHeader));
		curl_setopt($ch, CURLOPT_POST, TRUE);					// Set HTTP request method to POST
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);			// Return the transfer

		//execute post
		$result = curl_exec($ch);

		//close connection
		curl_close($ch);

		return $result;
	}

	/**
	 * @param array $arr
	 * @return array
	 */
	private static function arrayToHeaders(array $arr): array {
		$res = [];
		foreach ($arr as $key => $value) {
			$res[] = "{$key}: {$value}";
		}
		return $res;
	}

}