<?php

namespace SmartPanel\Mjml;

use SmartPanel\Mjml\Exceptions\CouldNotConvertMjml;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class Mjml
{
    protected bool $keepComments = true;

    protected bool $ignoreIncludes = false;

    protected bool $beautify = false;

    protected bool $minify = false;

    protected string $validationLevel;

    protected const LEVEL_STRICT = 'strict';
    protected const LEVEL_SOFT = 'soft';
    protected const LEVEL_SKIP = 'skip';

    protected string $filePath = '.';

    protected string $workingDirectory;

    public static function new(): self
    {
        return new static();
    }

    protected function __construct()
    {
        $this->validationLevel = self::LEVEL_SOFT;

        $this->workingDirectory = realpath(dirname(__DIR__).'/bin');
    }

    public function keepComments(bool $keepComments = true): self
    {
        $this->keepComments = $keepComments;

        return $this;
    }

    public function hideComments(): self
    {
        return $this->keepComments(false);
    }

    public function ignoreIncludes(bool $ignoreIncludes = true): self
    {
        $this->ignoreIncludes = $ignoreIncludes;

        return $this;
    }

    public function beautify(bool $beautify = true): self
    {
        $this->beautify = $beautify;

        return $this;
    }

    public function minify(bool $minify = true): self
    {
        $this->minify = $minify;

        return $this;
    }

    public function validationLevel(string $validationLevel): self
    {
        $this->validationLevel = $validationLevel;

        return $this;
    }

    public function filePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function workingDirectory(string $workingDirectory): self
    {
        $this->workingDirectory = $workingDirectory;

        return $this;
    }

    public function canConvert(string $mjml): bool
    {
        try {
            $this->convert($mjml);
        } catch (CouldNotConvertMjml $ex) {
            return false;
        }

        return true;
    }

    public function canConvertWithoutErrors(string $mjml): bool
    {
        try {
            $result = $this->convert($mjml);
        } catch (CouldNotConvertMjml $ex) {
            return false;
        }

        return ! $result->hasErrors();
    }

    /**
     * @throws CouldNotConvertMjml
     */
    public function toHtml(string $mjml, array $options = []): string
    {
        return $this->convert($mjml, $options)->html();
    }

    /**
     * @throws CouldNotConvertMjml
     */
    public function convert(string $mjml, array $options = []): MjmlResult
    {
        $arguments = [
            $mjml,
            $this->configOptions($options),
        ];

        $resultString = $this->getLocalResult($arguments);

        $resultString = $this->checkForDeprecationWarning($resultString);

        $resultProperties = json_decode($resultString, true);

        if (array_key_exists('mjmlError', $resultProperties)) {
            throw CouldNotConvertMjml::make($resultProperties['mjmlError']);
        }

        return new MjmlResult($resultProperties);
    }

    protected function checkForDeprecationWarning(string $result): string
    {
        $deprecationWarning = 'MJML v3 syntax detected, migrating to MJML v4 syntax. Use mjml -m to get the migrated MJML.';

        if (str_contains($result, $deprecationWarning)) {
            $result = str_replace($deprecationWarning, '', $result);
        }

        return $result;
    }

    protected function getCommand(array $arguments): array
    {
        $extraDirectories = [
            '/usr/local/bin',
            '/opt/homebrew/bin',
        ];

        $nodePathFromEnv = getenv('MJML_NODE_PATH');

        if ($nodePathFromEnv) {
            array_unshift($extraDirectories, $nodePathFromEnv);
        }

        return [
            (new ExecutableFinder())->find('node', 'node', $extraDirectories),
            'mjml.mjs',
            json_encode(array_values($arguments)),
        ];
    }

    protected function configOptions(array $overrides): array
    {
        $defaults = [
            'keepComments' => $this->keepComments,
            'ignoreIncludes' => $this->ignoreIncludes,
            'beautify' => $this->beautify,
            'minify' => $this->minify,
            'validationLevel' => $this->validationLevel,
            'filePath' => $this->filePath,
        ];

        return array_merge($defaults, $overrides);
    }

    protected function getLocalResult(array $arguments): string
    {
        $process = new Process(
            $this->getCommand($arguments),
            $this->workingDirectory,
        );

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }
}
