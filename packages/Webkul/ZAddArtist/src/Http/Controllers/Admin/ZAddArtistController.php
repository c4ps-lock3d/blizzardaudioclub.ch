<?php

namespace Webkul\ZAddArtist\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Artiste;
use Webkul\ZAddArtist\Http\Requests\ZAddArtistRequest;
use Illuminate\Http\UploadedFile;


class ZAddArtistController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('zaddartist::admin.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $artiste = new Artiste();
        return view('zaddartist::admin.create', [
            'artistes' => $artiste,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Artiste $artiste, ZAddArtistRequest $request)
    {
        $artiste = new Artiste();
        /** @var UploadedFile $image */
        $image = $request->validated('image');
        $data['image'] = $image->store('artiste', 'public');
        $datavalidated = [
            'image' => $data['image'],
            'slug' => $request->validated('slug'),
            'name' => $request->validated('name'),
        ];
        $artiste->create($datavalidated);
        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        return view('zaddartist::admin.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        
    }
}
