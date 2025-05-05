<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Publisher;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
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

    public function products()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.products', compact('products'));
    }

    public function product_add()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-add', compact('categories'));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
        ]);
        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->image = $request->image;
        $product->category_id = $request->category_id;

        $current_timestamp = Carbon::now()->timestamp;

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;
        if($request->hasFile('images')){
            $allowedfileExtion = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            // Log::info('Request::UploadMultipleImage' . $request); // Debug line
            Log::info($request->file('images')); // Debug line
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtion);
                if($gcheck){
                    $gfileName = $current_timestamp . "-" . $counter . "." . $gextension;
                    $this->GenerateProductThumbnailImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
        }
        $product->images = $gallery_images;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been added successfully.');
    }

    public function GenerateProductThumbnailImage($image, $imageName)
    {
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->path());
        $img->cover(540, 689, 'top');
        $img->resize(540, 689, function ($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);

        $img->resize(104, 104, function ($constraint){
            $constraint->aspectRatio();
        })->save($destinationPathThumbnail.'/'.$imageName);
    }

    public function product_edit($id)
    {
        $product = Product::find($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        return view('admin.product-edit', compact('product', 'categories'));
    }

    public function product_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,'.$request->id,
            'short_description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
        ]);
        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $current_timestamp = Carbon::now()->timestamp;

        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/products').'/'.$product->image)){
                File::delete(public_path('uploads/products').'/'.$product->image);
            }
            if(File::exists(public_path('uploads/products/thumbnails').'/'.$product->image)){
                File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
            }
            $image = $request->file('image');
            $imageName = $current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;
        if($request->hasFile('images')){
            foreach (explode(',', $product->images) as $ofile) {
                if(File::exists(public_path('uploads/products').'/'.$ofile)){
                    File::delete(public_path('uploads/products').'/'.$ofile);
                }
                if(File::exists(public_path('uploads/products/thumbnails').'/'.$ofile)){
                    File::delete(public_path('uploads/products/thumbnails').'/'.$ofile);
                }
            }
            $allowedfileExtion = ['jpg', 'png', 'jpeg'];
            $files = $request->file('images');
            // Log::info('Request::UploadMultipleImage' . $request); // Debug line
            Log::info($request->file('images')); // Debug line
            foreach ($files as $file) {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension, $allowedfileExtion);
                if($gcheck){
                    $gfileName = $current_timestamp . "-" . $counter . "." . $gextension;
                    $this->GenerateProductThumbnailImage($file, $gfileName);
                    array_push($gallery_arr, $gfileName);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',', $gallery_arr);
            $product->images = $gallery_images;
        }
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product has been updated successfully.');
    }

    public function product_delete($id)
    {
        $product = Product::find($id);
        if(File::exists(public_path('uploads/products').'/'.$product->image)){
            File::delete(public_path('uploads/products').'/'.$product->image);
        }
        if(File::exists(public_path('uploads/products/thumbnails').'/'.$product->image)){
            File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
        }

        foreach (explode(',', $product->images) as $ofile) {
            if(File::exists(public_path('uploads/products').'/'.$ofile)){
                File::delete(public_path('uploads/products').'/'.$ofile);
            }
            if(File::exists(public_path('uploads/products/thumbnails').'/'.$ofile)){
                File::delete(public_path('uploads/products/thumbnails').'/'.$ofile);
            }
        }

        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product has been deleted successfully.');
    }

    public function publishers()
    {
        $publishers = Publisher::orderBy('id', 'DESC')->paginate(10);
        return view('admin.publishers', compact('publishers'));
    }

    public function add_publisher()
    {
        return view('admin.publisher-add');
    }

    public function publisher_store(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'slug'=>'required|unique:publishers,slug',
            'image'=>'mimes:png,jpg,jpeg|max:2048'
        ]);
        $publisher = new Publisher();
        $publisher->name=$request->name;
        $publisher->slug=Str::slug($request->name);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extension;
        $this->GeneratePublisherThumbnailsImage($image, $file_name);
        $publisher->image = $file_name;
        $publisher->save();
        return redirect()->route('admin.publishers')->with('status', 'Publisher has been add successfully!');
    }

    // not good

    public function publisher_edit($id)
    {
        $publisher = Publisher::find($id);
        return view('admin.publisher-edit', compact('publisher'));
    }

    public function publisher_update(Request $request)
    {
        $request->validate([
            'name'=>'required',
            'slug'=>'required|unique:publishers,slug,'.$request->id,
            'image'=>'mimes:png,jpg,jpeg|max:2048'
        ]);
        $publisher=Publisher::find($request->id);
        $publisher->name=$request->name;
        $publisher->slug=Str::slug($request->name);

        if ($request->hasFile('image'))
        {
            if (File::exists(public_path('uploads/publishers').'/'.$publisher->image))
            {
                File::delete(public_path('uploads/publishers').'/'.$publisher->image);
            }
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_extension;
            $this->GeneratePublisherThumbnailsImage($image, $file_name);
            $publisher->image = $file_name;
        }
        // $publisher->save();
        // return redirect()->route('admin.publishers')->with('status', 'Publisher has been updated successfully!');
        if ($publisher->save()) {
            return redirect()->route('admin.publishers')->with('status', 'Publisher has been updated successfully!');
        } else {
            return redirect()->route('admin.publishers')->with('error', 'Failed to update publisher!');
        }
    }

    public function GeneratePublisherThumbnailsImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/publishers');
        $img = Image::read($image->path());
        $img->cover(124, 124, "top");
        $img->resize(124, 124, function($constraint)
        {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }

    public function publisher_delete($id)
    {
        $publisher=Publisher::find($id);
        if (File::exists(public_path('uploads/publishers').'/'.$publisher->image))
        {
            File::delete(public_path('uploads/publishers').'/'.$publisher->image);
        }
        $publisher->delete();
        return redirect()->route('admin.publishers')->with('status', "Publisher has been deleted successfully!");
    }
}
