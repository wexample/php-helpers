<?php

namespace Wexample\Helpers\Testing\Traits;

use Symfony\Component\Yaml\Yaml;

trait WithYamlTestCase
{
    use WithArrayTestCase;

    /**
     * Asserts that the contents of two YAML files are equal.
     * The YAML files are parsed into arrays before comparison.
     *
     * @param string $expectedFilePath Path to the expected YAML file.
     * @param string $actualFilePath Path to the generated YAML file.
     * @param string $message Optional failure message.
     *
     * @return void
     */
    protected function assertYamlFilesEqual(
        string $expectedFilePath,
        string $actualFilePath,
        string $message = ''
    ): void
    {
        $expectedArray = Yaml::parse(file_get_contents($expectedFilePath));
        $actualArray = Yaml::parse(file_get_contents($actualFilePath));
        $this->assertArraysEqual($expectedArray, $actualArray, $message);
    }
}
