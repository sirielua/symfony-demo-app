<?php

namespace App\Infrastructure\VichUploaderBundle;

/**
 * Trait for directory namers
 */
trait VichUploaderPrefixDirectoryNamerTrait
{
    public function getPrefixDirectory($object)
    {
        if (!($object instanceof HasVichUploaderPrefixDirectoryInterface)) {
            return '';
        }
        
        $prefix = trim($object->getVichPrefixDirectory(), '/');
        if (!$prefix) {
            return '';
        }
        
        return $prefix . '/';
    }
}
