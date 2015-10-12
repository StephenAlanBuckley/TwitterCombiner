<?php
require_once('TwitterAPIExchange.php');
require_once('markov.php');
ini_set('memory_limit', '512M');

$break_length = 7;

$check_for_word = strtolower($argv[1]);

$tweet_strings = array();
$tweet_strings['dad_beach'] = getRecentTweetString('dad_beach');
$tweet_strings['msalicenutting'] = getRecentTweetString('msalicenutting');
$complete_text = $tweet_strings['dad_beach'] . ' ' . $tweet_strings['msalicenutting'];


$complete_text = preg_replace("/\n/", ' ', $complete_text);
$complete_text = preg_replace("/\s\s/", ' ', $complete_text);


$markov_table = generate_markov_table($complete_text, $break_length);

if ($check_for_word) {
    for($i = 0; $i < 1; $i++) {
        print_r(ucwords(generate_markov_text(140, $markov_table, $break_length, $check_for_word)) . "\n");
    }
} else {
    print_r(ucwords(generate_markov_text(5000, $markov_table, $break_length)) . "\n");
}

function getRecentTweetString($twitter_handle, $include_replies = false, $include_retweets = false) {
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
        $tweet_text = cleanUpText($tweet->text);
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
