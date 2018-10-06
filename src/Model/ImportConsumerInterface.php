<?php

namespace Vinorcola\ImportBundle\Model;

interface ImportConsumerInterface
{
    /**
     * Prepare the import process.
     *
     * This method is called before any line is consumed.
     */
    public function prepare(): void;

    /**
     * Consume a line from the imported file.
     *
     * @param array $values    The values extracted from the current line of the file.
     * @param int   $lineIndex The index of the current line of the file (first line is 1).
     */
    public function consume(array $values, int $lineIndex): void;

    /**
     * Finish the import process.
     *
     * This method is called once all the lines have been consumed.
     *
     * @param int $nbLineConsumed The total number of lines consumed during the import.
     */
    public function finish(int $nbLineConsumed): void;
}
