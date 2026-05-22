<?php

declare(strict_types=1);

namespace Imadepurnamayasa\PhpInti\Ollama\Request;

use InvalidArgumentException;

/**
 * Class ChatRequestBuilder
 * 
 * Builder pattern implementation for constructing Ollama /api/chat requests.
 */
class ChatRequestBuilder
{
    /**
     * @var string The model name.
     */
    private string $model = 'llama3';

    /**
     * @var array List of message associative arrays: [['role' => 'user', 'content' => 'hello']].
     */
    private array $messages = [];

    /**
     * @var bool Whether to stream responses.
     */
    private bool $stream = false;

    /**
     * @var string|null The response format (e.g. 'json').
     */
    private ?string $format = null;

    /**
     * @var array Optional model options.
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
     * Adds a structured message to the chat history.
     *
     * @param string $role The role of the message sender ('system', 'user', 'assistant').
     * @param string $content The text content of the message.
     * @param array|null $images Optional list of base64-encoded image strings for multimodal models.
     * @return self
     */
    public function addMessage(string $role, string $content, ?array $images = null): self
    {
        $message = [
            'role' => $role,
            'content' => $content
        ];

        if ($images !== null) {
            $message['images'] = $images;
        }

        $this->messages[] = $message;
        return $this;
    }

    /**
     * Syntactic sugar to add a system message.
     *
     * @param string $content
     * @return self
     */
    public function addSystemMessage(string $content): self
    {
        return $this->addMessage('system', $content);
    }

    /**
     * Syntactic sugar to add a user message.
     *
     * @param string $content
     * @param array|null $images
     * @return self
     */
    public function addUserMessage(string $content, ?array $images = null): self
    {
        return $this->addMessage('user', $content, $images);
    }

    /**
     * Syntactic sugar to add an assistant message.
     *
     * @param string $content
     * @return self
     */
    public function addAssistantMessage(string $content): self
    {
        return $this->addMessage('assistant', $content);
    }

    /**
     * Set the whole messages collection directly.
     *
     * @param array $messages
     * @return self
     */
    public function messages(array $messages): self
    {
        $this->messages = $messages;
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
     * Configures the model-specific parameters.
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

        if (empty($this->messages)) {
            throw new InvalidArgumentException('Messages history must not be empty.');
        }

        $payload = [
            'model' => $this->model,
            'messages' => $this->messages,
            'stream' => $this->stream
        ];

        if ($this->format !== null) {
            $payload['format'] = $this->format;
        }

        if (!empty($this->options)) {
            $payload['options'] = $this->options;
        }

        return $payload;
    }
}
