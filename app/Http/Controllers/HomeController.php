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
        $this->train('perwalian', 'kapan krs kita');
        $this->train('perwalian', 'kapan terakhir krs dan perwalian');
        $this->train('siakad', 'kapan perwalian');
        $this->train('biaya', 'kapan terakhir pembayaran her registrasi');
        $this->train('siakad', 'kapan uts dimulai');
        $this->train('biaya', 'berapa biaya krs');
        $this->train('biaya', 'kapan mulai kuliah?');
        $this->train('perwalian', 'terkahir perwalian kapan');
        $this->train('perwalian', 'kapan perwalian krs');
        $this->train('komplain', 'kipas rusak kok tidak di perbaiki');
        $this->train('komplain', 'kipas tidak mau nyala ini rusak');
        $this->train('komplain', 'bayar mahal fasilitas kurang, rusak');
        $this->train('komplain', 'bisa memperbaiki komputer');
        $this->train('komplain', 'bisa memperbaiki lcd');
        $this->train('komplain', 'komputernya tidak bisa hidup');
        $this->train('komplain', 'kapan dibenerin kipasnya');
        $this->train('komplain', 'kapan dibenerin komputernya');
        $result = $this->classify('apakah bapak sudah bisa memperbaiki komputer saya?');
        dd($result) ;
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
