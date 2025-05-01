<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller {
    public function index() {
        return view( 'admin.index' );
    }

    public function categories() {
        $categories = Category::orderBy( 'id', 'DESC' )->paginate( 10 );
        return view( 'admin.categories', compact( 'categories' ) );
    }

    public function category_add() {
        return view( 'admin.category-add' );
    }

    public function category_store( Request $request ) {
        $request->validate( [
            'name'=> 'required',
            'slug'=> 'required|unique:categories,slug',
            'image'=> 'mimes:png,jpg,jpeg|max:2048'
        ] );

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug( $request->name );
        $image = $request->file( 'image' );
        $file_extension = $request->file( 'image' )->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateCategoryThumbailsImage( $image, $file_name );
        $category->image = $file_name;
        $category->save();
        return redirect()->route( 'admin.categories' )->with( 'status', 'Category has been added successfully.' );
    }

    public function GenerateCategoryThumbailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->path());
        $img->cover(124, 124, 'top');
        $img->resize(124, 124, function ($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function category_edit($id)
    {
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate( [
            'name'=> 'required',
            'slug'=> 'required|unique:categories,slug,'.$request->id,
            'image'=> 'mimes:png,jpg,jpeg|max:2048'
        ] );
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = Str::slug( $request->name );
        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/categories').'/'.$category->image)){
                File::delete(public_path('uploads/categories').'/'.$category->image);
            }
            $image = $request->file( 'image' );
            $file_extension = $request->file( 'image' )->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GenerateCategoryThumbailsImage( $image, $file_name );
            $category->image = $file_name;
        }
        $category->save();
        return redirect()->route( 'admin.categories' )->with( 'status', 'Category has been updated successfully.' );
    }

    public function category_delete($id)
    {
        $category = Category::find($id);
        if(File::exists(public_path('uploads/categories').'/'.$category->image)){
            File::delete(public_path('uploads/categories').'/'.$category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category has been deleted sucessfully.');
    }
}
