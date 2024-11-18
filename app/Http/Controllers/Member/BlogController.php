<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\str;

use function PHPUnit\Framework\fileExists;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $user = Auth::user();
        $data = post::where('user_id',$user->id)->orderBy('id','desc')->paginate(5);
        return view('member.blogs.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('member.blogs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Validate the incoming request data
    $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'thumbnail' => 'image|mimes:jpeg,jpg,png|max:15240'
    ], [
        'title.required' => 'Judul wajib diisi',
        'content.required' => 'Konten wajib diisi',
        'thumbnail.image' => 'Hanya gambar yang diperbolehkan',
        'thumbnail.mimes' => 'Ekstensi yang diperbolehkan hanya JPEG, JPG, dan PNG',
        'thumbnail.max' => 'Ukuran maksimum untuk thumbnail adalah 15MB'
    ]);

    // Initialize the data array
    $data = [
        'title' => $request->title,
        'description' => $request->description,
        'content' => $request->content,
        'status' => $request->status,
        'slug' => $this->generateSlug($request->title),
        'user_id' => Auth::user()->id,
    ];

    // Handle thumbnail upload
    if ($request->hasFile('thumbnail')) {
        $image = $request->file('thumbnail');
        $image_name = time() . "_" . $image->getClientOriginalName();
        
        // Store the thumbnail in the public storage
        try {
            // Store the image in the public disk
            $destination_path = public_path(getenv('CUSTOM_THUMBNAIL_LOCATION'));
            $image->move($destination_path, $image_name);
            Storage::disk('public')->putFileAs('thumbnails', $image, $image_name);
            $data['thumbnail'] = $image_name;
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Thumbnail upload failed: ' . $e->getMessage());
            return redirect()->back()->withErrors(['thumbnail' => 'Gagal mengunggah thumbnail'])->withInput();
        }
    }

    // Create the new post
    try {
        post::create($data);
    } catch (\Exception $e) {
        // Log the error message
        Log::error('Post creation failed: ' . $e->getMessage());
        return redirect()->back()->withErrors(['general' => 'Gagal membuat postingan'])->withInput();
    }
        return redirect()->route('member.blogs.index')->with('success', 'Data berhasil di-tambah');
    }

    /**
     * Display the specified resource.
     */
    public function show(post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(post $post)
    {
        $data = $post;
        return view('member.blogs.edit',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, post $post)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'thumbnail'=>'image|mimes:jpeg,jpg,png|max:15240'
    ],[
        'title.required'=>'Judul wajib diisi',
        'content.required'=>'konten wajib diisi',
        'thumbnail.image'=>'hanya gambar yang diperbolehkan',
        'thumbnail.mimes'=>'Ekstensi yang diperbolehkan hanya JPEG, JPG, dan PNG',
        'thumbnail.max'=>'Ukuran maksimum untuk thumbnail adalah 15MB'
    ]);

    

    if ($request->hasFile('thumbnail')) {
        // Construct the file path
        $filePath = public_path(getenv('CUSTOM_THUMBNAIL_LOCATION')) . "/" . $post->thumbnail;

        // Check if the file exists before trying to delete it
        if (isset($post->thumbnail) && file_exists($filePath)) {
            unlink($filePath);
        } else {
            // Log the error or output for debugging
            Log::error("File does not exist: " . $filePath);
        }

        $image = $request->file('thumbnail');
        $image_name = time() . "_" . $image->getClientOriginalName();
        Storage::disk('public')->putFileAs('thumbnails', $image, $image_name);
        $destination_path = public_path(getenv('CUSTOM_THUMBNAIL_LOCATION'));
        $image->move($destination_path, $image_name);
        $data['thumbnail'] = $image_name;
    }

    $data = [
        'title' => $request->title,
        'description' => $request->description,
        'content' => $request->content,
        'status' => $request->status,
        'thumbnail' => isset($image_name) ? $image_name : $post->thumbnail,
        'slug' => $this->generateSlug($request->title,$post->id)
    ];

    if ($request->hasFile('thumbnail') && Storage::disk('public')->exists('thumbnails/' . $image_name)) {
        $data['thumbnail'] = $image_name;
    }

    $post->update($data);
    return redirect()->route('member.blogs.index')->with('success', 'Data berhasil di-update');
    }

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(post $post)
    {
        //
    }

    private function generateSlug($title,$id=null){
        $slug = Str::slug($title);
        $count = post::where('slug',$slug)->when($id,function($query,$id){
            return $query->where('id','!=',$id);
        })->count();

        if($count > 0){
            $slug = $slug .  '-' . ($count + 1);
        }
        return $slug;
    }

}
