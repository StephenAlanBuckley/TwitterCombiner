<?php

class Markov {
    const BREAK_TYPE_CHUNK = 1;
    const BREAK_TYPE_WORD = 2;

    private $chunk_length = 5;
    private $chain = array();
    private $break_type = self::BREAK_TYPE_CHUNK;

    public function __construct() {
    }

    public function addTextToChain($text) {
        if ($this->break_type == self::BREAK_TYPE_CHUNK) {
            return $this->addTextToChainByChunks($text);
        } else {
            return $this->addTextToChainByWords($text);
        }
    }

    public function createStringFromChain($length = null) {
        return $this->chain;
    }

    private function addTextToChainByChunks($text) {
        $remaining_text = $text;
        $previous_chunk = null;
        while (strlen($remaining_text) > $this->chunk_length) {
            $current_chunk = substr($remaining_text, 0, $this->chunk_length);
            if (!is_null($previous_chunk)) {
                if (array_key_exists($current_chunk, $this->chain[$previous_chunk])) {
                    $this->chain[$previous_chunk][$current_chunk] += 1;
                } else {
                    $this->chain[$previous_chunk][$current_chunk] = 1;
                }
            }
            if (!array_key_exists($current_chunk, $this->chain)) {
                $this->chain[$current_chunk] = array();
            }
            $remaining_text = substr($remaining_text, $this->chunk_length);
            $previous_chunk = $current_chunk;
        }
    }

    private function addTextToChainByWords($text) {
      // do this one
    }
}
