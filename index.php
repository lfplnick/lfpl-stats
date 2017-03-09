<?php 
    $pageTitle = "Stat Tracker";
 
    include 'components/head.php';
    include 'mock-data.php';
?>
      <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <body class="no-top-pad">
<?php include 'components/old-browser-warn.php' ?>


    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center" style="border-bottom: #000 3pt solid">Stat Tracker 3000</h1>
        <h2 class="text-center"><?php echo $desk; ?> | <?php echo $branch; ?> <span class="glyphicon glyphicon-cog" style="font-size: 0.7em; padding-left: 10px;" aria-hidden="true"></h3></span>
      </div>
    </div>

    <div class="container" style="padding-bottom: 10px;">
      <!-- Example row of columns -->
      <div class="row">
        <div class="col-md-3">
          <h1>Info</h1>
        </div>
        <div class="col-md-3 btn btn-default">
          <h3>Easy<br/>( &lt; 2 minutes )</h3>
       </div>
        <div class="col-md-3 btn btn-default">
          <h3>Medium<br/>( 2 - 10 minutes )</h3>
       </div>
        <div class="col-md-3 btn btn-default">
          <h3>Hard<br/>( &gt;10 minutes )</h3>
       </div>
      </div>
    </div>

    <div class="container">
      <div class="row">
        <div class="col-md-3">
          <h1>Directional</h1>
        </div>
        <div class="col-md-3 btn btn-default">
          <h3>Easy<br/>( &lt; 2 minutes )</h3>
       </div>
        <div class="col-md-3 btn btn-default">
          <h3>Medium<br/>( 2 - 10 minutes )</h3>
       </div>
        <div class="col-md-3 btn btn-default">
          <h3>Hard<br/>( &gt;10 minutes )</h3>
       </div>
      </div>


      <hr>

      <div id="chart_div"></div>
<?php include 'components/footer.php' ?>
    </div> <!-- /container -->        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>

        <script src="js/vendor/bootstrap.min.js"></script>

        <script src="js/main.js"></script>
        <script type="text/javascript">
          google.charts.load('current', {packages: ['corechart', 'bar']});
          google.charts.setOnLoadCallback(drawColColors);

          function drawColColors() {
            var data = new google.visualization.DataTable();
            data.addColumn('timeofday', 'Time of Day');
            data.addColumn('number', 'Directional');
            data.addColumn('number', 'Informational');

            data.addRows([
              [{v: [9, 0, 0], f: '9 am'}, 8, 3],
              [{v: [10, 0, 0], f: '10 am'}, 13, 7],
              [{v: [11, 0, 0], f: '11 am'}, 9, 15],
              [{v: [12, 0, 0], f: '12 pm'}, 11, 13],
              [{v: [13, 0, 0], f: '1 pm'}, 16, 18],
              [{v: [14, 0, 0], f: '2 pm'}, 24, 20],
              [{v: [15, 0, 0], f: '3 pm'}, 25, 21],
              [{v: [16, 0, 0], f: '4 pm'}, 22, 24],
              [{v: [17, 0, 0], f: '5 pm'}, 27, 16],
              [{v: [18, 0, 0], f: '6 pm'}, 21, 13],
              [{v: [19, 0, 0], f: '7 pm'}, 14, 16],
              [{v: [20, 0, 0], f: '8 pm'}, 8, 7],
            ]);

            var options = {
              title: 'Stats Today',
              colors: ['#A178E8', '#7DD3F2'],
              isStacked: true,
              height: 300,
              hAxis: {
                title: 'Time of Day',
                format: 'h:mm a',
                viewWindow: {
                  min: [8, 0, 0],
                  max: [21,0,0]
                }
              },
              vAxis: {
                title: 'Questions'
              }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
            chart.draw(data, options);
          }
        </script>
    </body>
</html>
