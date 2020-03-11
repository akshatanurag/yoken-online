@extends('user.layout')

@section('main-content')
    <div class="container" id="year-wise-sales">
        <div class="row">
            <h5>Enrolled Courses</h5>
        </div>
        <div class="row">
            <div class="col m12">
                <table>
                    <thead>
                        <th>Date</th>
                        <th>Course Name</th>
                        <th>Institute Name</th>
                        <th>Batch Commence Date</th>
                        <th>Payment Type</th>
                        <th>Payment Status</th>
                    </thead>
                    <tbody>
                        @foreach($enrollments as $enrollment)
                        <tr>
                            <td>{{$enrollment->created_at}}</td>
                            <td>{{$enrollment->batch->course->name}}</td>
                            <td>{{$enrollment->batch->course->institute->name}}</td>
                            <td>{{$enrollment->batch->commence_date}}</td>
                        @if($enrollment->base_fees - ($enrollment->base_fees * ($enrollment->base_discount / 100)) - $enrollment->yoken_rebate - $enrollment->institute_rebate <= 0)
                            <td>Free</td>
                            <td>---</td>
                        @elseif($enrollment->type == 1 && !is_null($enrollment->payment))
                            <td>Online Payment</td>
                            <td>
                                {{$enrollment->payment->payment_status}}
                                @if(strtolower($enrollment->payment->payment_status) == "pending")
                                    <br /><a href="<?php echo $enrollment->payment->payment_details["longurl"]; ?>">Click Here To Pay</a>
                                @endif
                            </td>
                        @elseif($enrollment->type == 1)
                            <td>Online Payment (Errored)</td>
                            <td>---</td>
                        @elseif($enrollment->type == 0)
                            <td>Offline Payment</td>
                            <td>---</td>
                        @elseif($enrollment->type == 0)
                            <td>Unknown</td>
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
