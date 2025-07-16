<?php
namespace App\Http\Controllers;

use App\Models\Product;

class FreeProductController extends Controller {
    public function index() {
        $ebooks = Product::where( 'type', 'ebook' )
        ->whereHas( 'ebook', fn( $q ) => $q->whereNotNull( 'file_path' ) )
        ->get();

        $audiobooks = Product::where( 'type', 'audiobook' )
        ->whereHas( 'audiobook', fn( $q ) => $q->whereNotNull( 'file_path' ) )
        ->get();

        return view( 'free-products', compact( 'ebooks', 'audiobooks' ) );
    }
}
