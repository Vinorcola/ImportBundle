<?php

namespace Vinorcola\ImportBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vinorcola\ImportBundle\DependencyInjection\VinorcolaImportExtension;

class VinorcolaImportBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new VinorcolaImportExtension();
    }
}
