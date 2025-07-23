<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use Illuminate\Http\Request;

class AIController extends Controller {
    protected $aiService;

    public function __construct( AIService $aiService ) {
        $this->aiService = $aiService;
    }

    public function suggestCategory( Request $request ) {
        $request->validate( [
            'title' => 'required|string',
            'author' => 'required|string',
        ] );

        try {
            $category = $this->aiService->suggestCategory(
                $request->input( 'title' ),
                $request->input( 'author' )
            );

            return response()->json( [
                'success' => true,
                'category' => $category
            ] );

        } catch ( \Exception $e ) {
            return response()->json( [
                'success' => false,
                'message' => 'Failed to suggest category',
                'error' => $e->getMessage()
            ], 500 );
        }
    }

    public function generateDescription( Request $request ) {
        $request->validate( [
            'title' => 'required|string',
            'author' => 'required|string',
        ] );

        try {
            $description = $this->aiService->generateDescription(
                $request->input( 'title' ),
                $request->input( 'author' )
            );

            return response()->json( [
                'success' => true,
                'description' => $description
            ] );

        } catch ( \Exception $e ) {
            return response()->json( [
                'success' => false,
                'message' => 'Failed to generate description',
                'error' => $e->getMessage()
            ], 500 );
        }
    }
}
