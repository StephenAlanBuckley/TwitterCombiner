<?php

require_once('TwitterAPIExchange.php');
require_once('Markov.php');

class TwitterMarkov extends Markov {

    private $twitter_handles = array();
    private $twitter_settings = array();

    public function __construct($twitter_settings) {
        $this->break_length = 3;
        $this->twitter_settings = array(
            'oauth_access_token' => $twitter_settings['oauth_access_token'],
            'oauth_access_token_secret' => $twitter_settings['oauth_access_token_secret'],
            'consumer_key' => $twitter_settings['consumer_key'],
            'consumer_secret' => $twitter_settings['consumer_secret']
        );
        parent::__construct();
    }

    public function addTweetsFromUserToChain($handle) {
        if (array_key_exists($handle, $this->twitter_handles)
            && array_key_exists('complete_sanitized_text', $this->twitter_handles[$handle])) {
              $this->addTextToChain($this->twitter_handles[$handle]['complete_sanitized_text']);
              return true;
        }
        return false;
    }

    /*
     * Adds a handle to the private twitter handles array.
     *
     * returns count of tweets received from Twitter
     */
    public function getRecentTweetsFrom($twitter_handle, $include_replies = false, $include_retweets = false) {
        $this->twitter_handles[] = $twitter_handle;
        $get_fields ='?screen_name=' . $twitter_handle .
                        '&count=3200&include_rts=' . ($include_retweets ? 'true' : 'false') .
                        '&exclude_replies=' . ($include_replies ? 'false' : 'true'); //looks odd because my param is include and theirs is exclude
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($this->twitter_settings);
        $output= $twitter->setGetfield($get_fields)
                     ->buildOauth($url, $requestMethod)
                     ->performRequest();
        $output= json_decode($output);
        if (count($output->errors) > 0) {
            return array("had_errors" => true, "errors" => $output->errors);
        }
        $this->twitter_handles[$twitter_handle]['raw_tweets'] = array();
        $this->twitter_handles[$twitter_handle]['sanitized_tweets'] = array();
        $this->twitter_handles[$twitter_handle]['complete_sanitized_text'] = '';
        if (count($output) <= 0) {
            return 0;
        }
        foreach ($output as $tweet) {
            $this->twitter_handles[$twitter_handle]['raw_tweets'][] = $tweet->text;
            $tweet_text = $this->cleanUpText($tweet->text);
            $tweet_text = strtolower($tweet_text) . " ";
            $this->twitter_handles[$twitter_handle]['sanitized_tweets'][] = $tweet_text;
            $transcript .= $tweet_text;
        }

        $this->twitter_handles[$twitter_handle]['complete_sanitized_text'] = $transcript;
        return count($this->twitter_handles[$twitter_handle]['sanitized_tweets']);
    }

    private function cleanUpText($text) {
          $text = preg_replace('/Http(s)?:\/\/[^\s]*/i', '', $text); //remove_urls
          $text = preg_replace('/(^|\s)@([a-z0-9_]+)/i', '', $text);//remove twitter handles
          $text = str_replace('&amp;', '&', $text); //fix ampersands
          return $text;
    }
}
