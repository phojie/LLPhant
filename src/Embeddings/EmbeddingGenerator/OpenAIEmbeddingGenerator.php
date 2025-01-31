<?php

declare(strict_types=1);

namespace LLPhant\Embeddings\EmbeddingGenerator;

use Exception;
use function getenv;
use LLPhant\Embeddings\Document;
use LLPhant\OpenAIConfig;
use OpenAI;
use OpenAI\Client;
use function str_replace;

final class OpenAIEmbeddingGenerator
{
    public Client $client;

    public string $modelName = 'text-embedding-ada-002';

    public function __construct(OpenAIConfig $config = null)
    {
        $apiKey = $config->apiKey ?? getenv('OPENAI_API_KEY');
        if (! $apiKey) {
            throw new Exception('You have to provide a OPENAI_API_KEY env var to request OpenAI .');
        }
        $this->client = OpenAI::client($apiKey);
    }

    /**
     * Call out to OpenAI's embedding endpoint.
     *
     * @return float[]
     */
    public function embedText(string $text): array
    {
        $text = str_replace("\n", ' ', $text);

        $response = $this->client->embeddings()->create([
            'model' => $this->modelName,
            'input' => $text,
        ]);

        return $response->embeddings[0]->embedding;
    }

    /**
     * @return float[]
     */
    public function embedDocument(Document $document): array
    {
        return $this->embedText($document->content);
    }
}
