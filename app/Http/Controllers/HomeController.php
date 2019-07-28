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
    public function index()
    {
        // if(Auth::user()->hasRole('User')){
        //     Auth::logout();
        //     return redirect('/login');
        // }
        $fromDate = Carbon::now()->startOfMonth();
        $tillDate = Carbon::now()->endOfMonth();

        $date = \Carbon\Carbon::today()->subDays(30);
        $userCount = \App\User::role('User')->count();
        $openCount = \App\Tiket::join('statuses', 'statuses.id', '=', 'tikets.status_id')
            ->where('statuses.name', 'Open')->count();
        $closedCount = \App\Tiket::join('statuses', 'statuses.id', '=', 'tikets.status_id')
            ->where('statuses.name', 'Closed')->count(); 
        $countTiketMasuk = \App\Tiket::whereBetween('created_at', [$fromDate, $tillDate])->count();
        return view('home',compact('userCount', 'closedCount', 'openCount', 'countTiketMasuk'));
    }
}
