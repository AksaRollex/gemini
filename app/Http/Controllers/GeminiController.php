<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;

class GeminiController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Tampilkan halaman untuk menggunakan Gemini AI
     */
    public function index()
    {
        return view('gemini.index');
    }

    /**
     * Proses permintaan generasi teks
     */
    public function generateText(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
        ]);

        $prompt = $request->input('prompt');
        $result = $this->geminiService->generateText($prompt);

        return response()->json([
            'success' => !empty($result),
            'result' => $result
        ]);
    }

    /**
     * Proses permintaan analisis gambar
     */
    public function analyzeImage(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'image' => 'required|image|max:2048',
        ]);

        $prompt = $request->input('prompt');
        $image = file_get_contents($request->file('image')->path());
        $mimeType = $request->file('image')->getMimeType();

        $result = $this->geminiService->analyzeImage($prompt, $image, $mimeType);

        return response()->json([
            'success' => !empty($result),
            'result' => $result
        ]);
    }
}