@extends('admin.layout')
@section('main-content')
    <script src="/js/Chart.min.js"></script>
    <div class="container">
    @foreach($errors->all() as $error)
        {{$error}}
    @endforeach
        <div class="row">
            <form id="report-form" method="get" action="/admin/report-webinar">
                <div class="input-field col m6 s12">
                    <select required="required" name="webinar">
                        <option disabled <?php if(empty($webinar_details)) echo 'selected' ; ?>>--Choose Webinar--</option>
                    @foreach($webinars as $webinar)
                        <option <?php if(!empty($webinar_details) && ($webinar_details->id == $webinar->id)) echo 'selected ' ; ?>value="{{$webinar->id}}">{{$webinar->name}}</option>
                    @endforeach
                    </select>
                </div>
                <div class="input-field col m6 s12">
                    <select required="required" name="period">
                        <option disabled <?php if(empty($period)) echo 'selected' ; ?>>--Choose Time Period--</option>
                        <option <?php if(!empty($period) && ($period == 'hd')) echo 'selected ' ; ?>value="hd">Last Day (Per Hour)</option>
                        <option <?php if(!empty($period) && ($period == 'dw')) echo 'selected ' ; ?>value="dw">Last Week (Per Day)</option>
                        <option <?php if(!empty($period) && ($period == 'wm')) echo 'selected ' ; ?>value="wm">Last Month (Per Week)</option>
                        <option <?php if(!empty($period) && ($period == 'my')) echo 'selected ' ; ?>value="my">Last Year (Per Month)</option>
                        <option <?php if(!empty($period) && ($period == 'yy')) echo 'selected ' ; ?>value="yy">Last 10 Years (Per Year)</option>
                    </select>
                </div>
                <div class="row center-align">
                    <button class="btn waves-effect">Generate Report</button>
                </div>
            </form>
        </div>
        <div class="row center-align">
            <a href="/admin/report-webinar/download/all" target="_blank" class="btn waves-effect">Download Registrations List (All Webinars, All Time)</a>
        </div>
        @if(!empty($webinar_details))
        <div class="row center-align">
            <a href="/admin/report-webinar/download?webinar=<?php echo $_GET[ 'webinar' ]; ?>&period=<?php echo $_GET[ 'period' ]; ?>" target="_blank" class="btn waves-effect">Download Registrations List</a>
        </div>
        <div class="row">
            <div class="col m12 center-align">
                <h4>Report For Webinar "{{$webinar_details->name}}"</h4>
            </div>
            <div class="col m12 center-align">
                <strong>Overall Sales</strong><br /><br />
                <canvas id="myChart-common" width="400" height="200"></canvas><br /><br />
                <script type="text/javascript">
                    var myChart = new Chart(document.querySelector("#myChart-common"), {
                        type: 'bar',
                        options: {
                            scales: {
                                xAxes: [{
                                    stacked: true
                                }],
                                yAxes: [{
                                    stacked: true
                                }],
                            }
                        },
                        data: {
                            labels: [<?php echo $labels ; ?>],
                            datasets: [{
                                label: 'Sales (In Rupees)',
                                data: [<?php echo implode(', ', $registrations_general); ?>],
                                backgroundColor: 'rgba(<?php echo mt_rand(50,200); ?>,<?php echo mt_rand(50,200); ?>,<?php echo mt_rand(50,200); ?>,1)',
                                borderWidth: 1
                            }]
                        }
                    });
                </script>
            </div>
            <hr />
            @if(!empty($registrations_list))
            <div class="col m12 center-align">
                <table>
                    <thead>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Payment Type</th>
                        <th>Payment Status</th>
                    </thead>
                    <tbody>
                        @foreach($registrations_list as $registration)
                        <tr>
                            <td>{{$registration->created_at}}</td>
                            <td>{{$registration->user->name}}</td>
                            <td>{{$registration->user->email}}</td>
                            <td>{{$registration->user->phone}}</td>
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
            @endif
        </div>
        @endif
    </div>
@endsection
@section('footer')
    <script type="text/javascript">
        jQuery(document).ready(function() {
            Materialize.updateTextFields();
            $('select').material_select();
        });
    </script>
@endsection
