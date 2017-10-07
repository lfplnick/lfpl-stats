<?php 
    include 'includes/AutoLoader.php';

    $pageTitle = "Stat Tracker";
    include 'components/head.php';
?>
      <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    </head>
    <body class="no-top-pad" ng-app="stat3k">
<?php include 'components/old-browser-warn.php' ?>

    <div ng-controller="mainCtrl">
    <!-- Main jumbotron for a primary marketing message or call to action -->
      <div class="jumbotron">
        <div class="container">
          <h1 class="text-center" style="border-bottom: #000 3pt solid" ng-model="appName">{{appName}}</h1>
          <h2 class="text-center">


          <!-- Branch setting -->
          <span id="branch" ng-hide="editSettings">{{currentBranch.branches_name}}</span>
          <span id="branch-selector" ng-show="editSettings" class="form-inline form-group-lg">
            <select
              class="form-control"
              ng-model="selectBranchId"
              ng-change="changeBranch()"
              ng-options="branch.branches_id as branch.branches_name for branch in branches">
            </select>
          </span>
          
            <span class="hidden-xs"> | </span>

          <!-- Desk setting -->
          <span id="desk" ng-hide="editSettings">{{currentDesk.sp_name}}</span>
          <span id="desk-selector" ng-show="editSettings" class="form-inline form-group-lg">
            <select
              class="form-control"
              ng-init="selectDeskId = -1"
              ng-model="selectDeskId"
              ng-options="desk.sp_id as desk.sp_name for desk in desks">
            </select>
          </span>

          <!-- Settings button -->
            <span id="open-settings" ng-hide="editSettings" class="glyphicon glyphicon-cog btn" style="font-size: 0.7em; padding-left: 10px;" aria-hidden="true" ng-click="openSettings()"></span>
            <div id="settings-buttons" ng-show="editSettings" class="btn-group">
              <button type="button" id="save-settings" class="btn btn-primary btn-lg" ng-disabled="!newSettingsValid()" ng-click="saveSettings()">Save</button>
              <button type="button" id="cancel-settings" class="btn btn-default btn-lg" ng-disabled="!currentSettingsValid()" ng-click="cancelSettings()">Cancel</button>
            </div>
          </h2>
  <!-- Debugging screen sizes
          <h3 class="visible-xs">extra-small</h3>
          <h3 class="visible-sm">small</h3>
          <h3 class="visible-md">medium</h3>
          <h3 class="visible-lg">large</h3>
  -->
        </div>
      </div>

      <div class="container"> <!-- Main container -->
        <p>currentBranch: {{currentBranch}}</p>
        <p>currentDesk: {{currentDesk}}</p>
        <p>selectBranch: {{selectBranchId}}</p>
        <p>selectDesk: {{selectDeskId}}</p>
        <p>editSettings: {{editSettings}}</p>
        <div class="row" style="padding-bottom: 10px;">

          <!-- Informational question buttons -->
          <div class="col-xs-12 col-md-2 q-type-container">
            <h2 class="q-type-header">Info</h2>
          </div>

          <!-- Easy -->
          <div class="col-xs-4 col-md-3 btn btn-default stat-button" ng-disabled="editSettings" ng-click="submitStat(4)">
            <h3>Easy<span class="hidden-xs"><br/>( &lt; 2 minutes )</span></h3>
          </div>
          <!-- Medium -->
          <div class="col-xs-4 col-md-3 btn btn-default stat-button" ng-disabled="editSettings" ng-click="submitStat(5)">
            <h3>Medium<span class="hidden-xs"><br/>( 2 - 10 minutes )</span></h3>
          </div>
          <!-- Hard -->
          <div class="col-xs-4 col-md-3 btn btn-default stat-button" ng-disabled="editSettings" ng-click="submitStat(6)">
            <h3>Hard<span class="hidden-xs"><br/>( &gt; 10 minutes )</span></h3>
          </div>

        </div>



        <div class="row" style="padding-bottom: 10px;">

          <!-- Directional question buttons -->
          <div class="col-xs-12 col-md-2 q-type-container">
            <h2 class="q-type-header">Directional</h2>
          </div>

          <!-- Easy -->
          <div class="col-xs-4 col-md-3 btn btn-default stat-button" ng-disabled="editSettings" ng-click="submitStat(1)">
            <h3>Easy<span class="hidden-xs"><br/>( &lt; 2 minutes )</span></h3>
          </div>
          <!-- Medium -->
          <div class="col-xs-4 col-md-3 btn btn-default stat-button" ng-disabled="editSettings" ng-click="submitStat(2)">
            <h3>Medium<span class="hidden-xs"><br/>( 2 - 10 minutes )</span></h3>
          </div>
          <!-- Hard -->
          <div class="col-xs-4 col-md-3 btn btn-default stat-button" ng-disabled="editSettings" ng-click="submitStat(3)">
            <h3>Hard<span class="hidden-xs"><br/>( &gt; 10 minutes )</span></h3>
          </div>

        </div>


      <hr>


        <div id="chart_div" class="hidden-xs chart"></div>
  <?php include 'components/footer.php' ?>
      </div> <!-- /Main container -->
    </div> <!-- /mainCtrl scope -->

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.5.6/angular-cookies.js"></script>
    <script src="js/vendor/bootstrap.min.js"></script>
<!--    <script src="js/vendor/js.cookie.js"></script>  -->
<!--    <script src="js/main.js"></script>  -->
    <script src="apps/stat3k.js"></script>
    </body>
</html>
