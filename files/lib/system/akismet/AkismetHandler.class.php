<?php
namespace wcf\system\akismet;

use wcf\data\package\PackageCache;
use wcf\system\cache\builder\ApplicationCacheBuilder;
use wcf\system\exception\UserInputException;
use wcf\util\HTTPRequest;
use wcf\util\StringUtil;

/**
 * Handles Akismet support.
 * 
 * @author		Markus Bartz <roul@codingcorner.info>
 * @copyright	2013 Markus Bartz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		info.codingcorner.wcf.akismet
 * @subpackage	system.akismet
 * @category	Community Framework
 */
class AkismetHandler extends SingletonFactory {
	/**
	 * API key
	 * @var string
	 */
	protected $apiKey = '';

	/**
	 * user agent
	 * @var string
	 */
	protected $userAgent = '';

	/**
	 * application cache
	 * @var array
	 */
	protected $applicationURL = null;

	// API constants
	const API_BASE_DOMAIN = 'rest.akismet.com';
	const API_VERSION = '1.1';

	// API actions
	const ACTION_VERIFY_KEY = 'verify-key';
	const ACTION_CHECK = 'comment-check';
	const ACTION_SUBMIT_SPAM = 'submit-spam';
	const ACTION_SUBMIT_NOSPAM = 'submit-ham';

	// submit types
	const SUBMIT_SPAM = 0;
	const SUBMIT_NOSPAM = 1;

	// reply codes (see <http://akismet.com/development/api/>)
	const REPLY_KEY_VALID = 'valid';
	const REPLY_KEY_INVALID = 'invalid';

	const REPLY_CHECK_SPAM = 'true';
	const REPLY_CHECK_NOSPAM = 'false';
	const REPLY_CHECK_ERROR = 'invalid';

	const REPLY_SUBMIT_SUCCESS = 'Thanks for making the web a better place.';

	// error codes
	const ERROR_HTTP = 0;

	/**
	 * @see	wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->apiKey = AKISMET_API_KEY;

		$packageID = PackageCache::getInstance()->getPackageID('info.codingcorner.wcf.akismet');
		$packageVersion = PackageCache::getInstance()->getPackage($packageID)->packageVersion;

		$this->userAgent = 'WoltLab Community Framework/'.WCF_VERSION.' | Akismet/'.$packageVersion;

		$appCache = ApplicationCacheBuilder::getInstance()->getData();

		$this->applicationURL = $appCache['application'][$appCache['primary']]->getPageURL();
	}

	/**
	 * Generates the API url for the given action
	 * 
	 * @param string $action
	 * @return string
	 */
	protected function getApiURL($action) {
		// veryfy-key has no API key in the domain!
		if ($action == self::ACTION_VERIFY_KEY) {
			return 'http://'.self::API_BASE_DOMAIN.'/'.self::API_VERSION.'/'.$action;
		}

		return 'http://'.$this->apiKey.'.'.self::API_BASE_DOMAIN.'/'.self::API_VERSION.'/'.$action;
	}

	/**
	 * Makes the requests to the akismet API.
	 * 
	 * @param string $action
	 * @param array $params
	 * @return string
	 */
	protected function makeRequest($action, array $params) {
		$url = $this->getApiURL($action);

		$request = new HTTPRequest($url, array(), $params);
		$request->addHeader('User-Agent', $this->userAgent);

		try {
			$request->execute();
			$reply = $request->getReply();
			$akismetResponse = explode("\n", $reply['body']);

			return StringUtil::trim($akismetResponse[0]);
		}
		catch (SystemException $e) {
			return self::ERROR_HTTP;
		}
	}

	/**
	 * Verifies the the given API key
	 * 
	 * @param string $apiKey
	 * @return boolean
	 */
	public function verifyAPIKey($apiKey) {
		$response = $this->makeRequest(self::ACTION_VERIFY_KEY, array(
			'key' => $apiKey,
			'blog' => $this->applicationURL,
		));

		return ($response == self::REPLY_KEY_VALID);
	}

	public function check(/* options */) {
		// TODO: implement stub method
	}

	public function submit($type /* options */) {
		// TODO: implement stub method
	}
}
