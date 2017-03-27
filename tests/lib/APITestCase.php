<?php

namespace Tests\lib;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\VarDumper\VarDumper;

class APITestCase extends KernelTestCase
{
    private static $staticClient;
    private static $history;
    private $output;
    /**
     * @var Client
     */
    protected $client;

    public static function setUpBeforeClass()
    {
        self::$history = [];
        $history = Middleware::history(self::$history);
        $stack = HandlerStack::create();
// Add the history middleware to the handler stack.
        $stack->push($history);

        self::$staticClient = new Client([
            'handler' => $stack,
            'base_uri' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);
        self::bootKernel();
    }

    protected function setUp()
    {
        $this->client = self::$staticClient;
        $this->purgeDatabase();
    }

    protected function getService($id)
    {
        return self::$kernel->getContainer()
            ->get($id);
    }

    private function purgeDatabase()
    {
        $purger = new ORMPurger($this->getService('doctrine')->getManager());
        $purger->purge();
    }

    /**
     * Clean up Kernel usage in this test.
     */
    protected function tearDown()
    {
        // purposefully not calling parent class, which shuts down the kernel
    }

    protected function onNotSuccessfulTest(Exception $e)
    {
        if (self::$history) {
            $lastTransaction = end(self::$history);
            if (! $this->isResponseSuccessful($lastTransaction['response'])) {
                $this->printDebug('');
                $this->printDebug('<error>Failure!</error> when making the following request:');
                $this->printRequestUrl($lastTransaction['request']);
                $this->printDebug('');
                if ($lastTransaction['response']) {
                    $this->debugResponse($lastTransaction['response']);
                } elseif ($lastTransaction['error']) {
                    $this->printDebug($lastTransaction['error']);
                }
            }
        }
        throw $e;
    }

    protected function printRequestUrl(Request $request)
    {
        if ($request) {
            $this->printDebug(sprintf('<comment>%s</comment>: <info>%s</info>', $request->getMethod(), $request->getUri()));
        } else {
            $this->printDebug('No request was made.');
        }
    }

    private function isResponseSuccessful(Response $response)
    {
        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    protected function debugResponse(Response $response)
    {
        $profilerURI = $response->getHeader('X-Debug-Token-Link');
        if (count($profilerURI)) {
            $this->printDebug(sprintf('<comment>Profiler URI:</comment> <info>%s</info>', $profilerURI[0]));
        }
        $body = (string) $response->getBody();
        libxml_use_internal_errors(true);
        $html = new \DOMDocument();
        $html->loadHTML($body);
        $logElements = $html->getElementById('logs')->getElementsByTagName('ol')->item(0)->childNodes;
        foreach ($logElements as $logElement) {
            /** @var $logElement \DOMElement */
            if (get_class($logElement) == \DOMElement::class && stripos($logElement->getAttribute('class'), 'error') !== false) {
                $this->printDebug($logElement->nodeValue);
            }
        }
        libxml_use_internal_errors(false);
    }

    protected function printDebug($string)
    {
        if ($this->output === null) {
            $this->output = new ConsoleOutput();
        }
        $this->output->writeln($string);
    }
}