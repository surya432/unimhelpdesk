<?php

namespace App\Http\Controllers;

use App\Services;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:services-list');
        $this->middleware('permission:services-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:services-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:tiket-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $data  = \App\Services::orderBy('id', 'DESC')->get();
        return view('admin.services.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.services.create');
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
            'name' => 'required|unique:services,name',
        ]);
        $input = $request->all();
        \App\Services::create($input);
        return redirect()->route('services.index')
            ->with('success', 'Services created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Related  $related
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $Services = \App\Services::findOrFail($id);
        return view('admin.services.show', compact('Services'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Related  $related
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $Services = \App\Services::findOrFail($id);
        return view('admin.services.edit', compact('Services'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Related  $related
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'name' => 'required',
        ]);


        $Services = \App\Services::find($id);
        $Services->name = $request->input('name');
        $Services->save();
        return redirect()->route('services.index')
            ->with('success', 'Service updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Related  $related
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        DB::table("services")->where('id', $id)->delete();
        return redirect()->route('services.index')
            ->with('success', 'Services deleted successfully');
    }
}
