<?php
defined('BASEPATH') or exit('No direct script access allowed');
define('HR_EMAIL', 'akshay.patil@neosao.in');
define('HR_EMAIL_PASSWORD', 'Akshayp460@neosao');
define('SESS_KEY', 'MyVegizTesting');
define('USR_SESS_KEY', 'MyVegizTestingUser');
define('mailId', 'neosaotesting@neosao.com');
define('mailpassword', 'neosao@123');
define('AppName', 'MyVegiz');
defined('modules_file_path') or define('modules_file_path', "assets/rights/modules.json");
define('FIREBASE_ACCESS_KEY', 'AAAAMmGZrSo:APA91bEY8KswFbqDCnBGxeR0SJk2jrJkutZSDQ2AECrFaBoU7kkoiokhKs7hVqRCYyc68ERpCaDWj-zCA2P-ycA_So3Nb52M7vaX3YWJrFogn_LJNyrdIe9-7LuoIQtTrvgMrgocGUhV');
//define('FIREBASE_ACCESS_KEY', 'AAAA1vIBpSk:APA91bGOMnnKJ2DlF1UraRY_nQZ09DljykMk72f0_NTurbZPP4yyBoDh3_rSR0qWatdHTxtAFCtASWLGQy7tBQ_gn5zE4Ufg_5jtZh6V4g8mbbcFk1MnL_4rHhkiNGkO28vBofj2bM1o');
//define('FIREBASE_ACCESS_KEY', 'AAAAMmGZrSo:APA91bEY8KswFbqDCnBGxeR0SJk2jrJkutZSDQ2AECrFaBoU7kkoiokhKs7hVqRCYyc68ERpCaDWj-zCA2P-ycA_So3Nb52M7vaX3YWJrFogn_LJNyrdIe9-7LuoIQtTrvgMrgocGUhV');
define('FIRESTORE_PROJECT_ID', 'myvegiz-a3615');
define('FIREBASE_KEY', 'AIzaSyAkm6u4b3Q4kzUaUlhD02Xe61oU5-iIhx0');
define('PLACE_API_KEY', 'AIzaSyDLl0pyvxv4D55821seE3gQpUCAPHs06SY');
//test mode
define("CASHFREE_MODE", "TEST");
//TEST KEYS
define('CASHFREE_TEST_CLIENT_ID', '4901049bb1d0599867dbd845b01094');
define('CASHFREE_TEST_CLIENT_SECRET', 'TESTa85e4642e2943b013d84ad67c4acfade2f4ff388');
define("CASHFREE_TEST_URL", "https://sandbox.cashfree.com/pg/orders");
//LIVE KEYS
define("CASHFREE_LIVE_URL", "https://api.cashfree.com/pg/orders");
define('CASHFREE_LIVE_CLIENT_ID', '943291d7404214a5d49878be292349');
define('CASHFREE_LIVE_CLIENT_SECRET', '1181e196d15a1ff67621169563c4b05cde69d718');
//Hooks
define('CASHFREE_VENDOR_WEBHOOK_URL', 'https://myvegiz.com/live/Apiv1_8/Food/Api/verifyPayment');
define('CASHFREE_WEBHOOK_URL', 'https://myvegiz.com/live/Apiv1_8/Api/verifyPayment');
define('SMSKEY', '6b72b8710cd3291642509ba0bf236f');
define('SENDERID', 'TBTSGN');
/* |-------------------------------------------------------------------------- | Display Debug backtrace |-------------------------------------------------------------------------- | | If set to TRUE, a backtrace will be displayed along with php errors. If | error_reporting is disabled, the backtrace will not display, regardless | of this setting | */
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', TRUE);
/* |-------------------------------------------------------------------------- | File and Directory Modes |-------------------------------------------------------------------------- | | These prefs are used when checking and setting modes when working | with the file system.  The defaults are fine on servers with proper | security, but you may wish (or even need) to change the values in | certain environments (Apache running a separate process for each | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should | always be used to set the mode correctly. | */
defined('FILE_READ_MODE') or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') or define('DIR_WRITE_MODE', 0755);

/* |-------------------------------------------------------------------------- | File Stream Modes |-------------------------------------------------------------------------- | | These modes are used when working with fopen()/popen() | */
defined('FOPEN_READ') or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') or define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') or define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/* |-------------------------------------------------------------------------- | Exit Status Codes |-------------------------------------------------------------------------- | | Used to indicate the conditions under which the script is exit()ing. | While there is no universal standard for error codes, there are some | broad conventions.  Three such conventions are mentioned below, for | those who wish to make use of them.  The CodeIgniter defaults were | chosen for the least overlap with these conventions, while still | leaving room for others to be defined in future versions and user | applications. | | The three main conventions used for determining exit status codes | are as follows: | |    Standard C/C++ Library (stdlibc): |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html |       (This link also contains other GNU-specific conventions) |    BSD sysexits.h: |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits |    Bash scripting: |       http://tldp.org/LDP/abs/html/exitcodes.html | */
defined('EXIT_SUCCESS') or define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') or define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') or define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') or define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') or define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') or define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') or define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') or define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') or define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') or define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
