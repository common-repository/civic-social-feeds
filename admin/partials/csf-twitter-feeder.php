<?php

class Csf_Twitter_Feeder
{
    const CSF_TWITTER_AUTH_URL = 'https://api.twitter.com/oauth2/token';
    const CSF_TWITTER_ENDPOINT = 'https://api.twitter.com/1.1/statuses/user_timeline.json?tweet_mode=extended';
    private $twitter_api_key;
    private $twitter_api_secret;
    private $twitter_account;

    public function __construct($twitter_api_key, $twitter_api_secret, $twitter_account)
    {
        $this->twitter_api_key = $twitter_api_key;
        $this->twitter_api_secret = $twitter_api_secret;
        $this->twitter_account = $twitter_account;
        if(!empty($this->twitter_api_key) && !empty($this->twitter_api_secret) && !empty($this->twitter_account)){
            add_action( 'rest_api_init',            array($this,'csf_register_routes_twitter'));
        }else {
            add_action( 'rest_api_init',            array($this,'csf_register_routes_error'));
        }

    }

    public function csf_register_routes_twitter()
    {
        register_rest_route( 'rest-civic/v1', 'twitter', array(
                'methods' => 'GET',
                'callback' => array($this,'csf_restTwitterFeeds'),
            )
        );
    }

    public function csf_register_routes_error()
    {
        register_rest_route( 'rest-civic/v1', 'twitter', array(
                'methods' => 'GET',
                'callback' => array($this,'csf_rest_feeds_error'),
            )
        );
    }

    public function csf_rest_feeds_error()
    {
        return rest_ensure_response(array(
            'error' => 'Please check that your Twitter API key - Twitter API secret and Twitter Account name are valid'
        ));
    }

    public function csf_restTwitterFeeds($source = 'rest')
    {
        // auth parameters
        $api_key = $this->twitter_api_key; // Consumer Key (API Key)
        $api_secret = $this->twitter_api_secret; // Consumer Secret (API Secret)

        // what we want?
        $data_username = $this->twitter_account; // username
        $data_count = 20; // number of tweets

        // get api access token
        $api_credentials = base64_encode($api_key.':'.$api_secret);

        $auth_headers = 'Authorization: Basic '.$api_credentials."\r\n".
            'Content-Type: application/x-www-form-urlencoded;charset=UTF-8'."\r\n";

        $auth_context = stream_context_create(
            array(
                'http' => array(
                    'header' => $auth_headers,
                    'method' => 'POST',
                    'content'=> http_build_query(array('grant_type' => 'client_credentials')),
                )
            )
        );

        $auth_response = json_decode(@file_get_contents(self::CSF_TWITTER_AUTH_URL, 0, $auth_context), true);
        if(empty($auth_response)){
            return array(
                'error' => 'Please check that your Twitter API key - Twitter API secret and Twitter Account name are valid'
            );
        }
        $auth_token = $auth_response['access_token'];

        // get tweets
        $data_context = stream_context_create( array( 'http' => array( 'header' => 'Authorization: Bearer '.$auth_token."\r\n", ) ) );

        $data = json_decode(file_get_contents(self::CSF_TWITTER_ENDPOINT.'&count='.$data_count.'&screen_name='.urlencode($data_username), 0, $data_context), true);

        $filteredData = [];

        //filter response for Retweets
        foreach ($data as $item) {
            if ( empty ($item['retweeted_status'] ) ) {
                array_push($filteredData, $item);
            }
        }

        if($source == 'check'){
            return json_encode($filteredData);
        }
        return rest_ensure_response($filteredData);
    }
}
