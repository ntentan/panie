<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ntentan\panie;

/**
 * Description of ComponentLoaderInterface
 *
 * @author ekow
 */
interface ComponentResolverInterface {

    public function getComponentClassName($component, $parameters);
}
