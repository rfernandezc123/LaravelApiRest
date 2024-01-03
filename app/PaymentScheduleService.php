<?php

namespace App\Services\Commons;

use App\Repositories\Commons\PaymentScheduleRepository;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class PaymentScheduleService
{
    protected $paymentScheduleRepository;

    public function __construct(PaymentScheduleRepository $paymentScheduleRepository)
    {
        $this->paymentScheduleRepository = $paymentScheduleRepository;
    }
    /**
     * Get payment schedule by client account id
     * @param Request $data
     * @request client_account_id string
     * @request payment_schedule_id int
     * @request per_page int
     * @request current_page int
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function index(Request $data)
    {
        try {
            $paymentSchedule = $this->paymentScheduleRepository->index($data);
            if (count($paymentSchedule)) {
                $totalCount = $paymentSchedule[0]->cont;
            } else {
                $totalCount = count($paymentSchedule);
            }

            foreach ($paymentSchedule as $value) {
                $remaining_amount = 0;

                if ($value->payment_details) {

                    $value->payment_details = json_decode($value->payment_details);

                    // validate for payments from charges
                    foreach ($value->payment_details as $key => $detail) {
                        if ($detail->is_from_charge || $detail->is_from_charge == 1) {
                            $remaining_amount += $detail->charge_amount;
                        }
                    };

                    if ($value->monthly_payment > $remaining_amount && $remaining_amount > 0) {
                        $new_item = [
                            "void" => null,
                            "method" => $value->payment_details[0]->method,
                            "refund" => null,
                            "status" => $value->payment_details[0]->status,
                            "due_date" => $value->due_date,
                            "merchant" => null,
                            "is_advance" => 0,
                            "amount_paid" => null,
                            "charge_back" => null,
                            "charge_name" => null,
                            "client_name" => "",
                            "charge_amount" => null,
                            "is_from_charge" => 0,
                            "pending_amount" => $value->monthly_payment - $remaining_amount,
                            "status_other_p" => null,
                            "transaction_id" => $value->payment_details[0]->transaction_id,
                            "settlement_date" => $value->payment_details[0]->settlement_date,
                            "type_transaction" => null,
                            "overpayment_Amount" => null,
                            "status_transaction" => null,
                            "type_transaction_id" => null,
                            "status_transaction_id" => null,
                            "remaining_amount" => true
                        ];
                        array_unshift($value->payment_details, $new_item);
                    }
                }
            }
            $per_page = $data->per_page ?? 50;
            $current_page = $data->current_page ?? 1;
            $results = collect($paymentSchedule);
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator($results, $totalCount, $per_page, $current_page);

            return $paginator;
        } catch (\Exception $e) {
            Log::error('Error in PaymentScheduleService::index: ' . $e->getMessage());
            throw $e;
        }
    }
}
