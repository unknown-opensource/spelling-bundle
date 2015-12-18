<?php
namespace Unknown\Bundle\SpellingBundle\Checker;

class WordChecker
{
    protected $language;

    protected $words = null;

    protected $pspell = null;

    public function __construct($language)
    {
        $this->language = $language;
    }

    /**
     * Validates word first in cache then in pspell
     * @param string $word
     * @return boolean
     */
    public function valid($word)
    {
        $wordLower = mb_strtolower($word, 'UTF-8');
        $this->parseCache();
        if (!isset($this->words[$wordLower])) {
            $this->words[$wordLower] = pspell_check($this->getPspell(), $word);
        }
        return $this->words[$wordLower];
    }

    /**
     * Returns pspell resource
     * @return int
     */
    protected function getPspell()
    {
        if ($this->pspell === null) {
            $this->pspell = pspell_new($this->language, "", "", "UTF-8");
        }
        return $this->pspell;
    }

    /**
     * Parses cache into php array
     * @return boolean[]
     */
    protected function parseCache()
    {
        if ($this->words === null) {
            $filename = $this->getCacheFile();
            if (!file_exists($filename)) {
                return array();
            }
            $words = array();
            foreach (file($filename) as $row) {
                $words[trim(strtolower($row))] = true;
            }
            $this->words = $words;
        }
        return $this->words;
    }

    /**
     * Returns cache location
     * @return string
     */
    protected function getCacheFile()
    {
        $pathA = "../app/dictionary-".$this->language.".txt";
        if (file_exists($pathA)) {
            return $pathA;
        }
        $pathB = "app/dictionary-".$this->language.".txt";
        if (file_exists($pathB)) {
            return $pathB;
        }
    }
}
