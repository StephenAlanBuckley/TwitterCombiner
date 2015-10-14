<?php

require_once('_Markov.php');

$mark = new Markov();
$text = "Yo dog, I heard you like chains so I put a chain in your chain so you could chain while you're chaining.";

$mark->setBreakType(Markov::BREAK_TYPE_WORD);
$mark->setChunkLength(3);
$mark->addTextToChain($text);
$creation = $mark->getChain();
print_r($creation);
