<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class TiketController extends Controller
{
    //

    public function getMaster(Request $request)
    {
        $data = $request->input('TableName');
        switch ($data) {
            case "status":
                return response()->json(["status" => "success", 'data' => \App\Status::all()]);
                break;
            case "departement":
                return response()->json(["status" => "success", 'data' => Role:: whereNotIn("name",[ "SuperAdmin", "User"])->get()]);
                break;
            case "prioritas":
                return response()->json(["status" => "success", 'data' => \App\Prioritas::all()]);
                break;
            case "tiket":
                return response()->json(["status" => "success", 'data' => $this->getDataTiket($request), 'total' => \App\Tiket::where('user_id', $request->user()->id)->count()]);
                break;
            case "services":
                return response()->json(["status" => "success", 'data' => \App\Services::all()]);
                break;
            case "tiketContent":
                return response()->json(["status" => "success", 'data' => \App\Content_tiket::where('content_tikets.tiket_id',$request->input("tiketId"))->join('attachments', "content_tikets.id", "attachments.content_tiket_id")->get()]);
                break;
            default:
                return response()->json(["status" => "failed", 'data' => "null"], 404);
        }
    }
    private function getTiketBody($request){
        
    }
    private function getDataTiket($request)
    {
        if ($request->user()->hasRole('User')) {
            $data = \App\Tiket::join('users', 'tikets.user_id', 'users.id')
                ->join('content_tikets', 'content_tikets.id', 'tikets.id')
                ->join('departements', 'departements.id', 'tikets.departement_id')
                ->join('statuses', 'statuses.id', 'tikets.status_id')
                ->join('prioritas', 'prioritas.id', 'tikets.status_id')
                ->where('tikets.user_id', $request->user()->id)
                ->select('tikets.*', 'users.name as userName', 'prioritas.name as prioritasName', 'departements.name as departementName', 'statuses.name as statusName')
                ->orderBy('tikets.id', 'DESC')->get();

        } else if ($request->user()->hasRole("SuperAdmin")) {
            $data = \App\Tiket::join('users', 'tikets.user_id', 'users.id')
                ->join('content_tikets', 'content_tikets.id', 'tikets.id')
                ->join('departements', 'departements.id', 'tikets.departement_id')
                ->join('statuses', 'statuses.id', 'tikets.status_id')
                ->join('prioritas', 'prioritas.id', 'tikets.status_id')
                ->select('tikets.*', 'users.name as userName', 'prioritas.name as prioritasName', 'departements.name as departementName', 'statuses.name as statusName')
                ->orderBy('tikets.id', 'DESC')->get();
        } else {
            $departementId = Role::where('name', $request->user()->getRoleNames())->get();
            //dd($departementId['0']);
            $data = \App\Tiket::join('users', 'tikets.user_id', 'users.id')
                ->join('content_tikets', 'content_tikets.id', 'tikets.id')
                ->join('departements', 'departements.id', 'tikets.departement_id')
                ->join('statuses', 'statuses.id', 'tikets.status_id')
                ->join('content_tikets', 'content_tikets.tiket_id', 'tikets.id')
                ->join('prioritas', 'prioritas.id', 'tikets.status_id')
                ->select('tikets.*', 'users.name as userName', 'prioritas.name as prioritasName', 'departements.name as departementName', 'statuses.name as statusName')
                ->where('tikets.departement_id', $departementId['0']['id'])
                ->orderBy('tikets.id', 'DESC')->get();

        }
        // $bodyTiket = \App\Content_tiket::where('tiket_id', $data->id);
        // $data = array_marge($data, ['bodyTiket' => $bodyTiket]);  
            return $data;
        
    }
    public function create(Request $request){
        $request->validate($request, [
            'subject' => 'required',
            'body' => 'required',
            'prioritas_id' => 'required',
            'user_id' => 'required',
            'status_id' => 'required',
            'services_id' => 'required',
            'departement_id' => 'required',
            'senders' => 'required',
            'attachment' => 'max:100000',
        ]);
        $tiket = \App\Tiket::create($request->only('subject', 'user_id', 'prioritas_id', 'status_id', 'departement_id', 'services_id'));
        $content = new \App\Content_tiket;
        $content->body = $request->input('body');
        $content->senders = $request->input('senders');
        $content->tiket_id = $tiket->id;
        $content->save();
        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $name = md5(now()) . $file->getClientOriginalName();
                $upload_success = $file->move(public_path('attachment'), $name);
                //Storage::disk( 'attachment')->put($name, file_get_contents( $file->getRealPath()));
                \App\Attachment::create(["name" => $name, "file" => "attachment/$name", "content_tiket_id" => $content->id]);
            }
        }
        return response()->json(["status" => "success", 'msg' => "success"], 200);
    }
}
