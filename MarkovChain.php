<?php

/*
 * Manages operations on a collection (chain) of MarkovChainLinks
            ________        __    .___  __ _________
           /  _____/  _____/  |_  |   |/  |\_____   \
          /   \  ____/ __ \   __\ |   \   __\ /   __/
          \    \_\  \  ___/|  |   |   ||  |  |   |
           \______  /\___  >__|   |___||__|  |___|
                  \/     \/                  <___>
 */

require_once 'MarkovChainLink.php';

class MarkovChain {

    private $heads = array();  //Each source will have its own head
    private $links = array();  //Even though they chain, we want to be able to find them by their ngrams
    private $case_sensitive = true;

    public function __construct() {
    }

    public function addLink($ngram, $attach_to_ngram = null, $source = null) {
        //We're adding this to a previous link
        if (!is_null($attach_to_ngram) && array_key_exists($attach_to_ngram, $this->links)) {
            if (array_key_exists($ngram, $this->links)) {
                $this->links[$attach_to_ngram]->addOrIncrementNextLink($this->links[$ngram], $source);
                return;
            }
            //In this case we have a parent but not a child; add attached
            $this->addAttachedLink($ngram, $attach_to_ngram, $source);
        }
        //If this is the first link, make it a head
        if (count($this->links) == 0 || is_null($attach_to_ngram)) {
            $this->addHeadLink($ngram, $source);
        }
    }

    public function addHeadLink($ngram, $source = null, $increment_appearance = false) {
        if (!array_key_exists($ngram, $this->links)) {
            $new_head = new MarkovChainLink($ngram, $source);
            $this->links[$ngram] = $new_head;
            $this->heads[] = $new_head;
        } else {
            if ($increment_appearance) {
                $this->links[$ngram]->incrementAppearanceCount();
            }
            $this->heads[] = $this->links[$ngram];
        }
    }

    public function addAttachedLink($ngram, $attach_to_ngram, $source) {
        if (array_key_exists($attach_to_ngram, $this->links)) {
            $attach_to_link = $this->links[$attach_to_ngram];
        } else {
            throw new Exception("Tried to attach to $attach_to_ngram, which isn't in links");
        }
        if (array_key_exists($ngram, $this->links)) {
            $child = $this->links[$ngram];
        } else {
            $child = new MarkovChainLink($ngram, $source);
        }

    }
    private function getLinkWithNgram($ngram) {
    }

    private function addToLinks($chain_link){
    }

}
