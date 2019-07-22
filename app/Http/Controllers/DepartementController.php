<?php

namespace App\Http\Controllers;

use App\Departement;
use Illuminate\Http\Request;

use DB;

class DepartementController extends Controller
{
    function __construct()
    {   

        $this->middleware( 'role:SuperAdmin');
        $this->middleware('permission:role-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:role-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:role-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request)
    {
        //
        $data = \App\Departement::orderBy('id', 'desc')->get();;
        return view('admin.dept.index', compact( 'data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.dept.create');

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
            'name' => 'required|unique:departements,name'
        ]);
        Departement::create(['name' => $request->input('name')]);
        return redirect()->route( 'departement.index')
            ->with('success', 'Departement created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Departement  $departement
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        //
        $departement = \App\Departement::find($id);
        return view('admin.dept.show', compact( 'departement'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Departement  $departement
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        //
        $departement  = \App\Departement::find($id);
        return view('admin.dept.edit', compact( 'departement'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Departement  $departement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Departement $departement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Departement  $departement
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        DB::table("roles")->where('id', $id)->delete();
        return redirect()->route( 'departement.index')
            ->with('success', 'Role deleted successfully');
    }
}
