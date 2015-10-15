<?php

require_once('TwitterMarkov.php');
require_once('secrets.php');

$handles = array_slice($argv, 1);

$mark = new TwitterMarkov($secrets);
$mark->setBreakType(Markov::BREAK_TYPE_WORD);
foreach($handles as $handle) {
    $tweets_received = $mark->getRecentTweetsFrom($handle);
    print_r("Got $tweets_received tweets from $handle\n");
    $mark->addTweetsFromUserToChain($handle);
}
$creation = $mark->createStringFromChain(140);
print_r($creation);
print_r("\n");
