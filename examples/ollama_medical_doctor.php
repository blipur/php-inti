<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Imadepurnamayasa\PhpInti\Ollama\OllamaClient;

// Set error reporting
ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "========================================================\n";
echo "Ollama Medical Doctor & Coding Assistant Demonstration\n";
echo "========================================================\n\n";

// 1. Define simulated patient encounter notes
$patientEncounter = <<<ENCOUNTER
Patient is a 45-year-old male presenting with sharp, burning epigastric abdominal pain for the last 3 days. 
The pain is worse postprandially (especially after eating spicy foods) and partially relieved by over-the-counter antacids. 
He reports mild nausea but denies vomiting, diarrhea, hematemesis, melena, or fever. 

Physical Exam & Vitals:
- BP: 128/82 mmHg
- Heart Rate: 78 bpm
- Temperature: 36.8 C (98.2 F)
- Abdomen: Soft, non-distended, with moderate tenderness localized to the epigastrium. No guarding or rebound tenderness. Bowel sounds are normal.
ENCOUNTER;

echo "Patient Encounter Clinical Notes:\n";
echo "---------------------------------\n";
echo $patientEncounter . "\n";
echo "---------------------------------\n\n";

// 2. Formulate the Doctor System Prompt
$systemPrompt = <<<SYSTEM
You are an expert clinical medical doctor and professional medical coding specialist.
Your task is to analyze patient encounter summaries or clinical notes and generate:
1. A structured clinical SOAP note:
   - Subjective (S): The patient's subjective complaints, symptoms, duration, and history.
   - Objective (O): Objective measurements (vitals, physical exams, clinical observations).
   - Assessment (A): The clinical impression, working diagnosis, or differential diagnoses.
   - Plan (P): The diagnostic workup, treatment/medication plan, patient education, and follow-up instructions.
2. The most relevant diagnostic coding:
   - ICD-10 code(s) (e.g. K29.7 for Gastritis, K30 for Dyspepsia) with code and description.
   - ICD-9 code(s) (e.g. 535.50 for Gastritis, 536.8 for Dyspepsia) with code and description.

You MUST respond strictly in valid JSON format. Do not write any explanations, preamble, or markdown formatting outside the JSON block. Ensure your JSON structure strictly matches this schema:
{
  "soap": {
    "subjective": "Detailed subjective summary...",
    "objective": "Detailed objective clinical findings...",
    "assessment": "Working diagnosis and differential diagnoses...",
    "plan": "Diagnostic, therapeutic, educational, and follow-up plan..."
  },
  "icd10": [
    {
      "code": "ICD-10 Code",
      "description": "Short description of the diagnosis"
    }
  ],
  "icd9": [
    {
      "code": "ICD-9 Code",
      "description": "Short description of the diagnosis"
    }
  ]
}
SYSTEM;

// 3. Connect to local Ollama and run query
try {
    $client = OllamaClient::create('http://localhost:11434');
    
    // Check which models are available locally
    $models = $client->listModels();
    if (empty($models)) {
        throw new \RuntimeException("No models found on your local Ollama instance.");
    }
    
    $selectedModel = $models[0]['name'];
    echo "Connecting to Ollama using model: \"{$selectedModel}\"...\n";
    echo "Processing medical analysis with JSON mode enabled...\n\n";
    
    // Construct the request utilizing our fluent Builder Pattern and Ollama JSON Format
    $chatBuilder = $client->createChatBuilder()
        ->model($selectedModel)
        ->addSystemMessage($systemPrompt)
        ->addUserMessage($patientEncounter)
        ->jsonFormat(true) // Enforce JSON Output mode
        ->options([
            'temperature' => 0.2 // Lower temperature for high clinical precision
        ]);
        
    $response = $client->chat($chatBuilder);
    $jsonResponseString = $response['message']['content'];
    
    // Decode JSON string to verify validity
    $medicalRecord = json_decode($jsonResponseString, true);
    
    if ($medicalRecord === null) {
        echo "Raw AI response could not be parsed as JSON. Raw output:\n";
        echo $jsonResponseString . "\n";
    } else {
        echo "=== CLINICAL ANALYSIS & CODING RESULTS ===\n\n";
        
        echo "[SOAP NOTE]\n";
        echo "S (Subjective):\n  " . $medicalRecord['soap']['subjective'] . "\n\n";
        echo "O (Objective):\n  " . $medicalRecord['soap']['objective'] . "\n\n";
        echo "A (Assessment):\n  " . $medicalRecord['soap']['assessment'] . "\n\n";
        echo "P (Plan):\n  " . $medicalRecord['soap']['plan'] . "\n\n";
        
        echo "[DIAGNOSTIC CODING]\n";
        echo "ICD-10 Classification:\n";
        foreach ($medicalRecord['icd10'] as $icd10) {
            echo "  - Code {$icd10['code']}: {$icd10['description']}\n";
        }
        echo "\n";
        
        echo "ICD-9 Classification:\n";
        foreach ($medicalRecord['icd9'] as $icd9) {
            echo "  - Code {$icd9['code']}: {$icd9['description']}\n";
        }
        echo "\n";
    }
    
} catch (\Exception $e) {
    echo "Could not perform medical analysis:\n";
    echo $e->getMessage() . "\n";
    echo "\nMake sure your local Ollama server is running and a model is loaded.\n";
}

echo "========================================================\n";
