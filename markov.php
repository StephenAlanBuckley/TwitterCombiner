<?php

class Markov {
    const BREAK_TYPE_CHUNK = 1;
    const BREAK_TYPE_WORD = 2;

    private $chunk_length = 5;
    private $chain = array();
    private $break_type = self::BREAK_TYPE_CHUNK;
    private $starting_part = null;

    public function __construct() {
    }

    public function setChunkLength($length) {
        $this->chunk_length = $length;
    }

    public function setStartingPart($start) {
        if (array_key_exists($start, $this->chain)) {
            $this->starting_part = $start;
        }
        $this->starting_part = null;
        throw new Exception("$start not added to Markov chain. Starting Part reset.");
    }

    public function setBreakType($type) {
        switch ($type) {
            case self::BREAK_TYPE_CHUNK:
                $this->break_type = self::BREAK_TYPE_CHUNK;
                break;
            case self::BREAK_TYPE_WORD:
                $this->break_type = self::BREAK_TYPE_WORD;
                break;
        }
    }

    public function addTextToChain($text) {
        switch ($this->break_type) {
            case self::BREAK_TYPE_CHUNK:
                return $this->addTextToChainByChunks($text);
                break;
            case self::BREAK_TYPE_WORD:
                return $this->addTextToChainByWords($text);
                break;
        }
    }

    public function getChain() {
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
      // keeping it simple for now and only splitting on spaces
        $words = explode(" ", $text);
        $previous_word = null;
        foreach ($words as $current_word) {
            if (!is_null($previous_word)) {
                if (array_key_exists($current_word, $this->chain[$previous_word])) {
                    $this->chain[$previous_word][$current_word] += 1;
                } else {
                    $this->chain[$previous_word][$current_word] = 1;
                }
            }
            if (!array_key_exists($current_word, $this->chain)) {
                $this->chain[$current_word] = array();
            }
            $previous_word = $current_word;
        }
    }

    public function createStringFromChain($length = 100) {
        //If this is n-gram chunks we don't want a space between, if it's words then we do!
        if (empty($this->chain) || !isset($this->chain)) {
            return 0;
        }
        $between_parts = '';
        if ($this->break_type == self::BREAK_TYPE_WORD) {
            $between_parts = ' ';
        }
        $created_string = '';
        if (!is_null($this->starting_part)) {
            $current_part = $starting_part;
        } else {
            $current_part = array_rand($this->chain);
        }
        $created_string .= $current_part . $between_parts;
        try {
            while (strlen($created_string) < $length && is_array($this->chain[$current_part])) {
                $next_part = array_rand($this->chain[$current_part]);
                $created_string .= $next_part . $between_parts;
                $current_part = $next_part;
            }
        } catch (Exception $e) {
            print_r("FOUND THIS FOR CURRENT PART:" . $current_part);
            throw $e;
        }
        return $created_string;
    }

}
