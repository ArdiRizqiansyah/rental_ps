<?php
namespace App\Services;

use App\Helpers\GeneratingHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Midtrans
{
    protected $serverKey; // midtrans server key
    protected $url; // midtrans url
    protected $authorization;

    /**
     * Instantiate a new service instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->serverKey = base64_encode(config('services.midtrans.server_key') . ':'); // encode to base 64
        $this->url = config('services.midtrans.url') . '/charge';
        $this->authorization = [
            'Authorization' => 'Basic ' . $this->serverKey,
        ];
    }

    /**
     * Fungsi untuk request ke midtrans core api
     */
    public function request($request, $order)
    {
        $data = [
            'order' => $order,
            'request' => $request,
        ];

        // check payment type
        if (in_array($request->payment_type, ['bni', 'bri'])) {
            $response = Http::acceptJson()->withHeaders($this->authorization)->post($this->url, $this->body($data, 'bank_transfer'));
        } elseif ($request->payment_type == 'mandiri') {
            $response = Http::acceptJson()->withHeaders($this->authorization)->post($this->url, $this->body($data, 'echannel'));
        } elseif ($request->payment_type == 'gopay') {
            $response = Http::acceptJson()->withHeaders($this->authorization)->post($this->url, $this->body($data, 'gopay'));
        } elseif ($request->payment_type == 'shopeepay') {
            $response = Http::acceptJson()->withHeaders($this->authorization)->post($this->url, $this->body($data, 'shopeepay'));
        } elseif ($request->payment_type == 'qris') {
            $response = Http::acceptJson()->withHeaders($this->authorization)->post($this->url, $this->body($data, 'qris'));
        } else {
            // update code order
            $this->updateCodeOrder($order);

            return [
                'status_code' => 404,
            ];
        }

        // return if ok
        if ($response->ok()) {
            // create payment log
            if (in_array($response->json()['status_code'], [200, 201])) {
                $order->paymentLogs()->create([
                    'status' => $response->json()['transaction_status'],
                    'payment_type' => $response->json()['payment_type'],
                    'raw_response' => $response->json(),
                ]);
            }

            return [
                'status' => $response->json()['status_code'],
                'message' => $response->body(),
                'order' => $order,
            ];
        } else {
            // delete order
            $this->updateCodeOrder($order);

            return [
                'status_code' => $response->status(),
                'message' => $response->body(),
            ];
        }
    }

    /**
     * Set request body
     */
    private function body($data, $payment_type)
    {
        $order = $data['order'];
        $request = $data['request'];

        $callback_url = route('user.payment.index', $order->code);

        $attr = [
            'payment_type' => $payment_type,
            'transaction_details' => [
                'order_id' => $order->code,
                'gross_amount' => $order->total,
            ],
        ];

        // if bank transfer (va) [BCA, BNI, BRI]
        if (in_array($payment_type, ['bank_transfer'])) {
            $attr['bank_transfer'] = [
                'bank' => $request->payment_type,
            ];
        }

        // if mandiri echannel
        if ($request->payment_type == 'mandiri') {
            $attr['echannel'] = [
                'bill_info1' => 'Rental PS',
                'bill_info2' => $order->code,
            ];
        }

        // E-Money
        if (in_array($payment_type, ['gopay', 'qris', 'shopeepay'])) {
            $attr[$payment_type] = [
                'enable_callback' => true,
                'callback_url' => $callback_url,
            ];
        }

        // set item details
        $attr['item_details'] = [];
        foreach ($data['order']->orderItems as $item) {
            array_push($attr['item_details'], [
                'id' => $item->productable_id,
                'name' => $item->metadata->product_name,
                'price' => $item->price,
                'quantity' => $item->qty,
            ]);

            if ($item->addon_price) {
                array_push($attr['item_details'], [
                    'id' => $item->productable_id,
                    'name' => 'Addon '.$item->metadata->product_name,
                    'price' => $item->addon_price,
                    'quantity' => 1,
                ]);
            }
        }

        // set customer details
        $attr['customer_details'] = [
            'first_name' => str()->substr($order->user_metadata->user_name, 0, 50),
            'email' => $order->user_metadata->user_email,
        ];

        return $attr;
    }

    /**
     * Delete Order
     */
    private function updateCodeOrder($order)
    {
        // delete order
        if ($order) {
            $order->update([
                'code' => GeneratingHelper::setOrderCode(),
            ]);
        }
    }
}
