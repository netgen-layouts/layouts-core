<?php

namespace Netgen\BlockManager\Tests\Collection\Result;

use Iterator;

trait IteratorTestTrait
{
    /**
     * Asserts that iterator returns the expected values.
     *
     * @param array $expected
     * @param \Iterator $iterator
     */
    protected function assertIteratorValues(array $expected, Iterator $iterator)
    {
        $i = 0;

        foreach ($iterator as $value) {
            if (!isset($expected[$i])) {
                $this->fail(
                    sprintf(
                        'Iterator has more values than expected. Expected %d values.',
                        count($expected)
                    )
                );
            }

            if ($expected[$i] !== $value) {
                $this->fail(
                    sprintf(
                        'Item at position %d with value "%s" does not match expected value "%s"',
                        $i,
                        $value,
                        $expected[$i]
                    )
                );
            }

            ++$i;
        }
    }
}
