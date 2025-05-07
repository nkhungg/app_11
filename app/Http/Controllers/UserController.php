<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller {
    public function index() {
        return view( 'user.index' );
    }

    public function account_detail() {
        $user = Auth::user();
        return view( 'user.account-detail', compact( 'user' ) );
    }

    public function account_update( Request $request ) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validate general info
        $request->validate( [
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ] );

        // Update general info
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->email = $request->email;

        // Check if user wants to update password
        if ( $request->filled( 'old_password' ) || $request->filled( 'new_password' ) || $request->filled( 'confirm_password' ) ) {
            $request->validate( [
                'old_password' => 'required',
                'new_password' => 'required|min:8|confirmed', // matches confirm_password
            ] );

            if ( !Hash::check( $request->old_password, $user->password ) ) {
                return back()->withErrors( [ 'old_password' => 'Old password is incorrect.' ] );
            }

            $user->password = Hash::make( $request->new_password );
        }

        $user->save();

        return back()->with( 'success', 'Account updated successfully.' );
    }
}
