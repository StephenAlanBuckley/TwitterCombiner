<?php

require_once('TwitterAPIExchange.php');
require_once('markov.php');

class TwitterMarkov {

    private $twitter_handles = array();
    private $break_length = 5;

    public function __construct() {
    }

    public function addTwitterHandle($handle) {
        $this->twitter_handles[] = $handle;
        return true;
    }

    public function removeTwitterHandle($handle) {
        $remove_at_index = array_search($handle, $this->twitter_handles);
        if ($remove_at_index === false) {
            //Handle not in array, therefore basically removed already!
            return true;
        }
        unset($this->twitter_handles[$remove_at_index]);
        return true;
    }

    public function getMarkovOfAllAccountsRecentTweets() {
        $all_recent_tweets_text = '';
        foreach ($this->twitter_handles as $handle) {
            $recent_tweets = $this->getRecentTweetString($handle);
            $all_recent_tweets_text .= $recent_tweets;
        }
        $markov_table = generate_markov_table($all_recent_tweets_text, $this->break_length);
        return generate_markov_text(5000, $markov_table, $this->break_length);
    }

    private function getRecentTweetString($twitter_handle, $include_replies = false, $include_retweets = false) {
        $settings = array(
            'oauth_access_token' => "2550724993-9rNgO8fVfzLu3UlIXY2dMBq0JTYXHML0DgWH3Ir",
            'oauth_access_token_secret' => "LkVR3S7A5CyG1xOavXSRPJV0XQFEBfh8IXB4UpYRpPZwQ",
            'consumer_key' => "MQMaeAEdm5PRnEvjXl777ZwVx",
            'consumer_secret' => "ovY8COkG2t1a2YotAX372H8VwQQiubJ0ezQvGXfcPDghDh8Fsb"
        );

        $get_fields ='?screen_name=' . $twitter_handle .
                        '&count=3200&include_rts=' . ($include_retweets ? 'true' : 'false') .
                        '&exclude_replies=' . ($include_replies ? 'false' : 'true');
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($settings);
        $output= $twitter->setGetfield($get_fields)
                     ->buildOauth($url, $requestMethod)
                     ->performRequest();
        $output= json_decode($output);
        foreach ($output as $tweet) {
            $tweet_text = $this->cleanUpText($tweet->text);
            $transcript .= strtolower($tweet_text) . " ";
        }

        return $transcript;
    }

    function cleanUpText($text) {
          $text = preg_replace('/Http(s)?:\/\/[^\s]*/i', '', $text); //remove_urls
          $text = preg_replace('/(^|\s)@([a-z0-9_]+)/i', '', $text);//remove twitter handles
          $text = str_replace('&amp;', '&', $text); //fix ampersands
          return $text;
    }
}
