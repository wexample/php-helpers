<?php

namespace Wexample\Helpers\Testing\Traits;

use SebastianBergmann\Diff\Differ;
use Wexample\Helpers\Helper\ArrayHelper;

trait WithArrayTestCase
{
    /**
     * Asserts that two arrays contain the same values, regardless of order.
     * If differences are found, a unified diff is generated.
     *
     * @param array $expected The expected array.
     * @param array $actual The actual array.
     * @param string $message Optional failure message.
     * @param bool $allowEmptyMissing If true, a missing key paired with an empty array is ignored.
     * @return void
     */
    protected function assertArraysEqual(
        array $expected,
        array $actual,
        string $message = '',
        bool $allowEmptyMissing = false
    ): void {
        // If there are any differences reported by the helper, generate a diff.
        if (!ArrayHelper::areSame($expected, $actual, $allowEmptyMissing)) {
            // Make ignored différences the same between two arrays.
            ArrayHelper::normalize($expected, $actual, $allowEmptyMissing);

            if ($expected !== $actual) {
                // Convert canonical arrays to strings for diffing.
                $expectedString = var_export($expected, true);
                $actualString = var_export($actual, true);

                $differ = new Differ();
                $diff = $differ->diff($expectedString, $actualString);

                $fullMessage = $message . "\nDifferences found:\n" . $diff;
                $this->fail($fullMessage);
            }
        }

        $this->assertTrue(true);
    }
}
