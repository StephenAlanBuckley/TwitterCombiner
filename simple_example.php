<?php

require_once('_Markov.php');

$mark = new Markov();
$text = "Yo dog, I heard you like chains so I put a chain in your chain so you could chain while you're chaining.";

$mark->setBreakType(Markov::BREAK_TYPE_WORD);
$mark->addTextToChain($text);
$creation = $mark->createStringFromChain(100000);
print_r($creation);
print_r("\n");
