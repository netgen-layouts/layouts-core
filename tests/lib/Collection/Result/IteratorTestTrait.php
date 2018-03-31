<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Iterator;

trait IteratorTestTrait
{
    /**
     * Asserts that iterator returns the expected values.
     *
     * @param array $expected
     * @param \Iterator $resultIterator
     */
    private function assertIteratorValues(array $expected, Iterator $resultIterator)
    {
        $i = 0;

        $this->assertEquals(
            empty($expected),
            !$resultIterator->valid(),
            'Iterator does not have any values when it should.'
        );

        if (empty($expected)) {
            return;
        }

        foreach ($resultIterator as $result) {
            $this->assertArrayHasKey(
                $i,
                $expected,
                sprintf(
                    'Iterator has more values than expected. Expected %d values.',
                    count($expected)
                )
            );

            $this->assertEquals(
                $expected[$i]->getItem()->getValue(),
                $result->getItem()->getValue(),
                sprintf(
                    'Item at position %d with value "%s" does not match expected value "%s"',
                    $i,
                    $result->getItem()->getValue(),
                    $expected[$i]->getItem()->getValue()
                )
            );

            ++$i;
        }
    }
}
