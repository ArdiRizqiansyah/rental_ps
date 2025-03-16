@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        @if ($order->isSuccess)
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="fas fa-check-circle me-1"></i>
                                <div>
                                    <p class="mb-0">Pembayaran Berhasil</p>
                                </div>
                            </div>
                        @endif

                        @if ($payment->isBankTransfer || $payment->isEchannel)
                            @php
                                if ($payment->raw_response['payment_type'] == 'echannel') {
                                    $nama_bank = 'mandiri';
                                    $va_number = $payment->raw_response['bill_key'];
                                } else {
                                    $va = $payment->raw_response['va_numbers'][0];
                                    $nama_bank = $va['bank'];
                                    $va_number = $va['va_number'];
                                }
                            @endphp

                            <div class="row align-items-center">
                                <div class="col-9">
                                    <p class="text-muted mb-1">Nama Bank</p>
                                    <p class="text-base font-semibold text-uppercase">{{ $nama_bank }}</p>
                                </div>
                                <div class="col-3 d-flex">
                                    <img src="{{ asset('assets/img/payment/' . $nama_bank . '.svg') }}"
                                        class="img-fluid ms-auto" alt="">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Nomor Virtual Account</p>
                                    <p class="text-base font-semibold">{{ $va_number }}</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <p class="text-danger mb-0" style="display: none;">Text Copied!</p>
                                    <button class="btn btn-link text-primary p-0"
                                        onclick="copyToClipboard('{{ $va_number }}', this)">Salin</button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Total Bayar</p>
                                    <p class="text-base font-semibold">{{ rupiahFormat($order->total) }}</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <p class="text-danger mb-0" style="display: none;">Text Copied!</p>
                                    <button class="btn btn-link text-primary p-0"
                                        onclick="copyToClipboard('{{ $order->total }}', this)">Salin</button>
                                </div>
                            </div>
                        @endif

                        @if (in_array($payment->payment_type, ['gopay', 'qris', 'shopeepay']))
                            @php
                                $payment_name = $payment->payment_type;
                                $indexDeeplink = $payment_name == 'shopeepay' ? 0 : 3;
                            @endphp

                            <div class="row justify-content-center g-4">
                                <div class="col-9">
                                    <p class="text-muted fw-medium mb-1">Nama E-Wallet</p>
                                    <p class="fs-6 fw-semibold text-uppercase">{{ $payment_name }}</p>
                                </div>
                                <div class="col-3 align-self-center">
                                    <img src="{{ asset('img/logo/payment/' . $payment_name . '.svg') }}"
                                        class="img-fluid float-end" alt="">
                                </div>
                                @if (in_array($payment_name, ['gopay', 'qris']))
                                    <div class="col-12 col-md-5 col-lg-4">
                                        <img src="{{ $payment->raw_response['actions'][0]['url'] }}" class="img-fluid">
                                    </div>
                                @endif

                                @if (in_array($payment_name, ['gopay', 'shopeepay']))
                                    <div class="col-12 mb-3">
                                        <div class="text-center">
                                            @if ($payment_name == 'gopay')
                                                <p class="small text-muted fw-medium mb-2">Klik tombol
                                                    dibawah ini untuk membuka aplikasi Gojek</p>
                                                <a href="{{ $payment->raw_response['actions'][1]['url'] }}"
                                                    class="btn btn-primary btn-sm">Bayar dengan Gopay</a>
                                            @else
                                                <p class="small text-muted fw-medium mb-2">Klik tombol
                                                    dibawah ini untuk membuka aplikasi Shopee</p>
                                                <a href="{{ $payment->raw_response['actions'][0]['url'] }}"
                                                    class="btn btn-primary btn-sm">Bayar dengan ShopeePay</a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="d-grid">
                            <a href="{{ route('user.payment.index', $order->code) }}" class="btn btn-primary">
                                Cek Status Pembayaran
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('after-scripts')
        <script>
            function copyToClipboard(text, button) {
                navigator.clipboard.writeText(text).then(function() {
                    const alert = button.previousElementSibling;
                    alert.style.display = 'block';
                    setTimeout(() => {
                        alert.style.display = 'none';
                    }, 2000);
                }).catch(function(err) {
                    console.error('Failed to copy text: ', err);
                });
            }
        </script>
    @endpush
@endsection
