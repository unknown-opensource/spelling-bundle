<?php
namespace Unknown\Bundle\SpellingBundle\Checker;

use Unknown\Bundle\SpellingBundle\Helper;

class CoreChecker
{
    /**
     * @var string
     */
    protected $language;

    /**
     * @var string
     */
    protected $contents;

    /**
     * @var string
     */
    protected $strippedContents = null;

    /**
     * @var string[]
     */
    protected $wordList = null;

    /**
     * @var string[]
     */
    protected $errors = null;

    /**
     * @param string $contents
     */
    public function __construct($contents, $language)
    {
        $this->contents = $contents;
        $this->language = $language;
    }

    /**
     * Returns true if contents has html tags
     * @return boolean
     */
    public function hasHTML()
    {
        return ($this->getStripedContents() != $this->contents);
    }

    /**
     * Returns true if contents had typos words
     * @return boolean
     */
    public function hasErrors()
    {
        return count($this->getErrors()) > 0;
    }

    /**
     * Marks spelling errors in document
     */
    public function getErrors()
    {
        if ($this->errors === null) {
            $this->errors = array();
            $wordChecker = new WordChecker($this->language);
            foreach ($this->getWordList() as $word) {
                if ($word == "") {
                    continue;
                }
                if (!$wordChecker->valid($word)) {
                    $this->errors[] = $word;
                }
            }
        }
        return $this->errors;
    }

    /**
     * Ideally returns a list of words made of letters (no dashes, slashes or dots)
     * @return string[]
     */
    protected function getWordList()
    {
        if ($this->wordList === null) {
            $words = $this->getStripedContents();
            // replace non letter characters with single space
            $words = preg_replace('/[^\p{L}.]+/u', " ", $words);
            $this->wordList = explode(" ", $words);
            $this->wordList = array_unique($this->wordList);
        }
        return $this->wordList;
    }

    /**
     * Ideally returns a list of contents separated by spaces
     * @return string
     */
    protected function getStripedContents()
    {
        if ($this->strippedContents === null) {
            $this->strippedContents = html_entity_decode($this->contents);

            $pattern = '/<!-- START of Symfony Web Debug Toolbar -->.*<!-- END of Symfony Web Debug Toolbar -->/smi';
            $this->strippedContents = preg_replace($pattern, ' ', $this->strippedContents);
            $this->strippedContents = preg_replace('/<style[^>]*>(.*)<\/style>/smiU', ' ', $this->strippedContents);
            $this->strippedContents = preg_replace('/<script[^>]*>(.*)<\/script>/smiU', ' ', $this->strippedContents);
            $this->strippedContents = preg_replace('/<[^>]*>/smi', ' ', $this->strippedContents);
            $this->strippedContents = preg_replace('/<[^>]*>/smi', ' ', $this->strippedContents);
            $this->strippedContents = str_replace(array("\n", "\r", "\t"), " ", $this->strippedContents);
            $this->strippedContents = str_replace(array("     ", "    ", "   ", "  "), " ", $this->strippedContents);
        }
        return $this->strippedContents;
    }
}
