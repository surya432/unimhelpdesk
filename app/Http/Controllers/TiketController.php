<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Storage;
use GuzzleHttp\Client as client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response as guzzleResponse;

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

    use HelperController;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //dd( Auth::user()->getRoleNames());
        if ($request->user()->hasRole('User')) {
            $data = \App\Tiket::where('tikets.user_id', $request->user()->id)
                ->join('users', 'tikets.user_id', '=', 'users.id')
                ->join('content_tikets', 'content_tikets.id', '=', 'tikets.id')
                ->join('departements', 'departements.id', '=', 'tikets.departement_id')
                ->join('statuses', 'statuses.id', '=', 'tikets.status_id')
                ->join('services', 'services.id', '=', 'tikets.services_id')
                ->join('prioritas', 'prioritas.id', '=', 'tikets.prioritas_id')
                ->select('tikets.*', 'users.name as userName', 'prioritas.name as prioritasName', 'departements.name as departementName', 'statuses.name as statusName', 'services.name as servicesName')
                ->orderBy('tikets.updated_at', 'DESC')->get();
        } else if ($request->user()->hasRole("SuperAdmin")) {
            $data = \App\Tiket::join('users', 'tikets.user_id', '=', 'users.id')
                ->join('content_tikets', 'content_tikets.id', '=', 'tikets.id')
                ->join('departements', 'departements.id', '=', 'tikets.departement_id')
                ->join('statuses', 'statuses.id', '=', 'tikets.status_id')
                ->join('services', 'services.id', '=', 'tikets.services_id')
                ->join('prioritas', 'prioritas.id', '=', 'tikets.prioritas_id')
                ->select('tikets.*', 'users.name as userName', 'prioritas.name as prioritasName', 'departements.name as departementName', 'statuses.name as statusName', 'services.name as servicesName')
                ->orderBy('tikets.updated_at', 'DESC')->get();
        } else {
            $departementId = Role::where('name', $request->user()->getRoleNames())->get();
            $data = \App\Tiket::where('tikets.departement_id', $departementId['0']['id'])
                ->join('users', 'tikets.user_id', '=', 'users.id')
                ->join('content_tikets', 'content_tikets.id', '=', 'tikets.id')
                ->join('departements', 'departements.id', '=', 'tikets.departement_id')
                ->join('statuses', 'statuses.id', '=', 'tikets.status_id')
                ->join('services', 'services.id', '=', 'tikets.services_id')
                ->join('prioritas', 'prioritas.id', '=', 'tikets.prioritas_id')
                ->select('tikets.*', 'users.name as userName', 'prioritas.name as prioritasName', 'departements.name as departementName', 'statuses.name as statusName', 'services.name as servicesName')
                ->orderBy('tikets.updated_at', 'DESC')->get();
        }
        return view('admin.tiket.index', compact('data'));
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
        $departement = Role::whereNotIn('name', ['SuperAdmin', 'User'])->get();
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
        $content->repply = $request->input('repply');

        $content->save();
        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $name = md5(now()) . $file->getClientOriginalName();
                $upload_success = $file->move(public_path('attachment'), $name);
                try {
                    $mime = $file->getMimeType();
                } catch (\Exception $e) {
                    $mime = $file->getClientMimeType();
                }
                \App\Attachment::create(["name" => $name, "file" => "attachment/$name", "mime" =>  $mime, "content_tiket_id" => $content->id]);
            }
        }
        $this->reset();
        $data = \App\TrainingData::whereNotNull('hasilPrediksi')->get();
        //dd($data);
        foreach ($data as $b) {
            $this->train($b->hasilPrediksi, $b->words);
        }
        $result = "ok";
        $result = $this->classify($request->input('body'), $content->id);
        $data = \App\TrainingData::create(['words' => $result['words'], 'keysword' => $result['keysword'], 'tiket_id' => $result['tiket_id'], 'hasilPrediksi' => $result['hasilPrediksi']]);
        foreach ($result['dataHasil'] as $c) {
            \App\TrainingHasil::create(['keys' => $c['keys'], 'values' => $c['values'], 'training_data_id' => $data->id]);
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
        $contentTiket = \App\Content_tiket::where('tiket_id', $id)->orderBy('id', "DESC")->with('attachmentFile')->get();
        $status = \App\Status::all();
        $tiket = \App\Tiket::join('users', 'tikets.user_id', 'users.id')
            //->join('content_tikets', 'content_tikets.id', 'tikets.id')
            ->join('departements', 'departements.id', 'tikets.departement_id')
            ->join('statuses', 'statuses.id', 'tikets.status_id')
            ->join('prioritas', 'prioritas.id', 'tikets.prioritas_id')
            ->where('tikets.id', $id)
            ->with('RepplyTiket')
            ->select('tikets.*', 'users.name as userName', 'prioritas.name as prioritasName', 'departements.name as departementName', 'statuses.name as statusName')
            ->first();
        return view('admin.tiket.show', compact('tiket', 'contentTiket', 'status'));
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
        $tiket->status_id = $request->input('status_id');
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

    public function replyTiket(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
            'senders' => 'required',
            'repply' => 'required',
            'tiket_id' => 'required',
            'attachment' => 'max:5000',
        ]);

        $content = new \App\Content_tiket;
        $content->body = $request->input('body');
        $content->senders = $request->input('senders');
        $content->tiket_id = $request->input('tiket_id');
        $content->repply = $request->input('repply');
        $content->save();
        $this->push($request);
        \App\Tiket::find($request->input('tiket_id'))->touch();
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
    public function push(Request $request)
    {
        $tiketId = $request->input('tiket_id');
        $tiket = \App\Tiket::find($tiketId);
        $user = \App\User::find($tiket->user_id);
        $res = array();
        $res['DataFCM'] = $tiket->id;
        $name =  $request->input('senders');
        $res['repply'] = "$name Membalas Tiket";

        $message = array(
            // 'notification' => $request->notification,
            "data" => $res,
            'to' => $user['tokenfcm'],
            // 'android'=>[
            //     "priority"=>"high"
            // ]

        );
        try {
            $url = "https://fcm.googleapis.com/fcm/send";
            $headers = array(
                'Authorization: key=AIzaSyCW6NdQK_uJmUCzGal16lgkgrInC74pLD0',
                'Content-Type: application/json'
            );
            // Open connection
            $ch = curl_init();
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            // Disabling SSL Certificate support temporarly
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

            // Execute post
            $result = curl_exec($ch);
            if ($result === FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }

            // Close connection
            curl_close($ch);
        } catch (BadResponseException $ex) {
            $response = $ex->getResponse();
            return $response;
        }
    }
}
