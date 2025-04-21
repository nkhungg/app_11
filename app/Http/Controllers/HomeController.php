<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller {

    public function index() {
        return view( 'index' );
    }

    public function aboutus() {
        return view( 'aboutus' );
    }

    public function contact() {
        return view( 'contact' );
    }
    public function shop(){
        return view('shop');
    }
    public function account(){
        return view('account');
    }
    public function accountWishlist(){
        return view('account-wishlist');
    }

    public function accountOrder(){
        return view('account-order');
    }
}
