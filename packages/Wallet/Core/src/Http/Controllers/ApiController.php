<?php

namespace Wallet\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\MwaloniWallet;
use App\Jobs\ProcessPayment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Wallet\Core\Models\Service;
use Wallet\Core\Models\Transaction;
use Wallet\Core\Services\PaymentService;

class ApiController extends Controller
{
    use MwaloniWallet;

    public function __construct(protected PaymentService $paymentService)
    {
        $this->middleware('auth:api');
    }

    /* -----------------------------------------------------------------
     | BALANCE
     |-----------------------------------------------------------------*/

    public function fetch_balance(Request $request)
    {
        $service = $this->resolveService($request->post('service_id'));

        if (! $service) {
            return $this->error('Service not found');
        }

        $account = $service->account;

        $utilityBalance = $account->utility_balance
            - ($account->revenue + $account->withheld_amount);

        return $this->success([
            'balance' => $utilityBalance,
            'balanceBreakdown' => [
                'utilityBalance' => $utilityBalance,
                'workingBalance' => $account->working_balance,
            ],
        ]);
    }

    /* -----------------------------------------------------------------
     | TRANSACTION STATUS
     |-----------------------------------------------------------------*/

    public function get_transaction_status(Request $request)
    {
        $orderNumber = $request->post('orderNumber');

        $transaction = Transaction::with(['payload', 'status', 'service.account'])
            ->where(
                fn($q) =>
                $q->where('order_number', $orderNumber)
                    ->orWhere('reference', $orderNumber)
            )
            ->first();

        if (! $transaction || ! $this->ownsTransaction($transaction)) {
            return $this->error('Transaction not found');
        }

        $message = null;

        if ($transaction->payload?->raw_callback) {
            try {
                $message = json_decode($transaction->payload->raw_callback, true);
            } catch (\Throwable $e) {
                Log::warning('Callback decode failed', [
                    'transaction_id' => $transaction->id,
                ]);
            }
        }

        return $this->success([
            'message' => $message,
            'orderStatus' => $transaction->status->name,
        ]);
    }

    /* -----------------------------------------------------------------
     | SEND MONEY
     |-----------------------------------------------------------------*/

    public function send_money(Request $request)
    {
        try {
            $service = $this->resolveService($request->post('service_id'));

            if (! $service) {
                return $this->error('Service not found');
            }

            $transaction = $this->paymentService->sendMoney($request, $service);

            ProcessPayment::dispatch($transaction->id, $transaction->paymentChannel->slug)->onQueue('process-payments');

            return $this->success(['message' => 'Cashout was successful.', 'reference' => $transaction->identifier]);
        } catch (\DomainException $e) {
            return $this->error($e->getMessage());
        } catch (\Throwable $e) {
            Log::error($e);

            return $this->error(
                'Cashout failed. Please try again.'
            );
        }
    }

    /* -----------------------------------------------------------------
     | CONTACT LOOKUP
     |-----------------------------------------------------------------*/

    public function contact_lookup(Request $request)
    {
        $contact = substr($request->post('contact'), -9);

        $transaction = Transaction::where('account_number', 'like', "%{$contact}")
            ->whereNotNull('account_name')
            ->latest()
            ->first();

        if (! $transaction) {
            return $this->error('Contact not found', ['name' => '']);
        }

        return $this->success([
            'name' => $transaction->account_name,
        ]);
    }

    /* -----------------------------------------------------------------
     | SEND SMS
     |-----------------------------------------------------------------*/

    public function api_sendSMS(Request $request)
    {
        $this->sendSMS(
            $request->post('phoneNumber'),
            $request->post('message')
        );

        return $this->success();
    }

    /* -----------------------------------------------------------------
     | HELPERS
     |-----------------------------------------------------------------*/

    protected function resolveService(string $serviceId): ?Service
    {
        return $this->paymentService
            ->resolveService($serviceId, auth()->user());
    }

    protected function ownsTransaction(Transaction $transaction): bool
    {
        return $transaction->service
            ->account
            ->managed_by === auth()->id();
    }

    protected function success(array $data = [])
    {
        return response()->json(
            array_merge(['status' => '00'], $data),
            Response::HTTP_OK
        );
    }

    protected function error(string $message, array $extra = [])
    {
        return response()->json(
            array_merge([
                'status' => '01',
                'message' => $message,
            ], $extra),
            Response::HTTP_OK
        );
    }
}
