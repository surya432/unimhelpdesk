<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class PrioritasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('role:SuperAdmin');
        $this->middleware('permission:prioritas-list');
        $this->middleware( 'permission:prioritas-create', ['only' => ['create', 'store']]);
        $this->middleware( 'permission:prioritas-edit', ['only' => ['edit', 'update']]);
        $this->middleware( 'permission:prioritas-delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = \App\Prioritas::orderBy('id', 'DESC')->paginate(5);
        return view( 'admin.prioritas.index', compact( 'data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view( 'admin.prioritas.create');
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
            'name' => 'required|unique:prioritas,name',
        ]);

        \App\Prioritas::create(['name' => $request->input('name')]);
        
        return redirect()->route( 'prioritas.index')
            ->with('success', 'Prioritas created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = \App\Prioritas::find($id);

        return view('admin.prioritas.show', compact('role'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = \App\Prioritas::find($id);
        return view( 'admin.permission.edit', compact( 'permission'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);


        $role = \App\Prioritas::find($id);
        $role->name = $request->input('name');
        $role->save();



        return redirect()->route( 'prioritas.index')
            ->with('success', 'Prioritas updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table( "prioritas")->where('id', $id)->delete();
        return redirect()->route( 'prioritas.index')
            ->with('success', 'Prioritas deleted successfully');
    }
}
