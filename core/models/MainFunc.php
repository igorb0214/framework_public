<?php

namespace core\models;

class MainFunc
{
	/**
	 * @return bool
	 */
	public static function isAjax(): bool {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	}

	/**
	 * Set cookie via js
	 */
	public static function setCookieWithJs($cookieName, $cookieValue, $cookieLimitHour)
	{
		date_default_timezone_set('GMT');
		$t = 60*60;//1 hour

		$t *= $cookieLimitHour;

		$time = time() + $t;

		$expires = date("M Y H:i:s", $time);

		$js = 'document.cookie = "'.$cookieName.'='.$cookieValue.'; expires = '.$expires.'domain=.'.WEB_ROOT.';path=/"';


		echo '<script type="text/javascript">'.$js.'</script>';

	}

}