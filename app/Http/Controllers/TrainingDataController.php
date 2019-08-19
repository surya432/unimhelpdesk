<?php

namespace App\Http\Controllers;

use App\TrainingData;
use Illuminate\Http\Request;
use Yajra\DataTables\Contracts\DataTable;
use DataTables;

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
                return '<a href="' . route("bayes.edit", $query->id) .
                    '" class="btn btn-xs btn-warning"><i class="glyphicon glyphicon-edit"></i> Edit</a>' .
                    '<a href="' . route("bayes.show", $query->id) .
                    '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-eye-open"></i> Show</a>' .
                    '<Button data-id="' . $query->id . '" id="btnDelete" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> delete</Button>';
            })
            ->rawColumns(['nama', 'action'])
            ->make(true);

    }
    function __construct()
    {
        $this->middleware('permission:tiket-list');
        $this->middleware('permission:tiket-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:tiket-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:tiket-delete', ['only' => ['destroy']]);
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
    public function destroy(TrainingData $trainingData)
    {
        //
    }
}
