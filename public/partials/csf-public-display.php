<?php

/**
 * Provide a public-facing view for the plugin
 *
 * @link       https://www.civicuk.com/
 * @since      1.0.0
 *
 * @package    Csf
 * @subpackage Csf/public/partials
 */

class Csf_Public_Display
{

    const CSF_API_URL = '/wp-json/rest-civic/v1/';
    private $current_url;
    private $platform;
    private $feeds;
    private $cols;

    public function __construct($platform, $feeds, $cols)
    {
        $this->platform = $platform;
        $this->feeds = (int)$feeds;
        $this->current_url = get_site_url();
        $this->cols = $cols;
    }

    public function csf_showFacebookFeeds()
    {
        ob_start();
        $data = $this->csf_getResponse();
        if(count($data) < 3){
            return "<div class='csf_error'>Error: Could not load your feeds, please make sure you have added the correct permissions for displaying your feeds.</div>";
        }
        $name_id = $data[0];
        $name = $data[1];
        $data = $data[2]['data'];

        if($data == []){
            return "<div class='csf_error'>Error: Could not load your feeds, please make sure you have added the correct permissions for displaying your feeds or your timeline is not empty.</div>";
        }

        if (count($data) > $this->feeds) {
            $data = array_slice($data, 0, $this->feeds, true);
        }

        if (!empty($data)) {
            ?>
            <div class="csf-container">
                <div class="csf-grid-row">
                <?php
                foreach($data as $item):
                    $postDate = date("d F, Y", strtotime($item["created_time"]));
                 ?>
                   <?php echo $this->csf_displayColumns() ?>
                        <div class="csf-grid-item-wrapper">
                            <div class="csf-grid-item-container">
                                <div class="row header">
                                    <div class="name">
                                        <h5><a href="https://facebook.com/<?php echo $name_id; ?>" target="_blank"><?php echo $name; ?></a></h5>
                                        <span class="sub"><?php echo $postDate; ?></span>
                                    </div>
                                </div>

                                <hr />
                                <div class="row text">
                                    <?php if (!empty($item["message"])) {
                                        echo $this->makeClickableLinks($item["message"]);
                                    } ?>
                                </div>
                                <div class="row thumbnail">
                                    <img src="<?php echo $item["full_picture"] ?>" alt="" />
                                </div>
                                <hr />
                            </div>
                        </div>
                    </div>

                <?php

                endforeach; ?>
                </div>
            </div>
        <?php }
        return ob_get_clean();

    }


    public function csf_showTwitterFeeds()
    {
        ob_start();
        $data = $this->csf_getResponse();

        if (count($data) > $this->feeds) {
            $data = array_slice($data, 0, $this->feeds, true);
        }

        if(empty($data) || !empty($data['error'])){
            return "<div class='csf_error'>Error: Could not load your feeds, please make sure you have added the correct permissions for displaying your feeds or your timeline is not empty.</div>";
        }
        ?>

        <div class="csf-container">
        <div class="csf-grid-row">
            <?php foreach ($data as $k => $v) { ?>
            <?php echo $this->csf_displayColumns() ?>
            <div class="csf-grid-item-wrapper">
                <div class="csf-grid-item-container">
                <div class="csf-tweetEntry">
                    <div class="csf-tweetEntry-content">
                        <a class="csf-tweetEntry-account-group"
                        href="<?php echo "https://twitter.com/" . $v['user']['screen_name']; ?>">
                            <img class="csf-tweetEntry-avatar" src="<?php echo $v['user']['profile_image_url_https']; ?>">
                            <strong class="csf-tweetEntry-fullname">
                                <?php echo $v['user']['name'] ?>
                            </strong>
                            <div class="csf-tweetEntry-username">@<b><?php echo $v['user']['screen_name']; ?></b></div>
                            <div class="csf-tweetEntry-timestamp"> <?php echo $this->timeSince($v['created_at']); ?></div>
                        </a>
                        <div class="csf-tweetEntry-text-container">
                            <?php echo $this->makeClickableLinks($v['full_text']) ?>
                        </div>
                    </div>
                    <?php if(!empty($v['extended_entities']['media'][0]['media_url_https'])): ?>
                        <div class="csf-optionalMedia">
                            <img class="csf-optionalMedia-img" src="<?php echo $v['extended_entities']['media'][0]['media_url_https'] ?>">
                        </div>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </div>
                <?php
            } ?>
        </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function csf_getResponse()
    {
        ob_start();
        $data = array();

        $response = wp_remote_get($this->current_url.self::CSF_API_URL . $this->platform);
        if (is_array($response) && !is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);

            // Check for error
            if (is_wp_error($data)) {
                return 'Could not load your feeds';
            }
            return json_decode($body, true);
        } else {
            return 'Could not load your feeds';
        }
    }

    private function makeClickableLinks($s)
    {
        $url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
        return preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $s);
    }

    private function timeSince($time)
    {

        $since = time() - strtotime($time);


        $chunks = array(
            array(60 * 60 * 24 * 365, 'year'),
            array(60 * 60 * 24 * 30, 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24, 'day'),
            array(60 * 60, 'hour'),
            array(60, 'minute'),
            array(1, 'second')
        );

        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            if (($count = floor($since / $seconds)) != 0) {
                break;
            }
        }

        $string = ($count == 1) ? '1 ' . $name . ' ago' : $count . ' ' . $name . 's ago';

        return $string;

    }

    private function csf_displayColumns()
    {
        switch($this->cols){
            case 2:
                return "<div class='csf-grid-item-two'>";
            case 3:
                return "<div class='csf-grid-item-three'>";
            case 4:
                return "<div class='csf-grid-item-four'>";
            case 5:
                return "<div class='csf-grid-item-five'>";
            case 6:
                return "<div class='csf-grid-item-six'>";
            default:
                return "<div class='csf-grid-item-four'>";
        }
    }
}

