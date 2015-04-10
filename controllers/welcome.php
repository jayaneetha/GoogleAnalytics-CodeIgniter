<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Welcome extends CI_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     * 	- or -  
     * 		http://example.com/index.php/welcome/index
     * 	- or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index() {
        
        $this->load->view('welcome_message');
    }

    public function googleAnalytics() {

        $this->config->load('ga_api');
        $ga_params = array(
            'applicationName' => $this->config->item('ga_api_applicationName'),
            'clientID' => $this->config->item('ga_api_clientId'),
            'clientSecret' => $this->config->item('ga_api_clientSecret'),
            'redirectUri' => $this->config->item('ga_api_redirectUri'),
            'developerKey' => $this->config->item('ga_api_developerKey'),
            'profileID' => $this->config->item('ga_api_profileId')
        );

        $this->load->library('GoogleAnalytics', $ga_params);

        $data = array(
            'users' => $this->googleanalytics->get_total('users'),
            'sessions' => $this->googleanalytics->get_total('sessions'),
            'browsers' => $this->googleanalytics->get_dimensions('browser','sessions'),
            'operatingSystems' => $this->googleanalytics->get_dimensions('operatingSystem','sessions'),
            'profileInfo' => $this->googleanalytics->get_profile_info()
        );

        //$this->googleanalytics->some_function();

        $this->load->view('welcome_message', $data);
    }

    public function logout() {
        $this->config->load('ga_api');
        $ga_params = array(
            'applicationName' => $this->config->item('ga_api_applicationName'),
            'clientID' => $this->config->item('ga_api_clientId'),
            'clientSecret' => $this->config->item('ga_api_clientSecret'),
            'redirectUri' => $this->config->item('ga_api_redirectUri'),
            'developerKey' => $this->config->item('ga_api_developerKey'),
            'profileID' => $this->config->item('ga_api_profileId')
        );
        $this->load->library('GoogleAnalytics', $ga_params);
        $this->googleanalytics->logout();
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */