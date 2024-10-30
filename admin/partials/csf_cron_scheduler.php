<?php

class Csf_Cron_Scheduler
{
    private $schedule_time;

    public function __construct($schedule_time)
    {
        $this->schedule_time = $schedule_time;
    }

    private function csf_cron_activation_custom($schedule) {
        if( !wp_next_scheduled( 'civic_social_feeds_cron' ) ) {
            wp_schedule_event(time(), $schedule, 'civic_social_feeds_cron');
        }
    }

    private function csf_cron_deactivate()
    {
        $timestamp = wp_next_scheduled ('civic_social_feeds_cron');
        wp_unschedule_event ($timestamp, 'civic_social_feeds_cron');
    }

    public function csf_schedule_job()
    {
        $this->csf_cron_deactivate();
        $this->csf_cron_activation_custom($this->schedule_time);
    }

    public function csf_toString()
    {
        switch ($this->schedule_time) {
            case 'hourly':
                return 'Currently refreshing every hour';
                break;
            case 'daily':
                return 'Currently refreshing every day';
                break;
            case 'five_min':
                return 'Currently refreshing every five minutes';
                break;
            case 'thirty_min':
                return 'Currently refreshing every thirty minutes';
                break;
            case 'twicedaily':
                return 'Currently refreshing twice per day';
                break;
            default:
                return 'You need to configure refresh schedule';
        }
    }
}