<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Session;

class GoogleAnalytics {

	private static $token = null;

	public static function getToken() {
		if (null !== self::$token)
			return self::$token;

		$token = Session::get('google_analytics') ?? null;
		$client = new \Google_Client();
		if (empty($token)) {
			$token = self::generateAccessToken($client);
			if (!empty($token)) {
				session(['google_analytics' => $token]);
				Session::save();
			}
		} else {
			$client->setAccessToken($token);
			if ($client->isAccessTokenExpired()) {
				$token = self::generateAccessToken($client);
			}
		}

		self::$token = $token;

		return $token;
	}

	public static function getAccessToken() {
		return self::getToken()['access_token'];
	}

	/**
	 * @param $client
	 * @return mixed
	 */
	private static function generateAccessToken($client) {
		$client->setApplicationName("Google Analytics");
		$client->setAuthConfig(storage_path(env('GOOGLE_SERVICE_CLIENT_JSON')));
		$client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);

		$client->refreshTokenWithAssertion();
		$token = $client->getAccessToken();
		return $token;
	}

}