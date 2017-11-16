<div class="col-md-12">
	<div id="graph" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
</div>


<?php 
    $reported = '';
	$month = date("m");
	$d = date("d");
	
	
    for($i = 1; $i <= 31; $i++){
        $d = $i;
        if($d < 10){
            $d = '0'. $d;
        }

        $req = $pdo->prepare('SELECT id FROM reported_list WHERE datum LIKE ?');
        $req->execute(["_____".$month."_" . $d . "%"]);
        ${'nb' . $i} = $req->rowCount();
        $reported = $reported .",". ${'nb' . $i};    
    }


    $banned = '';
    for($i = 1; $i <= 31; $i++){
        $d = $i;
        if($d < 10){
            $d = '0'. $d;
        }

        $req = $pdo->prepare('SELECT * FROM reported_list WHERE (datum LIKE ?) AND (vac = "true" OR ow = "true")');
        $req->execute(["_____".$month."_" . $d . "%"]);
        ${'nb_ban' . $i} = $req->rowCount();
        $banned = $banned . ',' . ${'nb_ban' . $i};  
           
    }
    $banned = "Array," . $banned;
    $banned = str_replace("Array,,","", $banned);
    $ban = "[" . $banned . "]";

    $reported = "Array,," . $reported;
    $reported = str_replace("Array,,,",'', $reported);
    $report = "[" . $reported . "]";
?>
<script>


Highcharts.chart('graph', {

    chart: {
        type: 'areaspline'
    },
    title: {
        text: 'Monthly statistics [Report]'
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
            text: 'Report number'
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
        name: 'Report',
        data: <?= $report ?>,
    }, {
        name: 'Bans',
        data: <?= $ban ?>,
    }]
});
</script>