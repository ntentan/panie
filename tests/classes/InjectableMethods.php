<?php
namespace ntentan\panie\tests\classes;

use ntentan\panie\Inject;

/**
 * Description of InjectableMethods
 *
 * @author ekow
 */
class InjectableMethods
{
    
    private TestInterface $privateInjected;
    
    protected TestInterface $protectedInjected;
    
    public TestInterface $publicInjected;
    
    #[Inject]
    private function setPrivate(TestInterface $injected) {
        $this->privateInjected = $injected;
    }
    
    #[Inject]
    public function setPublic(TestInterface $injected) {
        $this->publicInjected = $injected;
    }
    
    #[Inject]
    protected function setProtected(TestInterface $injected) {
        $this->protectedInjected = $injected;
    }    
    
    public function getPrivate(): TestInterface
    {
        return $this->privateInjected;
    }
    
    public function getProtected(): TestInterface
    {
        return $this->protectedInjected;
    }
}
