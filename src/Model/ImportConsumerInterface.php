<?php

namespace Vinorcola\ImportBundle\Model;

interface ImportConsumerInterface
{
    /**
     * Consume a line from the imported file.
     *
     * @param array $values
     * @param int   $lineIndex
     */
    public function consume(array $values, int $lineIndex): void;
}
