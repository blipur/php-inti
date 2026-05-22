<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Imadepurnamayasa\PhpInti\Ollama\OllamaClient;
use Imadepurnamayasa\PhpInti\Ollama\Connection\MockConnection;

// Enable error reporting for demonstration purposes
ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "========================================================\n";
echo "Ollama API Integration Demonstration\n";
echo "========================================================\n\n";

//--------------------------------------------------------------------------
// 1. DEMONSTRATION WITH MOCK CONNECTION
//--------------------------------------------------------------------------
echo "--- PART 1: Demonstration using MockConnection ---\n";

// Instantiate the mock connection for safe testing
$mockConnection = new MockConnection();
$client = new OllamaClient($mockConnection);

// A. Listing models
echo "Listing local models:\n";
$models = $client->listModels();
foreach ($models as $model) {
    echo "- Name: " . $model['name'] . " (Size: " . round($model['size'] / (1024 * 1024 * 1024), 2) . " GB, Family: " . $model['details']['family'] . ")\n";
}
echo "\n";

// B. Basic generate completion request using Builder Pattern
echo "Sending a text generation request...\n";
$generateBuilder = $client->createGenerateBuilder()
    ->model('llama3')
    ->prompt('Why is the sky blue?')
    ->system('Explain simple concepts like a teacher.')
    ->options([
        'temperature' => 0.7,
        'seed' => 42
    ]);

$generateResponse = $client->generate($generateBuilder);
echo "Response from Ollama:\n";
echo $generateResponse['response'] . "\n\n";

// C. Chat conversation request using Builder Pattern
echo "Sending a chat conversation request...\n";
$chatBuilder = $client->createChatBuilder()
    ->model('llama3')
    ->addSystemMessage('You are a helpful travel assistant.')
    ->addUserMessage('What are the best tourist spots in Bali?')
    ->addAssistantMessage('Bali offers beautiful beaches like Kuta, cultural centers like Ubud, and iconic temples like Tanah Lot.')
    ->addUserMessage('Tell me more about Ubud.');

$chatResponse = $client->chat($chatBuilder);
echo "Assistant's Reply:\n";
echo $chatResponse['message']['content'] . "\n\n";


//--------------------------------------------------------------------------
// 2. DEMONSTRATION WITH REAL CONNECTION (Live execution)
//--------------------------------------------------------------------------
echo "--- PART 2: Connecting to a Real Ollama Service ---\n";
try {
    // Create the client pointing to your local Ollama port 11434
    $realClient = OllamaClient::create('http://localhost:11434');
    
    echo "Querying real local models...\n";
    $realModels = $realClient->listModels();
    
    if (empty($realModels)) {
        echo "No models found on the local Ollama server. Please run 'ollama pull llama3' in your terminal.\n\n";
    } else {
        echo "Real local models found on your machine:\n";
        $firstModelName = '';
        foreach ($realModels as $model) {
            if (empty($firstModelName)) {
                $firstModelName = $model['name'];
            }
            echo "- Name: " . $model['name'] . " (Size: " . round($model['size'] / (1024 * 1024 * 1024), 2) . " GB)\n";
        }
        
        // Let's use the first model found for a real live test!
        echo "\nUsing model: \"{$firstModelName}\"\n";
        echo "Sending live generation request to your real local Ollama server...\n";
        
        $realGenerateBuilder = $realClient->createGenerateBuilder()
            ->model($firstModelName)
            ->prompt('Explain in one short sentence why Bali is a beautiful tourist destination.');
            
        $realResponse = $realClient->generate($realGenerateBuilder);
        echo "Real Ollama Response:\n";
        echo $realResponse['response'] . "\n\n";
    }
} catch (\Exception $e) {
    echo "Could not connect to live Ollama server or query failed:\n";
    echo $e->getMessage() . "\n\n";
}

echo "========================================================\n";
echo "Demonstration Completed Successfully!\n";
echo "========================================================\n";
