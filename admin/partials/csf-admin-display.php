<?php

$data = $this->csf_getData();

$this->cron_schedule = isset($data['cron_schedule']) ? urlencode($data['cron_schedule']) : 'hourly';
$cron_scheduler = new Csf_Cron_Scheduler($this->cron_schedule);
$cron_scheduler->csf_schedule_job();

//TWITTER SETTINGS
$this->twitter_api_key = isset($data['twitter_api_key']) ? urlencode(sanitize_text_field($data['twitter_api_key'])) : '';
$this->twitter_api_secret = isset($data['twitter_api_secret']) ? urlencode(sanitize_text_field($data['twitter_api_secret'])) : '';
$this->twitter_account_name = isset($data['twitter_account_name']) ? urlencode(sanitize_text_field($data['twitter_account_name'])) : '';
if(!empty($this->twitter_api_key) && !empty($this->twitter_api_secret) && !empty($this->twitter_account_name) ){
    $twitter_feeder = new Csf_Twitter_Feeder($this->twitter_api_key, $this->twitter_api_secret, $this->twitter_account_name);
    $api_response_twitter = $twitter_feeder->csf_restTwitterFeeds('check');

}


//FACEBOOK SETTINGS
$this->facebook_account_id = isset($data['facebook_account_id']) ? urlencode(sanitize_text_field($data['facebook_account_id'])) : '';
$this->facebook_token = isset($data['facebook_token']) ? urlencode(sanitize_text_field($data['facebook_token'])) : '';
if(!empty($this->facebook_account_id) && !empty($this->facebook_token)  ){
    $facebook_feeder = new Csf_Facebook_Feeder($this->facebook_account_id, $this->facebook_token);
    $api_response_facebook = $facebook_feeder->csf_restFacebookFeeds('check');
}

$not_ready_twitter = (empty($data['twitter_api_key']) || empty($data['twitter_api_secret']) || empty($data['twitter_account_name']) || empty($api_response_twitter) || isset($api_response_twitter['error']));
$not_ready_facebook = (empty($data['facebook_account_id']) || empty($data['facebook_token'])  || empty($api_response_facebook) || isset($api_response_facebook['error']));

?>

<div class="wrap">

    <h1 class="csf-h1"><?php _e('Civic Social Feeds Settings', 'csf'); ?></h1>

    <form id="csf-admin-form" class="postbox">

        <div class="form-group inside">

            <?php

            ?>

            <h3>
                <?php echo $this->csf_getStatusIcon(!$not_ready_twitter); ?>
                <?php _e('Twitter API Settings', 'csf'); ?>
            </h3>

            <?php if ($not_ready_twitter): ?>
                <p>
                    <?php _e('Make sure you have created a Twitter app first, it\'s free! 👍', 'csf'); ?>
                    <?php _e('You can <a href="https://developer.twitter.com/en/docs/basics/apps/overview.html" target="_blank">follow instructions here</a>.', 'csf'); ?>
                    <br>
                    <?php _e('If so you can find your api keys from your <a href="https://developer.twitter.com/en/apps" target="_blank">account page</a>.', 'csf'); ?>
                    <br>
                    <br>
                    <?php _e('Once the keys set and saved, if you do not see any option, please reload the page. Thank you 🤘', 'csf'); ?>
                </p>
            <?php else: ?>
                <?php _e('Access your <a href="https://developer.twitter.com/en/apps" target="_blank">Twitter dashboard here</a>.', 'csf'); ?>
            <?php endif; ?>

            <table class="csf-form-table">
                <tbody>
                <tr>
                    <td scope="row">
                        <label><?php _e( 'Twitter API key', 'csf' ); ?></label>
                    </td>
                    <td>
                        <input name="feedier_twitter_api_key"
                               id="feedier_twitter_api_key"
                               class="regular-text"
                               type="text"
                               value="<?php echo (isset($data['twitter_api_key'])) ? $data['twitter_api_key'] : ''; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td scope="row">
                        <label><?php _e( 'Twitter API secret', 'csf' ); ?></label>
                    </td>
                    <td>
                        <input name="feedier_twitter_api_secret"
                               id="feedier_twitter_api_secret"
                               class="regular-text"
                               type="text"
                               value="<?php echo (isset($data['twitter_api_secret'])) ? $data['twitter_api_secret'] : ''; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td scope="row">
                        <label><?php _e( 'Account Name', 'csf' ); ?></label>
                    </td>
                    <td>
                        <input name="feedier_twitter_account_name"
                               id="feedier_twitter_account_name"
                               class="regular-text"
                               type="text"
                               value="<?php echo (isset($data['twitter_account_name'])) ? $data['twitter_account_name'] : ''; ?>"/>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>


        <?php if (!empty($data['twitter_api_secret']) && !empty($data['twitter_api_key']) && !empty($data['twitter_account_name'])): ?>

            <?php
            if (empty($api_response_twitter)) : ?>
                <p class="notice notice-error">
                    <?php _e( 'An error happened on the WordPress side. Make sure your server allows remote calls.', 'csf' ); ?>
                </p>
            <?php
            // If we have an error returned by the API
            elseif (isset($api_response_twitter['error'])): ?>

                <p class="notice notice-error">
                    <?php echo $api_response_twitter['error']; ?>
                </p>

            <?php
            else: ?>
            <?php endif; ?>
        <?php endif; ?>

        <hr>

        <div class="inside csf-button-url-content">

            <button class="button button-primary" id="csf-admin-save" type="submit">
                <?php _e( 'Save', 'csf' ); ?>
            </button>
            <?php if ($not_ready_twitter): ?>
                <div class="csf-url-section">
                    Setup your Twitter API key, API secret and account name to get your Twitter Feeds Url
                </div>
            <?php else: ?>
                <div class="csf-url-section">
                    You can use this url to retrieve your Twitter feeds: <a href="<?php echo get_option( 'siteurl' ).'/wp-json/rest-civic/v1/twitter'; ?>" target="_blank"><?php echo get_option( 'siteurl' ); ?>/wp-json/rest-civic/v1/twitter</a>
                </div>
            <?php endif; ?>
        </div>
    </form>

    <hr />
    <form id="csf-admin-form-facebook" class="postbox">

        <div class="form-group inside">

            <?php
            ?>

            <h3>
                <?php echo $this->csf_getStatusIcon(!$not_ready_facebook); ?>
                <?php _e('Facebook API Settings', 'csf'); ?>
            </h3>

            <?php if ($not_ready_facebook): ?>
                <p>
                    <?php _e('Make sure you have created an Facebook app first, it\'s free! 👍', 'csf'); ?>
                    <?php _e('You can <a href="https://developers.facebook.com/" target="_blank">follow instructions here</a>.', 'csf'); ?>
                    <br>
                    <?php _e('If so you can find your Account ID and token from your <a href="https://developers.facebook.com/apps/" target="_blank">account page</a>.', 'csf'); ?>
                    <br>
                    <br>
                    <?php _e('Once the keys set and saved, if you do not see any option, please reload the page. Thank you 🤘', 'csf'); ?>
                </p>
            <?php else: ?>
                <?php _e('Access your <a href="https://developers.facebook.com/apps/" target="_blank">Facebook dashboard here</a>.', 'csf'); ?>
            <?php endif; ?>

            <table class="csf-form-table">
                <tbody>
                <tr>
                    <td scope="row">
                        <label><?php _e( 'Facebook Account ID or Page Name', 'csf' ); ?></label>
                    </td>
                    <td>
                        <input name="feedier_facebook_account_id"
                               id="feedier_facebook_account_id"
                               class="regular-text"
                               type="text"
                               value="<?php echo (isset($data['facebook_account_id'])) ? $data['facebook_account_id'] : ''; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td scope="row">
                        <label><?php _e( 'Facebook Token', 'csf' ); ?></label>
                    </td>
                    <td>
                        <input name="feedier_facebook_token"
                               id="feedier_facebook_token"
                               class="regular-text"
                               type="text"
                               value="<?php echo (isset($data['facebook_token'])) ? $data['facebook_token'] : ''; ?>"/>
                    </td>
                </tr>
                </tbody>
            </table>

        </div>


        <?php if (!empty($data['facebook_account_id']) && !empty($data['facebook_token'])): ?>

            <?php
            // if we don't even have a response from the API
            if (empty($api_response_facebook)) : ?>
                <p class="notice notice-error">
                    <?php _e( 'An error happened on the WordPress side. Make sure your server allows remote calls.', 'csf' ); ?>
                </p>

            <?php
            // If we have an error returned by the API
            elseif (isset($api_response_facebook['error'])): ?>

                <p class="notice notice-error">
                    <?php echo $api_response_facebook['error']; ?>
                </p>

            <?php
            // If the Feedback carriers were returned
            else: ?>

            <?php endif; ?>

        <?php endif; ?>

        <hr>

        <div class="inside csf-button-url-content">

            <button class="button button-primary" id="csf-admin-facebook-save" type="submit">
                <?php _e( 'Save', 'csf' ); ?>
            </button>
            <?php if ($not_ready_facebook): ?>
                <div class="csf-url-section">
                    Setup your Facebook Account ID and Facebook Token to get your Facebook Feeds Url
                </div>
            <?php else: ?>
                <div class="csf-url-section">
                    You can use this url to retrieve your Facebook feeds: <a href="<?php echo get_option( 'siteurl' ).'/wp-json/rest-civic/v1/facebook'; ?>" target="_blank"><?php echo get_option( 'siteurl' ); ?>/wp-json/rest-civic/v1/facebook</a>
                </div>
            <?php endif; ?>
        </div>
    </form>
    <form id="csf-admin-form-cron" class="postbox">
        <div class="form-group inside">
            <?php
            ?>
            <h3>
                <span class="dashicons dashicons-clock csf-success-message"></span>
                <?php _e('Refresh Schedule', 'csf'); ?>
            </h3>
            <?php _e('Choose a refresh schedule for fetching social feeds.', 'csf'); ?>
            <div class="form-check csf-csf-radio-spaced-form">
                <input class="form-check-input" type="radio" name="feedier_cron_schedule" id="feedier_cron_five_mins" value="five_min" <?php echo $this->cron_schedule == 'five_min' ? 'checked="checked"' : ''; ?>>
                <label class="form-check-label" for="feedier_cron_five_mins">
                    Every Five Minutes
                </label>
            </div>
            <div class="form-check csf-radio-spaced">
                <input class="form-check-input" type="radio" name="feedier_cron_schedule" id="feedier_cron_thirty_mins" value="thirty_min" <?php echo $this->cron_schedule == 'thirty_min' ? 'checked="checked"' : ''; ?>>
                <label class="form-check-label" for="feedier_cron_thirty_mins">
                    Every Thirty Minutes
                </label>
            </div>
            <div class="form-check csf-radio-spaced">
                <input class="form-check-input" type="radio" name="feedier_cron_schedule" id="feedier_cron_hourly" value="hourly" <?php echo $this->cron_schedule == 'hourly' ? 'checked="checked"' : ''; ?>>
                <label class="form-check-label" for="feedier_cron_hourly">
                    Hourly
                </label>
            </div>
            <div class="form-check csf-radio-spaced">
                <input class="form-check-input" type="radio" name="feedier_cron_schedule" id="feedier_cron_daily" value="daily" <?php echo $this->cron_schedule == 'daily' ? 'checked="checked"' : ''; ?>>
                <label class="form-check-label" for="feedier_cron_daily">
                    Daily
                </label>
            </div>
            <div class="form-check csf-radio-spaced">
                <input class="form-check-input" type="radio" name="feedier_cron_schedule" id="feedier_cron_twice_daily" value="twicedaily" <?php echo $this->cron_schedule == 'twicedaily' ? 'checked="checked"' : ''; ?>>
                <label class="form-check-label" for="feedier_cron_twice_daily">
                    Twice Daily
                </label>
            </div>
        </div>
        <hr>
        <div class="inside csf-button-url-content">
            <button class="button button-primary" id="csf-admin-cron-save" type="submit">
                <?php _e( 'Save', 'csf' ); ?>
            </button>
            <button class="button button-secondary csf-button-section" id="csf-admin-cron-refresh" type="submit">
                <?php _e( 'Refresh now', 'csf' ); ?>
            </button>
            <div class="csf-url-section">
                <?php
                echo $cron_scheduler->csf_toString();
                ?>
            </div>
        </div>
    </form>
    <div class="postbox">
        <div class="form-group inside">
            <?php
            ?>
            <h3>
                <span class="dashicons dashicons-migrate csf-success-message"></span>
                <?php _e('Shortcode Usage', 'csf'); ?>
            </h3>
            <p>
                <?php _e('You can also show your social feeds via shortcode by stating your preferred social network (currently supported type options facebook / twitter), the number of feeds to display and the total columns for desktop mode.', 'csf'); ?>
            </p>
            <p>
                <?php _e('Examples: ', 'csf'); ?>
            </p>
            <p>
                <b><?php _e('[civic_social_feeds type=\'facebook\' num=6, cols=3]: ', 'csf'); ?></b>
                <?php _e('Social Network - Facebook, displaying six feeds in three column layout.', 'csf'); ?>
            </p>
         
            <p>
                <b><?php _e('[civic_social_feeds type=\'twitter\' num=5 cols=4]: ', 'csf'); ?></b>
                <?php _e('Social Network - Twitter, displaying five feeds in four column layout.', 'csf'); ?>
            </p>
        </div>

    </div>
</div>





