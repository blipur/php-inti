<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Ollama\Request;

use InvalidArgumentException;

/**
 * Class GenerateRequestBuilder
 * 
 * Builder pattern implementation for constructing Ollama /api/generate requests.
 */
class GenerateRequestBuilder
{
    /**
     * @var string The model name.
     */
    private string $model = 'llama3';

    /**
     * @var string The text prompt.
     */
    private string $prompt = '';

    /**
     * @var string|null Optional system prompt.
     */
    private ?string $system = null;

    /**
     * @var string|null Optional prompt template.
     */
    private ?string $template = null;

    /**
     * @var array|null Optional conversation context.
     */
    private ?array $context = null;

    /**
     * @var bool Whether to stream responses. We default this to false.
     */
    private bool $stream = false;

    /**
     * @var string|null The response format (e.g. 'json').
     */
    private ?string $format = null;

    /**
     * @var array Optional model options (e.g. temperature, seed, top_k).
     */
    private array $options = [];

    /**
     * Sets the model name.
     *
     * @param string $model
     * @return self
     */
    public function model(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Sets the prompt.
     *
     * @param string $prompt
     * @return self
     */
    public function prompt(string $prompt): self
    {
        $this->prompt = $prompt;
        return $this;
    }

    /**
     * Sets the system prompt.
     *
     * @param string $system
     * @return self
     */
    public function system(string $system): self
    {
        $this->system = $system;
        return $this;
    }

    /**
     * Sets the prompt template.
     *
     * @param string $template
     * @return self
     */
    public function template(string $template): self
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Sets the session conversation context.
     *
     * @param array $context
     * @return self
     */
    public function context(array $context): self
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Sets the response format to JSON.
     *
     * @param bool $json
     * @return self
     */
    public function jsonFormat(bool $json = true): self
    {
        $this->format = $json ? 'json' : null;
        return $this;
    }

    /**
     * Configures the model-specific parameters (e.g., temperature).
     *
     * @param array $options
     * @return self
     */
    public function options(array $options): self
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * Sets whether to stream responses.
     *
     * @param bool $stream
     * @return self
     */
    public function stream(bool $stream): self
    {
        $this->stream = $stream;
        return $this;
    }

    /**
     * Builds and returns the compiled payload array.
     *
     * @return array The request payload.
     * @throws InvalidArgumentException If required parameters are missing.
     */
    public function build(): array
    {
        if (empty($this->model)) {
            throw new InvalidArgumentException('Model name must not be empty.');
        }

        if (empty($this->prompt)) {
            throw new InvalidArgumentException('Prompt must not be empty.');
        }

        $payload = [
            'model' => $this->model,
            'prompt' => $this->prompt,
            'stream' => $this->stream
        ];

        if ($this->system !== null) {
            $payload['system'] = $this->system;
        }

        if ($this->template !== null) {
            $payload['template'] = $this->template;
        }

        if ($this->context !== null) {
            $payload['context'] = $this->context;
        }

        if ($this->format !== null) {
            $payload['format'] = $this->format;
        }

        if (!empty($this->options)) {
            $payload['options'] = $this->options;
        }

        return $payload;
    }
}
