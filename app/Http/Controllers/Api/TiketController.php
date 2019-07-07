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
                return response()->json(["status" => "success", 'data' => Role::all()]);
                break;
            case "prioritas":
                return response()->json(["status" => "success", 'data' => \App\Prioritas::all()]);
                break;
            case "tiket":
                return response()->json(["status" => "success", 'data' => $this->getDataTiket($request), 'total' => \App\Tiket::where('user_id', $request->user()->id)->count()]);
                break;
            case "tiketContent":
                return response()->json(["status" => "success", 'data' => \App\Content_tiket::all()]);
                break;
        }
    }
    private function getDataTiket($request)
    {
        if ($request->user()->hasRole('User')) {
            $data = \App\Tiket::join('users', 'tikets.user_id', 'users.id')
                ->join('content_tikets', 'content_tikets.id', 'tikets.id')
                ->join('departements', 'departements.id', 'tikets.departement_id')
                ->join('statuses', 'statuses.id', 'tikets.status_id')
                ->join( 'prioritas', 'prioritas.id', 'tikets.status_id')
                ->where('tikets.user_id', $request->user()->id)
                ->select( 'tikets.*', 'users.name as userName', 'prioritas.name as prioritasName', 'departements.name as departementName', 'statuses.name as statusName')
                ->orderBy( 'tikets.id', 'DESC')->get();
        } else if ($request->user()->hasRole("SuperAdmin")) {
            $data = \App\Tiket::join('users', 'tikets.user_id', 'users.id')
                ->join('content_tikets', 'content_tikets.id', 'tikets.id')
                ->join('departements', 'departements.id', 'tikets.departement_id')
                ->join('statuses', 'statuses.id', 'tikets.status_id')
                ->select('tikets.*', 'users.name as userName', 'prioritas.name as prioritasName', 'departements.name as departementName', 'statuses.name as statusName')
                ->orderBy( 'tikets.id', 'DESC')->get();
        } else {
            $departementId = Role::where('name', $request->user()->getRoleNames())->get();
            //dd($departementId['0']);
            $data = \App\Tiket::join('users', 'tikets.user_id', 'users.id')
                ->join('content_tikets', 'content_tikets.id', 'tikets.id')
                ->join('departements', 'departements.id', 'tikets.departement_id')
                ->join('statuses', 'statuses.id', 'tikets.status_id')
                ->join( 'content_tikets', 'content_tikets.tiket_id', 'tikets.id')
                ->select('tikets.*', 'users.name as userName', 'prioritas.name as prioritasName', 'departements.name as departementName', 'statuses.name as statusName')
                ->where('tikets.departement_id', $departementId['0']['id'])
                ->orderBy( 'tikets.id', 'DESC')->get();
        }
        //$data = array_merge($data, \App\Content_tiket::where('tiket_id', $data));

        return $data;
    }
}
