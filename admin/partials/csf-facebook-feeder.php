<?php

class Csf_Facebook_Feeder
{
    const CSF_FACEBOOK_PROTOCOL = 'https://graph.facebook.com/';
    const CSF_FACEBOOK_ENDPOINT = '?fields=id,name,feed{full_picture,type,message,description,link,status_type,created_time}&access_token=';
    private $facebook_account_id;
    private $facebook_token;
    private $facebook_feeds;

    public function __construct($facebook_account_id, $facebook_token)
    {
        $this->facebook_account_id = $facebook_account_id;
        $this->facebook_token = $facebook_token;
        if(!empty($this->facebook_account_id) && !empty($this->facebook_token)){
            add_action( 'rest_api_init',            array($this,'csf_register_routes_facebook'));
        }else {
            add_action( 'rest_api_init',            array($this,'csf_register_routes_error'));
        }
    }

    public function csf_register_routes_facebook()
    {
        register_rest_route( 'rest-civic/v1', 'facebook', array(
                'methods' => 'GET',
                'callback' => array($this,'csf_restFacebookFeeds'),
            )
        );
    }

    public function csf_register_routes_error()
    {
        register_rest_route( 'rest-civic/v1', 'facebook', array(
                'methods' => 'GET',
                'callback' => array($this,'csf_rest_feeds_error'),
            )
        );
    }

    public function csf_rest_feeds_error()
    {
        return rest_ensure_response(array(
            'error' => 'Please check that your Facebook Account ID and Facebook Token are valid'
        ));
    }

    public function csf_restFacebookFeeds($source = 'rest')
    {
        $data = array();
        $filteredData = [];

        $response = wp_remote_get(self::CSF_FACEBOOK_PROTOCOL. $this->facebook_account_id. self::CSF_FACEBOOK_ENDPOINT . $this->facebook_token);
        if (is_array($response) && !is_wp_error($response)) {
            $body = wp_remote_retrieve_body( $response );

            // Check for error
            if ( is_wp_error( $data ) ) {
                return array(
                    'error' => 'Please check that your Facebook Account ID and Facebook Token are valid'
                );
            }
            $data = json_decode($body, true);
            foreach ($data as $item) {
                if ( !empty ($item ) ) {
                    array_push($filteredData, $item);
                }
            }
        }else {
            return array(
                'error' => 'Please check that your Facebook Account ID and Facebook Token are valid'
            );
        }
        $arrayToReturn = array();

        foreach ($filteredData as $item) {
            if ( isset($item['code']) ) {
                if($item['code'] != 200){
                    return array(
                        'error' => 'Please check that your Facebook Account ID and Facebook Token are valid'
                    );
                }
            }else{
                array_push($arrayToReturn, $item);
            }
        }
        $this->facebook_feeds = json_encode($arrayToReturn);
        if($source == 'check'){
            return json_encode($arrayToReturn);
        }
        return rest_ensure_response($arrayToReturn);
    }
}