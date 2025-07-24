<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller {
    public function suggestCategory( Request $request ) {
        $request->validate( [
            'title' => 'required|string',
            'author' => 'required|string',
        ] );

        $prompt = "Suggest the most fitting book category (like Fantasy, Romance, Biography, etc.) for the book titled '{$request->title}' by {$request->author}. Respond with only the category name.";

        $response = $this->callOpenAI( $prompt );

        return response()->json( [
            'category' => trim( $response )
        ] );
    }

    public function generateDescription( Request $request ) {
        $request->validate( [
            'title' => 'required|string',
            'author' => 'required|string',
        ] );

        $prompt = "Write a short and engaging description for a book titled '{$request->title}' by {$request->author}. Make it appealing for potential readers.";

        $response = $this->callOpenAI( $prompt );

        return response()->json( [
            'description' => trim( $response )
        ] );
    }

    private function callOpenAI( string $prompt ): string {
        $apiKey = config( 'services.openai.key' );

        $response = Http::withToken( $apiKey )->post( 'https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'messages' => [
                [ 'role' => 'system', 'content' => 'You are a helpful assistant that categorizes books and writes descriptions.' ],
                [ 'role' => 'user', 'content' => $prompt ]
            ],
            'temperature' => 0.7,
        ] );

        return $response->json( 'choices.0.message.content' ) ?? 'Unknown';
    }
}
