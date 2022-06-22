<?php

namespace App\Http\Controllers;

use App\UrlList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\VarDumper\VarDumper;
use App\Http\Services\GoogleAnalytics;

/**
 * Class AnalyticsController
 * @package App\Http\Controllers
 */
class RequestController extends Controller {

	private $token;
	private $access;
	private $sites;
	private $prefix;

	public function __construct() {
		$this->middleware(function ($request, $next) {
			//$this->token = GoogleAnalytics::getAccessToken();
			$this->access = [
				'fixinglist' => '168329090',
				'ustabor' => '168341712',
			];
			$this->mobileAccess = [
				'fixinglist' => [
					'android' => '',
					'ios' => '',
				],
				'ustabor' => [
					'android' => '',
					'ios' => '',
				],
			];

			$this->sites = [
				'fixinglist' => [
					'www.fixinglist.com',
					'auto.fixinglist.com',
					'tech.fixinglist.com',
					'home.fixinglist.com',
				],
				'ustabor' => [
					'www.ustabor.uz',
					'auto.ustabor.uz',
					'tech.ustabor.uz',
					'home.ustabor.uz',
				],
			];

			$this->prefix = [
				'tech',
				'auto',
				'home',
			];
			return $next($request);
		});
	}

	/**
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index($query = false, Request $request) {
		$routeName = $request->route()->getName();
		$routePrefix = str_replace('/', '', $request->route()->getPrefix());

		$segments = $this->getSegments($query, $this->sites[$routePrefix][0], $routePrefix);
		//var_dump($query);exit;
		switch ($routeName) {
			case 'ustabor.requests':
			case 'ustabor.requests.query':
				$urlPrefix = 'ustabor';
				$apiUrl = 'https://api.ustabor.uz/sync/request/statistics/';
				$GAId = 'ga:168341712';
				break;
			case 'fixinglist.requests':
			case 'fixinglist.requests.query':
				$urlPrefix = 'fixinglist';
				$apiUrl = 'https://api.fixinglist.com/sync/request/statistics/';
				$GAId = 'ga:168329090';
				break;
		}

		$siteId = self::getSiteId($query);
		$data = self::siteApi($apiUrl, [
			'site_id' => $siteId,
			'status' => 'actual'
		]);
		//var_dump($data);exit;

		return view('requests.index')->with([
			'token' => GoogleAnalytics::getAccessToken(),
			'siteData' => $data,
			'siteId' => $siteId,
			'GAId' => $GAId,
			'urlPrefix' => $urlPrefix,
			//'token' => $this->token['access_token'],
			//'id' => $id,
			'segments' => $segments,
			//'url_list' => $url_list,
		]);
	}

	public function api($query = false, Request $request) {
		$routeName = $request->route()->getName();

		switch ($routeName) {
			case 'ustabor.requests.api':
				$apiUrl = 'https://api.ustabor.uz/sync/request/statistics/';
				$GAId = 'ga:168341712';
				break;
			case 'fixinglist.requests.api':
				$apiUrl = 'https://api.fixinglist.com/sync/request/statistics/';
				$GAId = 'ga:168329090';
				break;
		}

		$params = $request->query();
		//$params['site_id'] = self::getSiteId($query);
		$params['status'] = 'actual';
		$data = self::siteApi($apiUrl, $params);

		echo json_encode($data);
		exit;
	}

	protected function getSegments(string $query, string $site, string $routePrefix) {
		if (!empty($query)) {
			$segments = 'sessions::condition::ga:pagePath=~^' . $query . '\;condition::!ga:pagePath=~^www..*';
		} else {
			$segments = 'sessions::condition::ga:pagePath=~^' . $site . '/..*\,ga:pagePath=~^' . str_replace('www.', '', $site) . '/..*';
			$segments .= '\;condition::!';
			foreach ($this->sites[$routePrefix] as $key => $domain) {
				if ($domain !== $site) {
					if ($key + 1 == count($this->sites[$routePrefix])) {
						$segments .= 'ga:pagePath=~^' . $domain . '..*';
					} else {
						$segments .= 'ga:pagePath=~^' . $domain . '..*\,';
					}
				}
			}
		}
		return $segments;
	}

	protected static function getSiteId(string $query) {
		switch ($query) {
			case 'auto':
				return 2;
			case 'tech':
				return 3;
			case 'home':
				return 4;
			default:
				return 1;
		}
	}

	private static function siteApi($url, array $params = [], array $headers = []) {
		$ch = curl_init();
		$url .= '?' . http_build_query($params);
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, [
			//'Content-Type: text/xml;charset=UTF-8',
			'Api-Access-Key: PEtQOkwmJyY5NiVgIn18eXYrJmdUY3JMX3B'
		]));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$s = curl_exec($ch);
		curl_close($ch);
		//var_dump($url, $s);exit;

		$result = json_decode($s);

		return $result->result;
	}


}
