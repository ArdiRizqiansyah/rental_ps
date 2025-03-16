<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
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
        $this->serverKey = base64_encode(config('services.midtrans.server_key') . ":"); // encode to base 64
        $this->url = config('services.midtrans.url');
        $this->authorization = [
            'Authorization' => 'Basic ' . $this->serverKey,
        ];
    }

    public function index($code)
    {
        $order = Order::with(['orderItems', 'paymentLogs'])->where('code', $code)->firstOrFail();

        // snap
        if ($order->snap_token) {
            return redirect($order->snap_url);
        }

        $data = [
            'order' => $order,
            'payment' => $order->paymentLogs()->first(),
        ];

        return view('pages.payment.index', $data);
    }
}
