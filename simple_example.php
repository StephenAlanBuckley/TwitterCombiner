<?php

require_once('_Markov.php');

$mark = new Markov();
$text = "My, my, some weather we're having here, am I right? Back in my day the weather was weather whether we wanted it or not, and we all weathered it differently. Not like you millenial dirtbags with your unpaid internships, coasting on your lack of salary.";

$mark->addTextToChain($text);
$creation = $mark->createStringFromChain();
print_r($creation);
