<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class GoogleAnalytics {

    var $ga_api_applicationName;
    var $ga_api_clientId;
    var $ga_api_clientSecret;
    var $ga_api_redirectUri;
    var $ga_api_developerKey;
    var $ga_api_profileId;
    var $client;
    var $access_token_ready;

    public function __construct($params) {
        require_once 'google-api-php-client/src/Google_Client.php';
        require_once 'google-api-php-client/src/contrib/Google_AnalyticsService.php';

        $this->ga_api_applicationName = $params['applicationName'];
        $this->ga_api_clientId = $params['clientID'];
        $this->ga_api_clientSecret = $params['clientSecret'];
        $this->ga_api_redirectUri = $params['redirectUri'];
        $this->ga_api_developerKey = $params['developerKey'];
        $this->ga_api_profileId = $params['profileID'];

        session_start();

        $this->client = new Google_Client();

        $this->client->setApplicationName($this->ga_api_applicationName);
        $this->client->setClientId($this->ga_api_clientId);
        $this->client->setClientSecret($this->ga_api_clientSecret);
        $this->client->setRedirectUri($this->ga_api_redirectUri);
        $this->client->setDeveloperKey($this->ga_api_developerKey);
        $this->client->setScopes(array('https://www.googleapis.com/auth/analytics.readonly'));

        if (isset($_SESSION['token'])) {
            $this->client->setAccessToken($_SESSION['token']);
        }

        $this->client->setUseObjects(true);

        if (isset($_GET['code'])) {
            $this->client->authenticate();
            $_SESSION['token'] = $this->client->getAccessToken();
            $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
        }

        $this->access_token_ready = $this->client->getAccessToken();
        if (!$this->access_token_ready) {
            $authUrl = $this->client->createAuthUrl();
            print "<a class='login' href='$authUrl'>Connect Me!</a>";
        }
    }

    public function logout() {
        unset($_SESSION['token']);
        die('Logged Out'
                . '<br>'
                . '<a href="http://localhost/sayuri-info-admin/index.php/welcome/googleAnalytics">Back</a>');
    }

    /**
     * 
     * @param type $type Type of result {users,newUsers,percentNewSessions,sessions,bounces}
     * https://developers.google.com/analytics/devguides/reporting/core/dimsmets
     * Use without ga:
     * @return int
     */
    public function get_total($type = 'users') {
        if ($this->access_token_ready) {
            $analytics = new Google_AnalyticsService($this->client);
            try {

                $optParams = array(
                    'max-results' => '100');
                $results = $analytics->data_ga->get('ga:' . $this->ga_api_profileId, '2015-02-18', '2016-01-15', 'ga:' . $type, $optParams);

                $ga_total = 0;
                if (count($results->getRows()) > 0) {
                    foreach ($results->getRows() as $row) {
                        foreach ($row as $cell) {
                            $ga_total = $cell;
                        }
                    }
                } else {
                    return 0;
                }
                return $ga_total;
            } catch (Exception $ex) {
                $error = $ex->getMessage();
                die($error);
            }
        }
    }

    /**
     * 
     * @param type $dimension Type of dimension to filter {browser,browserVersion,operatingSystem,operatingSystemVersion,isMobile,isTablet,mobileDeviceBranding,mobileDeviceModel,mobileInputSelector,mobileDeviceInfo,mobileDeviceMarketingName,deviceCategory}
     * @param type $type Type of result {users,newUsers,percentNewSessions,sessions,bounces}
     * https://developers.google.com/analytics/devguides/reporting/core/dimsmets
     */
    public function get_dimensions($dimension = 'browser', $type = 'users') {
        if ($this->access_token_ready) {
            $analytics = new Google_AnalyticsService($this->client);
            try {
                $optParams = array(
                    'dimensions' => 'ga:' . $dimension,
                    'max-results' => '100');
                $results = $analytics->data_ga->get('ga:' . $this->ga_api_profileId, '2015-02-18', '2016-01-15', 'ga:' . $type, $optParams);

                $ga_dimension = array();
                if (count($results->getRows()) > 0) {
                    foreach ($results->getRows() as $row) {
                        $ga_dimension[$row[0]] = $row[1];
                    }
                } else {
                    return NULL;
                }
                return $ga_dimension;
            } catch (Exception $ex) {
                $error = $ex->getMessage();
                die($error);
            }
        }
    }

    public function get_profile_info() {
        if ($this->access_token_ready) {
            $analytics = new Google_AnalyticsService($this->client);
            try {
                $optParams = array(
                    'max-results' => '1');
                $results = $analytics->data_ga->get('ga:' . $this->ga_api_profileId, '2015-02-18', '2016-01-15', 'ga:users', $optParams);

                $profileInfo = $results->getProfileInfo();

                $html = <<<HTML
<pre>
Account ID               = {$profileInfo->getAccountId()}
Web Property ID          = {$profileInfo->getWebPropertyId()}
Internal Web Property ID = {$profileInfo->getInternalWebPropertyId()}
Profile ID               = {$profileInfo->getProfileId()}
Table ID                 = {$profileInfo->getTableId()}
Profile Name             = {$profileInfo->getProfileName()}
</pre>
HTML;

                return $html;
            } catch (Exception $ex) {
                $error = $ex->getMessage();
                die($error);
            }
        }
    }
}
