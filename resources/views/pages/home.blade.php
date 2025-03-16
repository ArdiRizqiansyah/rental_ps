@extends('layouts.app')

@section('content')
    @include('includes.vendor.fulltime-calender', [
        'events' => $events,
    ])

    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card bg-white">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">
                            Kalender Booking
                        </h5>
                        <a href="{{ route('user.booking.create') }}" class="btn btn-primary">
                            <i class="far fa-calendar-plus me-1"></i>
                            Pesan Rental
                        </a>
                    </div>

                    <div class="card-body">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
