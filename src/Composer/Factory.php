<?php

namespace Bolt\Composer;

use Bolt\Library as Lib;
use Bolt\Translation\Translator as Trans;
use Composer\Composer;
use Composer\IO\BufferIO;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Exception\CurlException;
use Silex\Application;

final class Factory extends PackageManager
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var Composer\IO\BufferIO
     */
    private $io;

    /**
     * @var Composer\Composer
     */
    private $composer;

    /**
     * @var Silex\Application
     */
    private $app;

    /**
     * @var array
     */
    public $messages = array();

    /**
     * @var boolean
     */
    protected $downgradeSsl;

    /**
     * @param Silx\Application        $app
     * @param array                   $options
     */
    public function __construct(Application $app, array $options)
    {
        $this->app = $app;
        $this->options = $options;

        // Create the Composer and IOInterface objects
        $this->composer = $this->getComposer();

        //parent::$io = $this->io;
        //parent::$composer = $this->composer;
    }

    /**
     * Get a Composer object
     *
     * @return Composer\Composer
     */
    public function getComposer()
    {
        if (!$this->composer) {
            // Set working directory
            chdir($this->options['basedir']);

            // Use the factory to get a new Composer object
            $this->composer = \Composer\Factory::create($this->getIO(), $this->options['composerjson'], true);

            if ($this->downgradeSsl) {
                $this->allowSslDowngrade(true);
            }
        }

        return $this->composer;
    }

    /**
     * Get the IOInterface object
     *
     * @return Composer\IO\IOInterface
     */
    protected function getIO()
    {
        if (!$this->io) {
            $this->io = new BufferIO();
        }

        return $this->io;
    }

    /**
     * Get a new Composer object
     *
     * @return Bolt\Composer\Factory
     */
    protected function resetComposer()
    {
        $this->composer = null;

        return $this->getComposer();
    }

    /**
     * Return the output from the last IO
     *
     * @return array
     */
    public function getOutput()
    {
        return $this->io->getOutput();
    }

    /**
     * Set repos to allow HTTP instead of HTTPS
     *
     * @param boolean $choice
     */
    private function allowSslDowngrade($choice)
    {
        $repos = $this->composer->getRepositoryManager()->getRepositories();

        foreach ($repos as $repo) {
            $reflection = new \ReflectionClass($repo);
            $allowSslDowngrade = $reflection->getProperty('allowSslDowngrade');
            $allowSslDowngrade->setAccessible($choice);
            $allowSslDowngrade->setValue($repo, $choice);
        }
    }
}