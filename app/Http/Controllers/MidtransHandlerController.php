<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Models\Booking;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransHandlerController extends Controller
{
    /**
     * Handle midtrans callback
     */
    public function callback(Request $request)
    {
        $validation = validator($request->all(), $this->validation());

        if ($validation->fails()) {
            return $this->response('error', $validation->errors(), 422);
        }

        // define data
        $data = $request->all();

        // Signature check
        if (!$this->signatureCheck($data)) {
            return $this->response('error', 'Signature key is invalid', 400);
        }

        // Get order
        $order = $this->getOrder($data['order_id']);

        // Order available check
        if (!$order) {
            return $this->response('error', 'Order not found', 404);
        }

        // Order status check
        if ($order->status == OrderStatusEnum::SUCCESS) {
            return $this->response('error', 'Order already success', 400);
        }

        // get status midtrans
        $status = $this->getStatusMidtrans($data);

        try {
            // add payment logs
            $this->addPaymentLogs($order, $data);

            // update order status
            $this->updateStatusOrder($order, $status);

            // action after success payment
            if ($status == OrderStatusEnum::SUCCESS) {                
                // add order items
                $this->addOrderToBooking($order);
            }

            return $this->response('success', 'Midtrans callback success', 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->response('error', $th->getMessage(), 500);
        }
    }

    /**
     * Validation rules
     */
    private function validation(): array
    {
        return [
            'order_id' => 'required',
            'status_code' => 'required',
            'transaction_status' => 'required',
            'payment_type' => 'required',
            'gross_amount' => 'required',
            'fraud_status' => 'required',
        ];
    }

    /**
     * Get single order
     * 
     * @param string $orderId
     */
    private function getOrder($orderId)
    {
        return Order::where('code', $orderId)->first();
    }

    /**
     * Check signature key
     */
    private function signatureCheck($data)
    {
        $serverKey = config('services.midtrans.server_key');
        $signature = $data['order_id'] . $data['status_code'] . $data['gross_amount'] . $serverKey;
        $mySignatureKey = hash('sha512', $signature);

        return $data['signature_key'] === $mySignatureKey;
    }

    /**
     * Get status midtrans
     */
    private function getStatusMidtrans($data)
    {
        $status = OrderStatusEnum::PENDING;
        $transactionStatus = $data['transaction_status'];
        $fraudStatus = $data['fraud_status'];

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $status = OrderStatusEnum::CHALLENGE;
            } else if ($fraudStatus == 'accept') {
                $status = OrderStatusEnum::SUCCESS;
            }
        } else if ($transactionStatus == 'settlement') {
            $status = OrderStatusEnum::SUCCESS;
        } else if (
            $transactionStatus == 'cancel' ||
            $transactionStatus == 'deny' ||
            $transactionStatus == 'expire'
        ) {
            $status = OrderStatusEnum::FAILED;
        } else if ($transactionStatus == 'pending') {
            $status = OrderStatusEnum::PENDING;
        }

        return $status;
    }

    /**
     * Add payment logs
     */
    private function addPaymentLogs($order, $data)
    {
        // save to payment log
        $order->paymentLogs()->create([
            'status' => $data['transaction_status'],
            'payment_type' => $data['payment_type'],
            'raw_response' => $data,
        ]);
    }

    /**
     * Update status order
     */
    private function updateStatusOrder($order, $status)
    {
        $order->status = $status;
        $order->settlement_at = $order->success_at ?? now();
        $order->save();
    }

    /**
     * Add order items
     */
    private function addOrderToBooking($order)
    {
        $items = $order->orderItems;

        foreach ($items as $item) {
            Booking::create([
                'user_id' => $order->user_id,
                'service_id' => $item->productable_id,
                'date' => $item->metadata->booking_date,
                'total_session' => $item->qty,
            ]);
        }
    }

    /**
     * Response JSON helper
     */
    private function response($title, $message, $status_code)
    {
        return response()->json([
            'status' => $title,
            'message' => $message
        ], $status_code);
    }
}
