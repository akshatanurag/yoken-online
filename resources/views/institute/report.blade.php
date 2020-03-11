@extends('institute.layout')
@section('main-content')
    <script src="/js/Chart.min.js"></script>
    <div class="container">
    @foreach($errors->all() as $error)
        {{$error}}
    @endforeach
        <div class="row">
            <form id="report-form" method="get" action="/institute/report">
                <div class="input-field col m6 s12">
                    <select required="required" name="course">
                        <option disabled <?php if(empty($course_details)) echo 'selected' ; ?>>--Choose Course--</option>
                    @foreach($courses as $course)
                        <option <?php if(!empty($course_details) && ($course_details->id == $course->id)) echo 'selected ' ; ?>value="{{$course->id}}">{{$course->name}}</option>
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
            <a href="/institute/report/download/all" target="_blank" class="btn waves-effect">Download Enrollments List (All Courses, All Time)</a>
        </div>
        @if(!empty($course_details))
        <div class="row center-align">
            <a href="/institute/report/download?course=<?php echo $_GET[ 'course' ]; ?>&period=<?php echo $_GET[ 'period' ]; ?>" target="_blank" class="btn waves-effect">Download Enrollments List</a>
        </div>
        <div class="row">
            <div class="col m12 center-align">
                <h4>Report For Course "{{$course_details->name}}"</h4>
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
                                label: 'Offline Sales (In Rupees)',
                                data: [<?php echo implode(', ', $enrollments_general [0]); ?>],
                                backgroundColor: 'rgba(<?php echo mt_rand(50,200); ?>,<?php echo mt_rand(50,200); ?>,<?php echo mt_rand(50,200); ?>,1)',
                                borderWidth: 1
                            },{
                                label: 'Online Sales (In Rupees)',
                                data: [<?php echo implode(', ', $enrollments_general [1]); ?>],
                                backgroundColor: 'rgba(<?php echo mt_rand(50,200); ?>,<?php echo mt_rand(50,200); ?>,<?php echo mt_rand(50,200); ?>,1)',
                                borderWidth: 1
                            }]
                        }
                    });
                </script>
            </div>
            <hr />
            <div class="col m12 center-align">
                <strong>Sales Pie Between Batches</strong><br /><br />
                <canvas id="myPie-common" width="400" height="200"></canvas><br /><br />
                <script type="text/javascript">
                    var myChart = new Chart(document.querySelector("#myPie-common"), {
                        type: 'doughnut',
                        data: {
                            labels: [<?php echo $pie_labels ; ?>],
                            datasets: [{
                                label: 'Sales (In Rupees)',
                                data: [<?php echo implode(', ', $enrollments_batch_general); ?>],
                                backgroundColor: [
                                    @foreach($enrollments_batch as $batch)
                                        'rgba(<?php echo mt_rand(50,200); ?>,<?php echo mt_rand(50,200); ?>,<?php echo mt_rand(50,200); ?>,1)',
                                    @endforeach
                                ],
                                borderWidth: 1
                            }]
                        }
                    });
                </script>
            </div>
            @foreach($batches as $batch)
                @if(!empty($enrollments_batch[$batch->id]))
                    <hr />
                    <div class="col m12 center-align">
                        <strong>Batch "{{$batch->commence_date}}"</strong><br /><br />
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
                            @if(empty($enrollments_users_list[$course->id]) === false)
                                @foreach($enrollments_users_list[$course->id] as $enrollment)
                                <tr>
                                    <td>{{$enrollment->created_at}}</td>
                                    <td>{{$enrollment->user->name}}</td>
                                    <td>{{$enrollment->user->email}}</td>
                                    <td>{{$enrollment->user->phone}}</td>
                                    @if($enrollment->base_fees - ($enrollment->base_fees * ($enrollment->base_discount / 100)) - $enrollment->yoken_rebate - $enrollment->institute_rebate <= 0)
                                        <td>Free</td>
                                        <td>---</td>
                                    @elseif($enrollment->type == 1 && !is_null($enrollment->payment))
                                        <td>Online Payment</td>
                                        <td>
                                            {{$enrollment->payment->payment_status}}
                                            @if(strtolower($enrollment->payment->payment_status) == "pending")
                                                <br /><a href="<?php echo $enrollment->payment->payment_details["longurl"]; ?>">Payment Link</a>
                                            @endif
                                        </td>
                                    @elseif($enrollment->type == 1)
                                        <td>Online Payment (Errored)</td>
                                        <td>---</td>
                                    @elseif($enrollment->type == 0)
                                        <td>Offline Payment</td>
                                        <td>---</td>
                                    @else
                                        <td>Unknown</td>
                                        <td>---</td>
                                    @endif
                                </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table><br /><br />
                        <canvas id="myChart-{{$batch->id}}" width="400" height="200"></canvas><br /><br />
                    </div>
                    <script type="text/javascript">
                        var myChart = new Chart(document.querySelector("#myChart-{{$batch->id}}"), {
                            type: 'line',
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
                                    <?php $color = '"rgba('.mt_rand(50,200).','.mt_rand(50,200).','.mt_rand(50,200).',1)"' ; ?>
                                    borderColor: <?php echo $color; ?>,
                                    backgroundColor: <?php echo $color; ?>,
                                    fill: false,
                                    label: 'Offline Sales (In Rupees)',
                                    data: [<?php echo implode(', ', $enrollments_batch[$batch->id][0]); ?>],
                                },{
                                    <?php $color = '"rgba('.mt_rand(50,200).','.mt_rand(50,200).','.mt_rand(50,200).',1)"' ; ?>
                                    borderColor: <?php echo $color; ?>,
                                    backgroundColor: <?php echo $color; ?>,
                                    fill: false,
                                    label: 'Online Sales (In Rupees)',
                                    data: [<?php echo implode(', ', $enrollments_batch[$batch->id][1]); ?>],
                                }]
                            },
                        });
                    </script>
                @endif
            @endforeach
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
