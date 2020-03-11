@extends('institute.layout')

@section('main-content')
    <div class="container" id="year-wise-sales">
        <div class="row">
            <h5>Monthly sales</h5>
            <canvas id="myChart" width="400" height="200"></canvas><br /><br />
        </div>
        <hr />
        <div class="row">
            <strong>Sales Pie Between Batches</strong><br /><br />
            <canvas id="myPolar" width="400" height="200"></canvas><br /><br />
        </div>
    </div>
@endsection

@section('footer')
<script src="/js/Chart.min.js"></script>
<script>
    var myChart = new Chart(document.querySelector("#myChart"), {
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
                data: [<?php echo implode(', ', $enrollment [0]); ?>],
                backgroundColor: 'rgba(<?php echo mt_rand(50,200); ?>,<?php echo mt_rand(50,200); ?>,<?php echo mt_rand(50,200); ?>,1)',
                borderWidth: 1
            },
            {
                label: 'Online Sales (In Rupees)',
                data: [<?php echo implode(', ', $enrollment [1]); ?>],
                backgroundColor: 'rgba(<?php echo mt_rand(50,200); ?>,<?php echo mt_rand(50,200); ?>,<?php echo mt_rand(50,200); ?>,1)',
                borderWidth: 1
            }]
        }
    });
</script>
<script type="text/javascript">
    var myChart = new Chart(document.querySelector("#myPolar"), {
        type: 'polarArea',
        data: {
            labels: [<?php echo $pie_labels ; ?>],
            datasets: [{
                label: 'Sales (In Rupees)',
                data: [<?php echo implode(', ', $enrollment_courses); ?>],
                backgroundColor: [
                @foreach($courses as $courses)
                    'rgba(<?php echo mt_rand(50,200); ?>,<?php echo mt_rand(50,200); ?>,<?php echo mt_rand(50,200); ?>,1)',
                @endforeach
                ],
                borderWidth: 1
            }]
        }
    });
</script>
@endsection