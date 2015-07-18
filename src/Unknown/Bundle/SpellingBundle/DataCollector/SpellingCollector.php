<?php
namespace Unknown\Bundle\SpellingBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Unknown\Bundle\SpellingBundle\Checker\CoreChecker;

class SpellingCollector implements DataCollectorInterface
{
    protected $typos = array();

    /**
     * @inheritdoc
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $content = $response->getContent();

        $checker = new CoreChecker($content, $request->getLocale());
        if (!$checker->hasHTML()) {
            return;
        }
        $this->typos = $checker->getErrors();
    }

    /**
     * Returns list of typos
     *
     * @return string[]
     */
    public function getTypos()
    {
        return $this->typos;
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     *
     * @api
     */
    public function getName()
    {
        return 'spelling_collector';
    }
}
