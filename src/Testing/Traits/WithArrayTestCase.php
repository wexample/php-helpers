<?php

namespace Wexample\Helpers\Testing\Traits;

use SebastianBergmann\Diff\Differ;
use Wexample\Pseudocode\Helper\ArrayHelper;

trait WithArrayTestCase
{
    /**
     * Asserts that two arrays contain the same values, regardless of order.
     * If differences are found, a unified diff is generated.
     *
     * @param array $expected The expected array.
     * @param array $actual The actual array.
     * @param string $message Optional failure message.
     *
     * @return void
     */
    protected function assertArraysEqual(
        array $expected,
        array $actual,
        string $message = ''
    ): void
    {
        // If there are any differences reported by the helper, generate a diff.
        $differences = ArrayHelper::diffArrays($expected, $actual);
        if (!empty($differences)) {
            // Canonicalize arrays to ignore order differences.
            $canonicalExpected = \Wexample\Helpers\Helper\ArrayHelper::canonicalize($expected);
            $canonicalActual = \Wexample\Helpers\Helper\ArrayHelper::canonicalize($actual);

            if ($canonicalExpected !== $canonicalActual) {
                // Convert canonical arrays to strings for diffing.
                $expectedString = var_export($canonicalExpected, true);
                $actualString = var_export($canonicalActual, true);

                $differ = new Differ();
                $diff = $differ->diff($expectedString, $actualString);

                $fullMessage = $message . "\nDifferences found:\n" . $diff;
                $this->fail($fullMessage);
            }
        }

        $this->assertTrue(true);
    }
}
