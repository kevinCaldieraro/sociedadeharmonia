<?php

namespace App\Http\Controllers;

use App\Entities\Subscription\Subscription;
use App\Http\Requests\Subscription\SubscriptionRequest;
use App\Models\Subscription\SubscriptionMonthMember;
use App\Services\Subscription\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    private SubscriptionService $service;

    public function __construct(SubscriptionService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): Response
    {
        $months = array_map('intval', $request->input('months', [date('n')]));
        $status = $request->input('status', ['paga', 'pendente', 'vencida', 'isenta']);
        $year = $request->input('year', date('Y'));

        $subscriptions = SubscriptionMonthMember::with([
            'subscription:id,member_id,last_paid_at',
            'subscription.member:id,name,cpf'
        ])
        ->whereIn('month', $months)
        ->whereIn('status', $status)
        ->where('year', $year)
        ->get();

        return Inertia::render('Subscriptions', [
            'monthSubscriptions' => $subscriptions,
            'months' => $months,
            'status' => $status,
            'year' => $year,
        ]);
    }

    public function registerPayment(SubscriptionRequest $request): JsonResponse
    {
        $subscriptionMonthMember = $this->service->registerPayment($request->all());

        return response()->json([
            'subscriptionMonthMember' => $subscriptionMonthMember,
            'message' => 'Registro de pagamento efetuado com sucesso!'
        ]);
    }

    public function exemptMonth(Request $request): JsonResponse
    {
        $subscriptionMonth = $this->service->exemptMonth($request->id);

        return response()->json([
            'subscriptionMonthMember' => $subscriptionMonth,
            'message' => 'Mensalidade atualizada com sucesso.'
        ]);
    }
}
