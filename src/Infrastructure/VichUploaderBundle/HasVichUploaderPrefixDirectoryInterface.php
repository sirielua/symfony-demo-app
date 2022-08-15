<?php

namespace App\Infrastructure\VichUploaderBundle;

/**
 * Interface for #[Vich\Uploadable] entities
 */
interface HasVichUploaderPrefixDirectoryInterface
{
    public function getVichPrefixDirectory(): string;
}
