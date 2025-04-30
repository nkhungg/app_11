<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
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
