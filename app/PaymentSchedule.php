<?php

namespace App\Http\Controllers\Api\Commons;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\Commons\PaymentScheduleService;
use App\Repositories\Commons\PaymentScheduleRepository;
use Illuminate\Http\JsonResponse;

class PaymentSchedule extends Controller
{
    protected $paymentScheduleService;
    protected $paymentScheduleRepository;

    public function __construct(
        PaymentScheduleService $paymentScheduleService,
        PaymentScheduleRepository $paymentScheduleRepository
    ) {
        $this->paymentScheduleService = $paymentScheduleService;
        $this->paymentScheduleRepository = $paymentScheduleRepository;
    }

    /**
     * Get payment schedule by client account id
     * @param Request $request
     * @request client_account_id string
     * @request payment_schedule_id int
     * @request per_page int
     * @request current_page int
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $paymentSchedule = $this->paymentScheduleService->index($request);
            return response()->json($paymentSchedule, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate payment schedule for new or existing clients
     * @param Request $request
     * @request client_account_id string
     * @request program_id int
     * @request mode int
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $hasPaymentSchedule = $this->paymentScheduleRepository->getByClientAccountId($request['client_account_id']);
            if (count($hasPaymentSchedule) > 0) {
                return response()->json(['message' => 'There is already a payment schedule for this client'], 200);
            }
            $this->paymentScheduleRepository->store($request);
            return response()->json(['message' => 'Payment schedule generated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get tracking by client account id
     * @param Request $request
     * @request client_account_id string
     * @return JsonResponse
     */
    public function getTracking(Request $request)
    {
        try {
            $tracking = $this->paymentScheduleRepository->getTracking($request['clientAccountId']);
            return response()->json($tracking, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get total amount fee by client account id
     * @param Request $request
     * @request client_account_id string
     * @return JsonResponse
     */
    public function getTotalAmountFee(Request $request)
    {
        try {
            $total = $this->paymentScheduleRepository->getTotalAmountFee($request['client_account_id']);
            return response()->json(["total" => $total], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get last payment date by client account id
     * @param Request $request
     * @request client_account_id string
     * @return JsonResponse
     */
    public function getLastPaymentDate(Request $request)
    {
        try {
            $lastPaymentDate = $this->paymentScheduleRepository->getLastPaymentDate($request['client_account_id']);
            return response()->json($lastPaymentDate, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
