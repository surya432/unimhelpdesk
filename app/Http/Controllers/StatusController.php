<?php

namespace App\Http\Controllers;

use App\Status;
use Illuminate\Http\Request;
use DB;
class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('role:SuperAdmin');
        $this->middleware('permission:status-list');
        $this->middleware('permission:status-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:status-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:status-delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = \App\Status::orderBy('id', 'DESC')->get();
        return view('admin.status.index', compact('data'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.status.create');
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
            'name' => 'required|unique:statuses,name',
        ]);

        \App\Status::create(['name' => $request->input('name')]);

        return redirect()->route('status.index')
            ->with('success', 'status created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = \App\Status::find($id);

        return view('admin.status.show', compact('role'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = \App\Status::find($id);
        return view('admin.permission.edit', compact('permission'));
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


        $role = \App\Status::find($id);
        $role->name = $request->input('name');
        $role->save();



        return redirect()->route('status.index')
            ->with('success', 'Status updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table("statuses")->where('id', $id)->delete();
        return redirect()->route('status.index')
            ->with('success', 'Status deleted successfully');
    }
}
