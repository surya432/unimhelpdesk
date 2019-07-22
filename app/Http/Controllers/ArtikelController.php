<?php

namespace App\Http\Controllers;

use App\Artikel;
use Illuminate\Http\Request;
use DB;
use Spatie\Permission\Models\Role;

class ArtikelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        //$this->middleware('permission:artikel-list');
        $this->middleware('permission:artikel-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:artikel-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:artikel-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {
        //
        $departementId = Role::where('name', $request->user()->getRoleNames())->get();
        $data = $departementId['0']['id'];
        $data = \App\Artikel::where('departement_id',$data)->orderBy('updated_at', 'DESC')->get();
        return view('admin.artikel.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $departementId = Role::where('name', $request->user()->getRoleNames())->get();
        $data = $departementId['0']['id'];
        $name = $request->user()->name;
        return view('admin.artikel.create', compact('data','name'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name' => 'required',
            'body' => 'required',
            'created_by' => 'required',
            'departement_id' => 'required'
        ]);
        $user = \App\Artikel::create($request->all());
        return redirect()->route('artikel.index')
            ->with('success', 'Artikel created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Artikel  $artikel
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        //

     
        $datacontent = \App\Artikel::find($id);
        return view('admin.artikel.show', compact('datacontent'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Artikel  $artikel
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //
        $departementId = Role::where('name', $request->user()->getRoleNames())->get();
        $data = $departementId['0']['id'];
        $name = $request->user()->name;
        $datacontent = \App\Artikel::find($id);
        return view('admin.artikel.edit', compact('datacontent'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Artikel  $artikel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'name' => 'required',
            'body' => 'required',
            'created_by' => 'required',
            'departement_id' => 'required'
        ]);
        $content = \App\Artikel::find($id);
        $content->name = $request->input('name');
        $content->body = $request->input('body');
        $content->created_by = $request->input('created_by');
        $content->departement_id = $request->input('departement_id');
        $content->save();
        return redirect()->route('artikel.index')
            ->with('success', 'Artikel Updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Artikel  $artikel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        DB::table("artikels")->where('id', $id)->delete();
        return redirect()->route('artikel.index')
            ->with('success', 'Artikel deleted successfully');
    }
}
