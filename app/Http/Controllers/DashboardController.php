<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Member\Member;
use App\Models\Member\MemberRole;
use App\Models\Subscription\SubscriptionMonthMember;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMembers = Member::all()->count();
        $totalPatrimonialMembers = MemberRole::where('type', 'patrimonial')->get()->count();
        $totalRevenue = SubscriptionMonthMember::where('status', 'paga')->sum('value') + MemberRole::sum('patrimonial_value');

        $today = Carbon::today();
        $currentMonth = $today->month;
        $currentYear = $today->year;

        $totalMembersWithPendentPayment = SubscriptionMonthMember::where('status', 'pendente')
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->get()
            ->count();

        $monthlyRevenues = SubscriptionMonthMember::select(
            'month',
                DB::raw("
                    CASE month
                        WHEN 1 THEN 'Janeiro'
                        WHEN 2 THEN 'Fevereiro'
                        WHEN 3 THEN 'Março'
                        WHEN 4 THEN 'Abril'
                        WHEN 5 THEN 'Maio'
                        WHEN 6 THEN 'Junho'
                        WHEN 7 THEN 'Julho'
                        WHEN 8 THEN 'Agosto'
                        WHEN 9 THEN 'Setembro'
                        WHEN 10 THEN 'Outubro'
                        WHEN 11 THEN 'Novembro'
                        WHEN 12 THEN 'Dezembro'
                    END AS month_name
                "),
            DB::raw("SUM(CASE WHEN status = 'paga' THEN value ELSE 0 END) as total_paid"),
            DB::raw("SUM(CASE WHEN status = 'vencida' THEN 79.9 ELSE 0 END) as total_expired")
        )
        ->where('year', $currentYear)
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

        $months = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        ];

        $finalDataMonthlyRevenues = collect($months)->map(
            function ($name, $num) use ($monthlyRevenues) {
                $data = $monthlyRevenues->get($num);
                return [
                    'month' => $num,
                    'month_name' => $name,
                    'total_paid' => $data->total_paid ?? 0,
                    'total_expired' => $data->total_expired ?? 0,
                ];
            })->values();

        return Inertia::render('Dashboard', [
            'totalMembers' => $totalMembers,
            'totalPatrimonialMembers' => $totalPatrimonialMembers,
            'totalRevenue' => number_format($totalRevenue, 2, ',', '.'),
            'totalMembersWithPendentPayment' => $totalMembersWithPendentPayment,
            'monthlyRevenues' => $finalDataMonthlyRevenues
        ]);
    }
}
