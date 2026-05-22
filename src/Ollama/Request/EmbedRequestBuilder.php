<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Ollama\Request;

use InvalidArgumentException;

/**
 * Class EmbedRequestBuilder
 *
 * Implements the Builder design pattern to construct payloads for the Ollama API `/api/embed` endpoint.
 * This class facilitates fluent generation of query payloads with structured inputs, models, and options.
 */
class EmbedRequestBuilder
{
    /**
     * @var string The model name to be used for generating embeddings.
     */
    private string $model = 'all-minilm';

    /**
     * @var string|array The input text or collection of texts to embed.
     */
    private $input;

    /**
     * @var bool|null Controls whether the model truncates input to fit its context window.
     */
    private ?bool $truncate = null;

    /**
     * @var array Model-specific options (e.g. temperature, seed, context size).
     */
    private array $options = [];

    /**
     * Sets the model name.
     *
     * @param string $model The model name (e.g., 'all-minilm', 'nomic-embed-text').
     * @return self The builder instance for chaining.
     */
    public function model(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Sets the input text(s) to embed.
     *
     * @param string|array $input A single string or an array of strings to generate embeddings for.
     * @return self The builder instance for chaining.
     */
    public function input($input): self
    {
        $this->input = $input;
        return $this;
    }

    /**
     * Configures the truncation option.
     *
     * @param bool $truncate True to truncate input to fit context window, false otherwise.
     * @return self The builder instance for chaining.
     */
    public function truncate(bool $truncate): self
    {
        $this->truncate = $truncate;
        return $this;
    }

    /**
     * Configures model-specific options.
     *
     * @param array $options Key-value options for the model.
     * @return self The builder instance for chaining.
     */
    public function options(array $options): self
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    /**
     * Compiles and builds the request payload array.
     *
     * @return array The compiled request payload compatible with Ollama `/api/embed`.
     * @throws InvalidArgumentException If required parameters are missing or invalid.
     */
    public function build(): array
    {
        if (empty($this->model)) {
            throw new InvalidArgumentException('Model name must not be empty.');
        }

        if (empty($this->input)) {
            throw new InvalidArgumentException('Input to embed must not be empty.');
        }

        $payload = [
            'model' => $this->model,
            'input' => $this->input,
        ];

        if ($this->truncate !== null) {
            $payload['truncate'] = $this->truncate;
        }

        if (!empty($this->options)) {
            $payload['options'] = $this->options;
        }

        return $payload;
    }
}
