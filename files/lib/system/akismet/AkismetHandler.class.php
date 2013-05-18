<?php
namespace wcf\system\akismet;

use wcf\data\package\PackageCache;
use wcf\system\exception\UserInputException;
use wcf\util\HTTPRequest;

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
	const ERROR_TIMEOUT = 0;

	/**
	 * @see	wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$this->apiKey = AKISMET_API_KEY;

		$packageID = PackageCache::getInstance()->getPackageID('info.codingcorner.wcf.akismet');
		$packageVersion = PackageCache::getInstance()->getPackage($packageID)->packageVersion;

		$this->userAgent = 'WoltLab Community Framework/'.WCF_VERSION.' | Akismet/'.$packageVersion;
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

	protected function makeRequest(/* options */) {
		// TODO: implement stub method
	}

	public function verifyAPIKey($apiKey) {
		// TODO: implement stub method
	}

	public function check(/* options */) {
		// TODO: implement stub method
	}

	public function submit($type /* options */) {
		// TODO: implement stub method
	}
}
