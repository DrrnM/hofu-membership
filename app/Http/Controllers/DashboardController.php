<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function indexOwner()
    {
        $totalMember = Member::count();
        $totalPoin = Member::sum('poin');
        return view('Owner.Dashboard', compact('totalMember', 'totalPoin'));
    }

    public function indexKasir()
    {
        $totalMember = Member::count();
        $totalPoin = Member::sum('poin');
        return view('Kasir.Dashboard', compact('totalMember', 'totalPoin'));
    }
}
