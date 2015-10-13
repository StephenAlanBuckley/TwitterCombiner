<?php

class Markov {
    private $break_length = 5;
    private $chain = array();

    public function __construct() {
    }

    public function addTextToChain($text) {
        $remaining_text = $text;
        $previous_chunk = null;
        while (strlen($remaining_text) > $this->break_length) {
            $current_chunk = substr($remaining_text, 0, $this->break_length);
            if (!is_null($previous_chunk)) {
                if (array_key_exists($current_chunk, $chain[$previous_chunk])) {
                    $chain[$previous_chunk][$current_chunk] += 1;
                } else {
                    $chain[$previous_chunk][$current_chunk] = 1;
                }
            }
            if (!array_key_exists($current_chunk, $chain)) {
                $chain[$current_chunk] = array();
            }
            $remaining_text = substr($remaining_text, $this->break_length);
            $previous_chunk = $current_chunk;
        }
    }

    public function createStringFromChain($length = null) {
    }
}
