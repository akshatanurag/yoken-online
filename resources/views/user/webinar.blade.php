@extends('user.layout')

@section('main-content')
    <div class="container" id="year-wise-sales">
        <div class="row">
            <h5>Registered Webinars</h5>
        </div>
        <div class="row">
            <div class="col m12">
                <table>
                    <thead>
                        <th>Date</th>
                        <th>Webinar Name</th>
                        <th>Webinar Start Time</th>
                        <th>Webinar End Time</th>
                        <th>Payment Type</th>
                        <th>Payment Status</th>
                    </thead>
                    <tbody>
                        @foreach($registrations as $registration)
                        <tr>
                            <td>{{$registration->created_at}}</td>
                            @if($registration->webinar)
                                <td>{{$registration->webinar->name}}</td>
                                <td>{{date("d/m/Y H:i", strtotime($registration->webinar->starts_at))}}</td>
                                <td>{{date("d/m/Y H:i", strtotime($registration->webinar->ends_at))}}</td>
                            @else
                                <td>Unknown/Deleted Webinar</td>
                                <td>Unknown Date</td>
                                <td>Unknown Date</td>
                            @endif
                            @if($registration->base_fees - ($registration->base_fees * ($registration->base_discount / 100)) <= 0)
                                <td>Free</td>
                                <td>---</td>
                            @elseif(!is_null($registration->payment))
                                <td>Online Payment</td>
                                <td>
                                    {{$registration->payment->payment_status}}
                                    @if(strtolower($registration->payment->payment_status) == "pending")
                                        <br /><a href="<?php echo $registration->payment->payment_details["longurl"]; ?>">Click Here To Pay</a>
                                    @endif
                                </td>
                            @else
                                <td>Online Payment (Errored)</td>
                                <td>---</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('footer')
@endsection
