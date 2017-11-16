<div class="col-md-12">
	<div id="graph_commend" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
</div>


<?php 
    $commended = '';
    for($i = 1; $i <= 31; $i++){
        $d = $i;
        if($d < 10){
            $d = '0'. $d;
        }

        $req = $pdo->prepare('SELECT id FROM commended_list WHERE datum LIKE ?');
        $req->execute(["________" . $d . "%"]);
        ${'nb' . $i} = $req->rowCount();
        $commended = $commended .",". ${'nb' . $i};    
    }


    
    $commended = "Array," . $commended;
    $commended = str_replace("Array,,","", $commended);
    $commend = "[" . $commended . "]";
?>
<script>


Highcharts.chart('graph_commend', {

    chart: {
        type: 'areaspline'
    },
    title: {
        text: 'Monthly statistics [Commend]'
    },
    legend: {
        layout: 'vertical',
        align: 'left',
        verticalAlign: 'top',
        x: 150,
        y: 100,
        floating: true,
        borderWidth: 1,
        backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
    },
    xAxis: {
        categories: [
            "1st",
            "2nd",
            "3rd",
            "4th",
            "5th",
            "6th",
            "7th",
            "8th",
            "9th",
            "10th",
            "11th",
            "12th",
            "13th",
            "14th",
            "15th",
            "16th",
            "17th",
            "18th",
            "19th",
            "20th",
            "21st",
            "22nd",
            "23rd",
            "24th",
            "25th",
            "26th",
            "27th",
            "28th",
            "29th",
            "30th",
            "31st",


        ],
        plotBands: [{ // visualize the weekend
            
            color: 'rgba(68, 170, 213, .2)'
        }]
    },
    yAxis: {
        title: {
            text: 'Commend number'
        }
    },
    tooltip: {
        shared: true,
        valueSuffix: ''
    },
    credits: {
        enabled: false
    },
    plotOptions: {
        areaspline: {
            fillOpacity: 0.5
        }
    },
    series: [{
        name: 'Commend',
        data: <?= $commend ?>,
    }]
});
</script>