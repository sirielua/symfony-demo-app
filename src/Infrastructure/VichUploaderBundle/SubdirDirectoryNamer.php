<?php

namespace App\Infrastructure\VichUploaderBundle;

use Vich\UploaderBundle\Naming\SubdirDirectoryNamer as BaseNamer;
use Vich\UploaderBundle\Mapping\PropertyMapping;

class SubdirDirectoryNamer extends BaseNamer
{
    use VichUploaderPrefixDirectoryNamerTrait;
    
    public function directoryName($object, PropertyMapping $mapping): string
    {
        return $this->getPrefixDirectory($object) . parent::directoryName($object, $mapping);
    }
}
