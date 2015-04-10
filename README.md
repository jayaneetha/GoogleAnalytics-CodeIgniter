# GoogleAnalytics-CodeIgniter
CodeIgniter Library to connect Google Analytics API.

##Installation
1. Copy `config/ga_api.php` file to the `config` folder of your application.
2. Copy `GoogleAnalytics.php` file and `google-api-php-client` folder to the `library` folder of your application.

##Usage
1. Load the config file to your controller
  `$this->config->load('ga_api');`
2. Load the library & config values
```
$ga_params = array(
          'applicationName' => $this->config->item('ga_api_applicationName'),
           'clientID' => $this->config->item('ga_api_clientId'),
          'clientSecret' => $this->config->item('ga_api_clientSecret'),
          'redirectUri' => $this->config->item('ga_api_redirectUri'),
           'developerKey' => $this->config->item('ga_api_developerKey'),
           'profileID' => $this->config->item('ga_api_profileId')
       );
 $this->load->library('GoogleAnalytics', $ga_params);
 ```
### Get Total Values
 * `$this->googleanalytics->get_total('users')` returns total number of users visited to your page.
 * `$this->googleanalytics->get_total({metric})` {metric} = users, newUsers, percentNewSessions, sessionsPerUser.

### Get Dimensional Values
* `$this->googleanalytics->get_dimensions('browser','users')` returns array of number of users visited to your page with browsers.
* `$this->googleanalytics->get_dimensions({dimension},{matric})` 
 {metric} = users, newUsers, percentNewSessions, sessionsPerUser. 
 {dimension} = browser, browserVersion, operatingSystem, operatingSystemVersion, mobileDeviceBranding, mobileDeviceModel, mobileInputSelector, mobileDeviceInfo, mobileDeviceMarketingName, deviceCategory.

[Dimensions & Metrics Reference](https://developers.google.com/analytics/devguides/reporting/core/dimsmets)

### Logout
`$this->googleanalytics->logout();` Destroys the session.
