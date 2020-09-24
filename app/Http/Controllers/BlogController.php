<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Blog::latest()->paginate(10);
        return view('blog.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view ('blog.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:png,jpg,jpeg',
            'judul' => 'required',
            'deskripsi' => 'required'
        ]);

        $image = $request->file('image');
        $image->storeAs('public/blogs', $image->hashName());

        $blog = Blog::create([
            'image'     => $image->hashName(),
            'judul'     => $request->judul,
            'deskripsi'   => $request->deskripsi
        ]);


        if($blog){
            return redirect()->route('blog.index')->with(['success' => 'Berhasil Input Data']);
        } else {
            return redirect()->route('blog.index')->with(['error' => 'Gagal Input Data']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        return view('blog.edit', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blog $blog)
    {
        $this->validate($request, [
            'judul' => 'required',
            'deskripsi' => 'required'
        ]);

        $blog = Blog::findOrFail($blog->id);

        if($request->file('image') == ""){
            
            $blog->update([
                'judul'     => $request->judul,
                'deskripsi'   => $request->deskripsi
            ]);

        } 
        
        else {

            // hapus gambar local yang di update
            Storage::disk('local')->delete('public/blogs/'.$blog->image);
            
            // input gambar baru
            $image = $request->file('image');
            $image->storeAs('public/blogs', $image->hashName());
    
            $blog->update([
                'image'     => $image->hashName(),
                'judul'     => $request->judul,
                'deskripsi'   => $request->deskripsi
            ]);
    
        }

        if($blog){
            //redirect dengan pesan sukses
            return redirect()->route('blog.index')->with(['success' => 'Data Berhasil Diupdate!']);
        }else{
            //redirect dengan pesan error
            return redirect()->route('blog.index')->with(['error' => 'Data Gagal Diupdate!']);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        Storage::disk('local')->delete('public/blogs/'.$blog->image);
        $blog->delete();
      
        if($blog){
           //redirect dengan pesan sukses
           return redirect()->route('blog.index')->with(['success' => 'Data Berhasil Dihapus!']);
        }else{
          //redirect dengan pesan error
          return redirect()->route('blog.index')->with(['error' => 'Data Gagal Dihapus!']);
        }
    }
}
