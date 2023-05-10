<?php
//  BARMAZ erp system
//  Copyright (c) BARMAZ Group
//  Web: https://BARMAZ.ru/
//  Commercial license https://BARMAZ.ru/article/litsenzionnoe-soglashenie.html
//  THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
//  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
//  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
//  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
//  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
//  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
//  Revision: 135 (2023-05-10 14:11:23)
// 

defined('_BARMAZ_VALID') or die("Access denied");

// Check server settings
//include_once PATH_PACKAGES.DS.'Based'.DS.'core'.DS.'Util.php';
include_once PATH_PACKAGES.DS.'loader.php';
include_once PATH_CONFIG.DS.'packages.php';
Util::checkServerSettings(true);
/*
// Include loader

// CMS packages configuration
loader::usePackage("cms_core");
loader::usePackage("database");
loader::usePackage("mvc_base");
loader::usePackage("sprav");
loader::usePackage("tools");
loader::usePackage("custom");
loader::usePackage("tools".DS."SMSProviders");
loader::usePackage("tools".DS."SNProviders");
*/

final class DatabaseConfig {
	public static $dbHost			= '';
	public static $dbPort			= '';
	public static $dbName			= '';
	public static $dbUser			= '';
	public static $dbPassword	= '';
	public static $dbPrefix		= '';
	public static	$dbSecret		= '';
}

class siteConfig {
	public static $debugMode		= false;
	public static $defaultLanguage	= 'ru';
	public static $siteDisabled		= false;
	public static $siteTemplate		= '';
	public static $def_account_bonus	= 0;
}
class seoConfig {
	public static $sefMode			= false;
//	public static $strictSefMode	= false;
}
// Set PHP interpreter parameters
ini_set('display_errors',0);		// Show errors
error_reporting(0);				// Report all errors

if (version_compare(phpversion(), '5.3.0', '<') == true)	error_reporting(E_ALL|E_STRICT);
else error_reporting(E_ALL|E_STRICT|E_DEPRECATED);

ini_set('memory_limit','64M');		// Limit memory to 64 megabytes
ini_set('max_execution_time','300');		// Limit memory to 64 megabytes

// Initialize system debugger
Debugger::createInstance();

// Setup locale (core config contains default language parameter)
siteConfig::$defaultLanguage = Request::get('lang','ru');
Util::setupLocale();

// Setup system localization
Text::setLanguage(siteConfig::$defaultLanguage);
Text::parseBaseIni('debug');
Text::parseBaseIni('install');

// Installer itself
require_once PATH_INSTALL.DS.'Installer.php';

Installer::getInstance()->render();

?>