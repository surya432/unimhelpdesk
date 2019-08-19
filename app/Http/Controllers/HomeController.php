<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use auth;
use Carbon\Carbon;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    use HelperController;

    public function index()
    {
        // if(Auth::user()->hasRole('User')){
        //     Auth::logout();
        //     return redirect('/login');
        // }
        $this->reset();
        $data = \App\TrainingData::whereNotNull('hasilPrediksi')->get();
        //dd($data);
        foreach($data as $b){
          $this->train($b->hasilPrediksi,$b->words);
        }
        $result = "ok";
        $result = $this->classify('bagaimana cara perwalian untuk semester baru','2');
        $data = \App\TrainingData::create(['words' => $result['words'] , 'keysword' => $result['keysword'] , 'tiket_id' => $result['tiket_id'], 'hasilPrediksi' => $result['hasilPrediksi'] ]);
        foreach ($result['dataHasil'] as $c) {
            \App\TrainingHasil::create(['keys'=> $c['keys'], 'values' => $c['values'], 'training_data_id' => $data->id]);
        }
        $fromDate = Carbon::now()->startOfMonth();
        $tillDate = Carbon::now()->endOfMonth();

        $date = \Carbon\Carbon::today()->subDays(30);
        $userCount = \App\User::role('User')->count();
        $openCount = \App\Tiket::join('statuses', 'statuses.id', '=', 'tikets.status_id')
            ->where('statuses.name', 'Open')->count();
        $closedCount = \App\Tiket::join('statuses', 'statuses.id', '=', 'tikets.status_id')
            ->where('statuses.name', 'Closed')->count();
        $countTiketMasuk = \App\Tiket::whereBetween('created_at', [$fromDate, $tillDate])->count();
        return view('home', compact('userCount', 'closedCount', 'openCount', 'countTiketMasuk'));
    }
}
