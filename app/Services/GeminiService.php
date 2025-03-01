<?php
// Langkah 1: Buat proyek Laravel baru (jika belum ada)
// composer create-project laravel/laravel laravel-gemini-ai

// Langkah 2: Install Guzzle HTTP Client untuk membuat request API
// composer require guzzlehttp/guzzle

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl;
    protected $client;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
        $this->client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    /**
     * Generate text using Gemini Pro model
     */
    public function generateText($prompt, $model = 'gemini-1.5-pro')
    {
        try {
            $url = "{$this->baseUrl}/models/{$model}:generateContent?key={$this->apiKey}";
            
            $response = Http::post($url, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 800,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                // Extract generated text from response
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    return $result['candidates'][0]['content']['parts'][0]['text'];
                }
                
                return $result;
            } else {
                Log::error('Gemini API error: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Gemini API exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Analyze image with text prompt using Gemini Pro Vision
     */
    public function analyzeImage($prompt, $imageData, $mimeType = 'image/jpeg', $model = 'gemini-1.5-flash')
    {
        try {
            $url = "{$this->baseUrl}/models/{$model}:generateContent?key={$this->apiKey}";
            
            // Encode image to base64
            $base64Image = base64_encode($imageData);
            
            $response = Http::post($url, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $base64Image
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.4,
                    'maxOutputTokens' => 800,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                // Extract generated text from response
                if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    return $result['candidates'][0]['content']['parts'][0]['text'];
                }
                
                return $result;
            } else {
                Log::error('Gemini Vision API error: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Gemini Vision API exception: ' . $e->getMessage());
            return null;
        }
    }
}