<?php

namespace App\Repositories\Commons;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

class PaymentScheduleRepository
{
    /**
     * Get payment schedule by client account id
     * @param Request $data
     * @request client_account_id string
     * @request payment_schedule_id int
     * @request per_page int
     * @request current_page int
     * @return Array
     */
    public function index(Request $data)
    {
        try {
            $per_page = $data->per_page ?? 50;
            $current_page = $data->current_page ?? 1;
            $statement = "call get_payment_schedule_by_client(?,?,?,?)";
            $parameters = [
                $data['client_account_id'],
                $data['payment_schedule_id'] ?? null,
                $per_page,
                $current_page
            ];
            return DB::select($statement, $parameters);
        } catch (QueryException $e) {
            Log::error('Error in PaymentScheduleRepository::index: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate payment schedule for new or existing clients
     * @param Request $data
     * @request client_account_id string
     * @request program_id int
     * @request mode int
     * @return Array
     */
    public function store(Request $data)
    {
        try {
            $statement = "call generate_payment_schedule_for_new_or_existing_clients(?,?,?)";
            $parameters = [
                $data['client_account_id'],
                $data['program_id'],
                $data['mode']
            ];
            return DB::select($statement, $parameters);
        } catch (QueryException $e) {
            Log::error('Error in PaymentScheduleRepository::Store: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get payment schedule by client account id
     * @param string $clientAccountId
     * @return Array
     */
    public function getByClientAccountId(string $clientAccountId)
    {
        try {
            return DB::table('payment_schedule')
                ->where('client_account_id', $clientAccountId)
                ->where('is_active', 1)
                ->get();
        } catch (QueryException $e) {
            Log::error('Error in PaymentScheduleRepository::getPaymentSchedule: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get tracking by client account id
     * @param string $clientAccountId
     * @return Array
     */
    public function getTracking(string $clientAccountId) 
    {
        try {
            $statement = "call get_tracking_client_payment_schedule(?)";
            $parameters = [
                $clientAccountId
            ];
            return DB::select($statement, $parameters);
        } catch (QueryException $e) {
            Log::error('Error in PaymentScheduleRepository::getTracking: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get total amount fee by client account id
     * @param string $clientAccountId
     * @return Array
     */
    public function getTotalAmountFee(string $clientAccountId){
        try {
            $statement = "call get_total_fee_by_client_account(?)";
            $parameters = [
                $clientAccountId
            ];
            return DB::select($statement, $parameters);
        } catch (QueryException $e) {
            Log::error('Error in PaymentScheduleRepository::getTotalAmountFee: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get last payment date by client account id
     * @param string $clientAccountId
     * @return Array
     */
    public function getLastPaymentDate(string $clientAccountId){
        try {
            return DB::table('payment_schedule as ps')
            ->select('ps.client_account_id', 'ps.is_active', 'psd.due_date', 'psd.monthly_payment', 'psd.status', DB::raw('ABS(COALESCE(psd.pending_amount,0)) as pending_amount'))
            ->join('payment_schedule_detail as psd', function ($join) {
                $join->on('psd.payment_schedule_id', '=', 'ps.id')
                    ->whereNull('psd.transaction_id');
            })
            ->where('ps.client_account_id', $clientAccountId)
            ->where('ps.is_active', 1)
            ->orderBy('psd.due_date', 'asc')
            ->limit(1)
            ->get();
        } catch (QueryException $e) {
            Log::error('Error in PaymentScheduleRepository::getLasPaymentDate: ' . $e->getMessage());
            throw $e;
        }
    }
}
