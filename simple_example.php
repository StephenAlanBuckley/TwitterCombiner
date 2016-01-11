<?php

require_once('TwitterMarkov.php');
require_once('secrets.php');

$handles = array_slice($argv, 1);

debugMessage($handles);

$mark = new TwitterMarkov($secrets);
$mark->setBreakType(Markov::BREAK_TYPE_WORD);
foreach($handles as $handle) {
    debugMessage("getting tweets for $handle");
    $tweets_received = $mark->getRecentTweetsFrom($handle);
    if (!is_numeric($tweets_received) && $tweets_received['had_errors']) {
        debugMessage($tweets_received['errors']);
        continue;
    }
    debugMessage("Got $tweets_received tweets from $handle\n");
    $mark->addTweetsFromUserToChain($handle);
    debugMessage("Added the tweets from $handle to the chain");
}
$creation = $mark->createStringFromChain(140);
debugMessage("Making a 140 character string");
debugMessage($creation);
debugMessage("\n");

function debugMessage($message, $add_debug_string_to_start = true, $echo = true) {
    $debug_string = "DEBUGGING: ";

    $output = "";
    if ($add_debug_string_to_start) {
        $output = $debug_string;
    }

    if (is_array($message)) {
        $output .= "Array:\n";
        foreach ($message as $value) {
            $output .= debugMessage($value, false, false);
        }
    } else {
        $output .= " " . var_dump($message, true) . "\n";
    }

    if ($echo) {
        print_r($output);
    }

    return $output;
}
