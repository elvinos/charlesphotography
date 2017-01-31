<?php
if (!defined('ABSPATH'))
    exit();

if (!class_exists('OPS_Google_API')) {

    class OPS_Google_API
    {

        public $authUrl = '';

        public $client;

        public $service;

        public function __construct()
        {

            include_once('autoload.php');
            $config = new Google_Config();
            $config->setCacheClass('Google_Cache_Null');

            // settings
            $this->client = new Google_Client($config);
            $this->client->setScopes('https://www.googleapis.com/auth/webmasters.readonly'); // https://developers.google.com/webmaster-tools/v3/how-tos/authorizing
            $this->client->setAccessType('offline');
            $this->client->setApplicationName('Off Page SEO');
            $this->client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
            $this->client->setClientId('547283294552-qdtvvpcadvdomga6dh0re3q185t5v4gv.apps.googleusercontent.com');
            $this->client->setClientSecret('7X8yC_eTlMhjFZLXhqgv2kOh');

            $this->authUrl = $this->client->createAuthUrl(); // set auth url for later use

        }

        public function authenticate_google_api()
        {

            $settings = Off_Page_SEO::ops_get_settings();
            $authorization_code = $settings['google_api']['authorization_code'];
            try {
                $this->client->authenticate($authorization_code); // authorize myself
            } catch (Exception $e) {
                Off_Page_SEO::ops_create_log_entry('google_api','error', 'Authentication error: ' . $e->getMessage());
                return;
            }

            $access_tokens = $this->client->getAccessToken(); // get access token

            if (strlen($access_tokens) < 2) {
                Off_Page_SEO::ops_create_log_entry('google_api', 'error',__('The access token appears to be in the wrong format.','off-page-seo'));
            }
            $this->cache_access_tokens($access_tokens);
        }

        public function connect_to_google_api($client)
        {

            $access_tokens = json_decode($this->get_cached_tokens());
            $this->client->setAccessToken($this->get_cached_tokens());
            return;

            if ('' != $access_tokens->refresh_token) {
                try {
                    // Refresh the token
                    $response = $client->client->refreshToken($access_tokens->refresh_token);

                    // Check response
                    if ('' != $response) {
                        $response = json_decode($response);
                        // Check if there is an access_token
                        if (isset($response->access_token)) {

                            // Set access_token
                            $this->setAccessToken($this->get_cached_tokens());

                        } else {
                            Off_Page_SEO::ops_create_log_entry('google_api', 'error', __('Tokens seem to have been refreshed, but it does not contain access token.','off-page-seo'));
                        }
                    } else {
                        Off_Page_SEO::ops_create_log_entry('google_api', 'error', __('Token was not refreshed successfully.','off-page-seo'));
                    }
                } catch (Exception $e) {
                    Off_Page_SEO::ops_create_log_entry('google_api', 'error', __('We could not connect to Google API: ','off-page-seo') . $e);
                }
            } else {
                Off_Page_SEO::ops_create_log_entry('google_api', 'error', "We are trying to connect to Google API, but we don't have access tokens:"  . $this->get_cached_tokens());
            }
        }

        public function get_cached_tokens()
        {
            return Off_Page_SEO::ops_get_option('ops_google_api_access_token');
        }

        public function cache_access_tokens($tokens)
        {
            Off_Page_SEO::ops_update_option('ops_google_api_access_token', $tokens);
        }

        function process_query_response($response)
        {
            $output = array();
            foreach ($response as $row) {
                $output[$row->keys[0]]['clicks'] = $row->clicks;
                $output[$row->keys[0]]['ctr'] = $row->ctr;
                $output[$row->keys[0]]['position'] = $row->position;
                $output[$row->keys[0]]['impressions'] = $row->impressions;
            }
            return $output;
        }


    }
}
