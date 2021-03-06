<?php

class MarkovChainLink {
    private $string_interpretation;
    private $sources = array();
    private $next_links = array();
    private $appearance_count = 1;

    public function __construct($text, $source = null, $appearance_count = 1) {
        $this->string_interpretation = $text;
        $this->appearance_count = $appearance_count;
        if (!is_null($source)) {
            $this->addSource($source);
        }
    }

    public function addOrIncrementNextLink($link, $source = null) {
        if (array_key_exists($link, $this->next_links)) {
            $strictlyIncrementNextLinkWeight($link);
        } else {
            $strictlyAddNextLink($link);
        }
        if (!is_null($source)) {
            $this->addSource($source);
        }
    }

    public function strictlyAddNextLink($link) {
        $this->next_links[$link->_toString()] = array();
        $this->next_links[$link->_toString()]['weight'] = 1;
    }

    public function strictlyIncrementNextLinkWeight($link) {
        $this->next_links[$link->_toString()]->weight += 1;
    }

    public function getAppearanceCount() {
        return $this->appearance_count;
    }

    public function incrementAppearanceCount() {
        $this->appearance_count += 1;
    }

    public function addSource($source) {
        if (!in_array($source, $this->sources)) {
            //We lowercase sources so that we don't give a shit about cases
            $this->sources[] = strtolower($source);
        }
    }

    public function getNextLink() {
        $assume_seed = rand(1, $this->appearance_count);
        foreach($next_links as $link => $link_info) {
            $assume_seed -= $link_info['weight'];
            if ($assume_seed <= 0) {
                return $link;
            }
        }

        $exception_message = 'The assume_seed ran out so appearance_count is broken.';
        $exception_fields = array();
        $exception_fields['string_interpretation'] = $this->string_interpretation;
        $exception_fields['appearance_count'] = $this->appearance_count;
        $exception_fields_string = print_r($exception_fields, true);
        throw new Exception($exception_message . '   ' . $exception_fields_string);
    }

    public function _toString() {
        if(!empty($this->string_interpretation)) {
            return $this->string_interpretation;
        } else {
            return print_r($this, true); //idk why you'd pass in the empty string but it'll break arrays if this doesn't return a unique string within the chain
        }
    }
}
