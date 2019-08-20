<?php

namespace App\Http\Controllers;

use App\TrainingData;
use Illuminate\Http\Request;
use Yajra\DataTables\Contracts\DataTable;
use DataTables;
use DB;
class TrainingDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function json(){
        $data = \App\TrainingData::orderBy('created_at','desc');

        return DataTables::of($data)
            // ->rawColumns(['keys', 'value'])
            ->addColumn('action', function ($query) {
                return 
                    '<Button data-id="' . $query->id . '" id="btnDelete" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> delete</Button>';
            })
            ->rawColumns(['nama', 'action'])
            ->make(true);

    }
    function __construct()
    {
        //$this->middleware('permission:bayes-list');
        $this->middleware('permission:bayes-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:bayes-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:bayes-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        //
        return view('admin.bayes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\TrainingData  $trainingData
     * @return \Illuminate\Http\Response
     */
    public function show(TrainingData $trainingData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\TrainingData  $trainingData
     * @return \Illuminate\Http\Response
     */
    public function edit(TrainingData $trainingData)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TrainingData  $trainingData
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TrainingData $trainingData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TrainingData  $trainingData
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request )
    {
        //
        $data = \App\TrainingData::where('id', $request->input('id'))->first();
        $data->delete();
        return response()->json("delete Berhasil", 200);
    }
}
