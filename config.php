<?php
if (!isset($_SERVER["SERVER_NAME"]) || strpos($_SERVER["SERVER_NAME"], "sahfor.com") === false) {
    //dev / not sahfor.com (might have to think about white-label some day)
    ini_set("error_reporting", "true");
    error_reporting(E_ALL|E_STRICT);
    ini_set('display_errors',1);

    return (object)array(
        'env' => 'dev',
        'rootDir' => '/connector',
        'siteHost' => 'localhost:8777/connector',
        'siteProtocol' => 'http://',
        'dbHost' => '127.0.0.1',
        'dbName' => 'sahfor',
        'dbUser' => 'sahfor',
        'dbPass' => 'sahfor',
        'apiUser' => 'steve',
        'apiPass' => 'steve11',
        'apiHost' => 'http://localhost:8777/connector/api',
        'oauthHost' => 'http://localhost:8777/sso',
        'oauthClientId' => 'mytestclientid',
        'oauthClientSecret' => 'steve11',
        'oauthDiscoveryDoc' => 'http://localhost:8777/sso/.well-known/openid-configuration',
    );
}
else {
    //production / sahfor.com and subdomains
    ini_set('session.cookie_domain','.sahfor.com');
    ini_set("error_reporting", "true");
    error_reporting(E_ALL|E_STRICT);
    ini_set('display_errors',0);

    return (object)array(
        'env' => 'prod',
        'rootDir' => '/connector',
        'siteHost' => 'sahfor.com/connector',
        'siteProtocol' => 'http://',
        'dbHost' => '127.0.0.1',
        'dbName' => 'sahfor',
        'dbUser' => 'sahfor',
        'dbPass' => 'sahfor',
        'apiUser' => 'steve',
        'apiPass' => 'steve11',
        'apiHost' => 'http://api.sahfor.com',
        'oauthHost' => 'http://account.sahfor.com',
        'oauthClientId' => 'mytestclientid',
        'oauthClientSecret' => 'steve11',
        'oauthDiscoveryDoc' => 'account.sahfor.com/.well-known/openid-configuration',
    );
}
?>