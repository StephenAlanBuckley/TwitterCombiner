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

    public function getMarkovOfAllAccountsRecentTweets($length = 140) {
        $all_recent_tweets_text = '';
        foreach ($this->twitter_handles as $handle) {
            $recent_tweets = $this->getRecentTweetString($handle);
            $all_recent_tweets_text .= $recent_tweets;
        }
        $markov_table = $this->addTextToChain($all_recent_tweets_text);
        return $this->createStringFromChain($length);
    }

    private function getRecentTweetString($twitter_handle, $include_replies = false, $include_retweets = false) {
        $get_fields ='?screen_name=' . $twitter_handle .
                        '&count=3200&include_rts=' . ($include_retweets ? 'true' : 'false') .
                        '&exclude_replies=' . ($include_replies ? 'false' : 'true');
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($this->twitter_settings);
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

    private function cleanUpText($text) {
          $text = preg_replace('/Http(s)?:\/\/[^\s]*/i', '', $text); //remove_urls
          $text = preg_replace('/(^|\s)@([a-z0-9_]+)/i', '', $text);//remove twitter handles
          $text = str_replace('&amp;', '&', $text); //fix ampersands
          return $text;
    }
}
