<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class TiketController extends Controller
{
    //
    use HelperController;
    public function getMaster(Request $request)
    {
        $data = $request->input('TableName');
        switch ($data) {
            case "status":
                return response()->json(["status" => "success", 'data' => \App\Status::all()]);
                break;
            case "role":
                return response()->json(["status" => "success", 'data' => $request->user()->hasRole('User')]);
                break;
            case "departement":
                return response()->json(["status" => "success", 'data' => \App\Departement::all()]);
                break;
            case "prioritas":
                return response()->json(["status" => "success", 'data' => \App\Prioritas::all()]);
                break;
            case "artikel":
                return response()->json(["status" => "success", 'data' => \App\Artikel::orderBy('updated_at', 'Desc')->get()]);
                break;
            case "tiket":
                return response()->json(["status" => "success", 'data' => $this->getDataTiket($request)]);
                break;
            case "services":
                return response()->json(["status" => "success", 'data' => \App\Services::all()]);
                break;
            case "tiketContent":
                return response()->json(["status" => "success", 'data' => \App\Content_tiket::where('content_tikets.tiket_id', $request->input("tiketId"))->with('attachmentFile')->get()]);
                break;
            case "TokenFCM":
                
                return response()->json(["status" => "success", 'data' => $this->getDataTokenFCM($request)]);
                break;
            default:
                return response()->json(["status" => "failed", 'data' => "null"], 404);
        }
    }
    private function getDataTokenFCM(Request $request){
        $data = \App\User::find($request->input('id'));
        if($request->input('tokenFCM') != $data->tokenFCM){
            $data->tokenFCM = $request->input('tokenFCM');
            $data->save();
        }
        return $data;
    }
    public function closedTiket(Request $request)
    {
        if ($request->input('tiket_id')) {
            $tiket = \App\Tiket::find($request->input('tiket_id'));
            $tiket->status_id = $request->input('status_id');
            $tiket->save();
            return response()->json(["status" => "success"], 200);
        }
        return response()->json(["status" => "failed", 'msg' => "tiket_id"], 404);
    }
    private function getDataTiket($request)
    {
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
        return $data;
    }
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
        ]);
        $tiket = \App\Tiket::create($request->only('subject', 'user_id', 'prioritas_id', 'status_id', 'departement_id', 'services_id'));
        $content = new \App\Content_tiket;
        $content->body = $request->input('body');
        $content->senders = $request->input('senders');
        $content->tiket_id = $tiket->id;
        if ($request->user()->hasRole("User")) {
            $content->repply = "0";
        } else {
            $content->repply = "1";
        }
        $content->save();
        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $name = md5(now()) . $file->getClientOriginalName();
                $file->move(public_path('attachment'), $name);
                //$mime = $file->getClientMimeType();
                $mime = $file->getClientMimeType() ? $file->getClientMimeType() : "file";
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
        return response()->json(["status" => "success", 'msg' => "Created Sukses"], 200);
    }
    public function replyTiket(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
            'senders' => 'required',
            'repply' => 'required',
            'tiket_id' => 'required',
        ]);

        $content = new \App\Content_tiket;
        $content->body = $request->input('body');
        $content->senders = $request->input('senders');
        $content->tiket_id = $request->input('tiket_id');
        $content->repply = $request->input('repply');
        $content->save();

        \App\Tiket::find($request->input('tiket_id'))->touch();
        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $name = md5(now()) . $file->getClientOriginalName();
                $mime = $file->getClientMimeType() ? $file->getClientMimeType() : "file";
                try {
                    $mime = $file->getMimeType();
                } catch (\Exception $e) {
                    $mime = $file->getClientMimeType();
                }
                $upload_success = $file->move(public_path('attachment'), $name);
                \App\Attachment::create(["name" => $name, "file" => "attachment/$name", "mime" =>  $mime, "content_tiket_id" => $content->id]);
            }
        }
        return response()->json(["status" => "success", 'data' => \App\Content_tiket::where('content_tikets.tiket_id', $request->input("tiket_id"))->with('attachmentFile')->get()]);
    }
}
