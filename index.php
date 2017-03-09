<?php 
    $pageTitle = "Stat Tracker";
 
    include 'components/head.php';
    include 'mock-data.php';
?>
    <body class="no-top-pad">
<?php include 'components/old-browser-warn.php' ?>


    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
      <div class="container">
        <h1 class="text-center" style="border-bottom: #000 3pt solid">Stat Tracker 3000</h1>
        <h2 class="text-center"><?php echo $desk; ?> | <?php echo $branch; ?></h3>
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

<?php include 'components/footer.php' ?>
    </div> <!-- /container -->        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>

        <script src="js/vendor/bootstrap.min.js"></script>

        <script src="js/main.js"></script>
    </body>
</html>
