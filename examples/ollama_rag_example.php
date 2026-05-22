<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Imadepurnamayasa\PhpInti\Ollama\OllamaClient;
use Imadepurnamayasa\PhpInti\Ollama\Rag\RagOrchestrator;

// Set error reporting for development
ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "========================================================\n";
echo "    Ollama Semantic RAG (Retrieval-Augmented) System\n";
echo "========================================================\n\n";

// 1. Define Simulated Medical Knowledge Documents
$medicalDocuments = [
    [
        'title' => 'Gastroesophageal Reflux Disease (GERD) Clinical Guidelines',
        'content' => <<<TEXT
Gastroesophageal reflux disease (GERD) is a chronic digestive disease that occurs when stomach acid or, occasionally, stomach content, flows back into the food pipe (esophagus). The backwash (reflux) irritates the lining of your esophagus and causes GERD.
Common symptoms of GERD include:
- A burning sensation in your chest (heartburn), usually after eating, which might be worse at night.
- Chest pain.
- Difficulty swallowing (dysphagia).
- Regurgitation of food or sour liquid.
- Sensation of a lump in your throat.
Management and lifestyle recommendations:
1. Avoid lying down for at least 3 hours after eating a meal.
2. Avoid spicy, fatty, or acidic foods, as well as chocolate, caffeine, and alcohol which can relax the lower esophageal sphincter.
3. Elevate the head of your bed by 6 to 9 inches to prevent acid reflux during sleep.
4. Over-the-counter antacids, H2 blockers (like famotidine), or PPIs (like omeprazole) can provide relief.
TEXT
    ],
    [
        'title' => 'Gastritis - Diagnosis and Treatment Protocol',
        'content' => <<<TEXT
Gastritis is an inflammation, irritation, or erosion of the lining of the stomach. It can occur suddenly (acute) or gradually (chronic).
Common causes include infection with Helicobacter pylori (H. pylori) bacteria, regular use of pain relievers (NSAIDs like ibuprofen or naproxen), excessive alcohol use, or stress.
Symptoms of gastritis include:
- Gnawing or burning ache or pain (indigestion) in your upper abdomen (epigastrium) that may become either worse or better with eating.
- Nausea and vomiting.
- A feeling of fullness in your upper abdomen after eating.
Treatment typically involves:
- Antibiotics to eliminate H. pylori if present.
- Avoiding NSAID pain relievers; switch to acetaminophen if necessary.
- Proton pump inhibitors (PPIs) to block acid production and promote healing.
- Acid reducers (H2 blockers) to reduce the amount of acid released into your digestive tract.
TEXT
    ],
    [
        'title' => 'Acute Appendicitis - Emergency Medical Assessment',
        'content' => <<<TEXT
Acute appendicitis is a medical emergency that requires prompt surgical intervention to remove the appendix (appendectomy) before it ruptures. A ruptured appendix can spread infection throughout the abdomen (peritonitis), which is life-threatening.
Clinical presentation of Acute Appendicitis:
- Pain starts near the navel (umbilicus) and shifts to the lower right side of the abdomen (McBurney's point).
- The pain becomes sharp, severe, and worsens when walking, coughing, or making jarring movements.
- Low-grade fever that may worsen as the illness progresses.
- Loss of appetite, nausea, vomiting, and abdominal bloating or swelling.
CRITICAL EMERGENCY ACTION:
- If a patient presents with sharp, severe pain localized in the lower right abdomen accompanied by fever and nausea, they must go to the nearest Emergency Room (ER) immediately.
- Do NOT give the patient pain medications, laxatives, or antacids, as these can mask symptoms or cause the appendix to rupture.
- Maintain NPO status (Nothing by mouth - no food or drinks) in preparation for potential urgent surgery.
TEXT
    ]
];

// 2. Setup Ollama Client and RAG Orchestrator
try {
    $client = OllamaClient::create('http://localhost:11434');

    // Fetch local models to dynamically choose available models
    $models = $client->listModels();
    if (empty($models)) {
        throw new RuntimeException("No models found on your local Ollama instance. Please run 'ollama pull <model_name>' first.");
    }

    // Attempt to select an appropriate LLM model
    $llmModel = 'llama3'; // default choice
    $embeddingModel = 'all-minilm'; // default choice

    $availableModelNames = array_map(fn($m) => $m['name'], $models);
    echo "Available local Ollama models: " . implode(', ', $availableModelNames) . "\n\n";

    // Auto-select based on availability
    foreach (['llama3', 'llama3:latest', 'mistral', 'gemma', 'phi3'] as $candidate) {
        if (in_array($candidate, $availableModelNames)) {
            $llmModel = $candidate;
            break;
        }
    }
    // If our default/candidate isn't found, pick the first available one as fallback
    if (!in_array($llmModel, $availableModelNames)) {
        $llmModel = $availableModelNames[0];
    }

    // Auto-select embedding model
    foreach (['all-minilm', 'all-minilm:latest', 'nomic-embed-text', 'nomic-embed-text:latest', 'bge-large'] as $candidate) {
        if (in_array($candidate, $availableModelNames)) {
            $embeddingModel = $candidate;
            break;
        }
    }
    // Fallback to the same LLM model if no specific embedding model is installed
    if (!in_array($embeddingModel, $availableModelNames)) {
        $embeddingModel = $llmModel;
    }

    echo "--> Selected LLM Model for generation: \"{$llmModel}\"\n";
    echo "--> Selected Embedding Model:         \"{$embeddingModel}\"\n\n";

    // Create the RAG Orchestrator using the Facade factory method
    // Chunk size is set to 1000 characters, overlap to 200 characters to keep context intact
    echo "Initializing RAG Pipeline (using Strategy, Facade, and Builder patterns)...\n";
    $rag = RagOrchestrator::createDefault(
        $client,
        $llmModel,
        $embeddingModel,
        1000,
        200
    );

    // 3. Index Medical Documents
    echo "Indexing medical documents into the semantic InMemoryVectorStore...\n";
    foreach ($medicalDocuments as $doc) {
        echo "  - Ingesting: \"{$doc['title']}\"\n";
        $rag->indexText($doc['content'], [
            'source_file' => $doc['title']
        ]);
    }
    echo "Indexing completed successfully!\n\n";

    // 4. Run RAG Queries
    $queries = [
        "What are the key lifestyle recommendations for managing GERD?",
        "A patient is complaining of severe sharp pain in their lower right stomach and has a low-grade fever. What should they do?"
    ];

    foreach ($queries as $idx => $query) {
        $queryNum = $idx + 1;
        echo "========================================================\n";
        echo "QUERY #{$queryNum}: \"{$query}\"\n";
        echo "========================================================\n\n";

        // Step 4a: Demonstrate Context Retrieval (Semantic Search)
        echo "[STEP 1: Semantic Retrieval & Similarity Search]\n";
        echo "Querying Vector Database using cosine similarity...\n";
        
        $retrieved = $rag->retrieveContext($query, 2);
        
        if (empty($retrieved)) {
            echo "  No matching documents found.\n\n";
        } else {
            foreach ($retrieved as $rank => $match) {
                $rankNum = $rank + 1;
                $doc = $match['document'];
                $score = round($match['score'], 4);
                $source = $doc->getMetadata()['source_file'];
                $chunkIdx = $doc->getMetadata()['chunk_index'];
                
                echo "  Rank #{$rankNum} | Source: \"{$source}\" (Chunk #{$chunkIdx}) | Similarity Score: {$score}\n";
                echo "  --- Chunk Text ---\n";
                // Print a small indented snippet of the chunk
                $snippet = str_replace("\n", "\n    ", trim($doc->getContent()));
                echo "    {$snippet}\n";
                echo "  ------------------\n\n";
            }
        }

        // Step 4b: Complete RAG generation
        echo "[STEP 2: Retrieval-Augmented Generation (LLM)]\n";
        echo "Sending query and retrieved context to Ollama...\n\n";

        $systemPrompt = "You are a professional medical assistant and triage advisor. Respond objectively, precisely, and strictly based on the provided context guidelines.";
        $result = $rag->answerQuery($query, $systemPrompt, 2, [
            'temperature' => 0.1 // Low temperature for high factual accuracy
        ]);

        echo "[AI CLINICAL RESPONSE]\n";
        echo "--------------------------------------------------------\n";
        echo $result['response'] . "\n";
        echo "--------------------------------------------------------\n\n";
    }

} catch (\Exception $e) {
    echo "An error occurred during the RAG demonstration:\n";
    echo "Error Message: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting suggestions:\n";
    echo "1. Verify your local Ollama server is running (usually http://localhost:11434).\n";
    echo "2. Ensure the selected model is pulled: 'ollama pull {$llmModel}' or 'ollama pull {$embeddingModel}'.\n";
    echo "3. Check the PHP cURL extension is enabled.\n";
}

echo "========================================================\n";
echo "            End of Ollama RAG Demonstration\n";
echo "========================================================\n";
