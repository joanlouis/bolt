<?php
namespace Bolt\Tests\Configuration;

use Bolt\Application;
use Bolt\Configuration\Composer;
use Bolt\Configuration\ComposerChecks;

/**
 * Class to test correct operation and locations of composer configuration.
 *
 * @author Ross Riley <riley.ross@gmail.com>
 *
 */
class ComposerConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function setup()
    {

    }

    public function tearDown()
    {
    }

    
    public function testComposerCustomConfig()
    {
        $config = new Composer(TEST_ROOT);
        $app = new Application(array('resources' => $config));
        $this->assertEquals('/bolt-public/', $config->getUrl('app'));
    }
    
    public function testComposerVerification()
    {
        $config = new Composer(TEST_ROOT);
        $verifier = new ComposerChecks($config);
        $config->setVerifier($verifier);
        $config->verify(); 
    }

   
}
