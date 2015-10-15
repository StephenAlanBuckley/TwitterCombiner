<?php

require_once('TwitterMarkov.php');
require_once('secrets.php');

$mark = new TwitterMarkov($secrets);
$mark->setBreakType(Markov::BREAK_TYPE_CHUNK);
$mark->addTwitterHandle('dad_beach');
$mark->addTwitterHandle('alexisohanian');
$creation = $mark->getMarkovOfAllAccountsRecentTweets(1000);
print_r($creation);
print_r("\n");
