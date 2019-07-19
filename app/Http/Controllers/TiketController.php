<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Storage;

class TiketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:tiket-list');
        $this->middleware('permission:tiket-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:tiket-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:tiket-delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //dd( Auth::user()->getRoleNames());
        if ( Auth::user()->hasRole('User')) {
            $data = \App\Tiket::join('users', 'tikets.user_id', 'users.id')
                ->join('content_tikets', 'content_tikets.id', 'tikets.id')
                ->join('departements', 'departements.id', 'tikets.departement_id')
                ->join('statuses', 'statuses.id', 'tikets.status_id')
                ->where('tikets.user_id',Auth::user()->id)
                ->select('tikets.*', 'users.name as userName', 'departements.name as departementName', 'statuses.name as statusName')
                ->orderBy('id', 'DESC')->paginate(10);
        } else if ( Auth::user()->hasRole("SuperAdmin")) {
            $data = \App\Tiket::join('users', 'tikets.user_id', 'users.id')
                ->join('content_tikets', 'content_tikets.id', 'tikets.id')
                ->join('departements', 'departements.id', 'tikets.departement_id')
                ->join('statuses', 'statuses.id', 'tikets.status_id')
                ->select('tikets.*', 'users.name as userName', 'departements.name as departementName', 'statuses.name as statusName')
                ->orderBy('id', 'DESC')->paginate(10);
        }else{
            $departementId = Role::where('name', Auth::user()->getRoleNames())->get();
            $data = \App\Tiket::join('users', 'tikets.user_id', 'users.id')
                ->join('content_tikets', 'content_tikets.id', 'tikets.id')
                ->join('departements', 'departements.id', 'tikets.departement_id')
                ->join('statuses', 'statuses.id', 'tikets.status_id')
                ->select('tikets.*', 'users.name as userName', 'departements.name as departementName', 'statuses.name as statusName')
                ->where('tikets.departement_id' , $departementId['0']['id'])
                ->orderBy('id', 'DESC')->paginate(10);
        }
        return view('admin.tiket.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        //dd( Auth::user());
        $user  = \App\User::join('model_has_roles', 'model_has_roles.model_id', 'users.id')
            ->join('roles', 'roles.id', 'model_has_roles.role_id')
            ->select('users.name', "users.id")
            ->where('roles.name', 'User')->get();
       
        $prioritas = \App\Prioritas::all();
        $status = \App\Status::all();
        $services = \App\Services::all();
        $departement =Role::whereNotIn('name', ['SuperAdmin', 'User'])->get();
        return view('admin.tiket.create', compact('user', 'prioritas', 'status', 'departement', 'services'));
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
            'subject' => 'required',
            'body' => 'required',
            'prioritas_id' => 'required',
            'user_id' => 'required',
            'status_id' => 'required',
            'services_id' => 'required',
            'departement_id' => 'required',
            'senders' => 'required',
            'attachment' => 'max:5000',

        ]);
            $tiket = \App\Tiket::create($request->only('subject', 'user_id', 'prioritas_id', 'status_id', 'departement_id', 'services_id'));
            $content = new \App\Content_tiket;
            $content->body = $request->input('body');
            $content->senders = $request->input('senders');
            $content->tiket_id = $tiket->id;
        $repply = 1;
        if ($request->user()->hasRole("User")) {
            $repply = 0;
        }
        $content->repply = $repply;
            $content->save();
            if( $request->hasFile( 'attachment')){
                foreach ($request->file( 'attachment') as $file) {
                    $name = md5(now()).$file->getClientOriginalName();
                    $upload_success = $file->move( public_path( 'attachment'), $name);
                try {
                    $mime = $file->getMimeType();
                } catch (\Exception $e) {
                    $mime = $file->getClientMimeType();
                }
                \App\Attachment::create(["name" => $name, "file" => "attachment/$name", "mime" =>  $mime, "content_tiket_id" => $content->id]);
                }
            }

        return redirect()->route('tiket.index')
            ->with('success', 'tiket created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tiket = \App\Tiket::findOrFail($id);
        $contentTiket = \App\Content_tiket::where('tiket_id', $tiket->id)->orderBy('id',"DESC")->get();
        $status = \App\Status::all();
        $tiket = \App\Tiket::join('users', 'tikets.user_id', 'users.id')
            ->join('content_tikets', 'content_tikets.id', 'tikets.id')
            ->join('departements', 'departements.id', 'tikets.departement_id')
            ->join('statuses', 'statuses.id', 'tikets.status_id')
            ->join('prioritas', 'prioritas.id', 'tikets.prioritas_id')
            ->where('tikets.id', $tiket->id)
            ->select('tikets.*', 'users.name as userName', 'prioritas.name as prioritasName', 'departements.name as departementName', 'statuses.name as statusName')
            ->first();
        return view('admin.tiket.show', compact( 'tiket', 'contentTiket', 'status'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = \App\Tiket::find($id);
        return view('admin.tiket.edit', compact('permission'));
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
            'status_id' => 'required',
            'attachment' => 'max:5000',
        ]);


        $tiket = \App\Tiket::find($id);
        $tiket-> status_id = $request->input( 'status_id');
        $tiket->save();


        return redirect()->route('tiket.index')
            ->with('success', 'tiket updated successfully');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table("tikets")->where('id', $id)->delete();
        return redirect()->route('tiket.index')
            ->with('success', 'tiket deleted successfully');
    }

    public function replyTiket(Request $request){
        $this->validate($request, [
            'body' => 'required',
            'senders' => 'required',
            'repply' => 'required',
            'tiket_id' => 'required',
            'attachment' => 'max:5000',
        ]);

        $content = \App\Content_tiket::Create($request->all());
         if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $name = md5(now()) . $file->getClientOriginalName();
                $upload_success = $file->move(public_path('attachment'), $name);
                //Storage::disk( 'attachment')->put($name, file_get_contents( $file->getRealPath()));
                \App\Attachment::create(["name" => $name, "file" => url("attachment/$name"), "content_tiket_id" => $content->id]);
            }
        }
        return redirect()->back()
            ->with('success', 'tiket updated successfully');
    }
}
