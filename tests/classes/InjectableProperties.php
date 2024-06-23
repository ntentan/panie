<?php

namespace ntentan\panie\tests\classes;

use ntentan\panie\Inject;


/**
 * Description of Injectable
 *
 * @author ekow
 */
class InjectableProperties {
    
    #[Inject]
    private TestInterface $privateInjected;
    
    #[Inject]
    protected TestInterface $protectedInjected;
    
    #[Inject]
    public TestInterface $publicInjected;
    
    public function getPrivate(): TestInterface
    {
        return $this->privateInjected;
    }
    
    public function getProtected(): TestInterface
    {
        return $this->protectedInjected;
    }
}
