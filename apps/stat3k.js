'use strict';

angular.module('stat3k', ['ngCookies'])

.controller('mainCtrl', function ($cookies, $scope, dataService){

  var allCookies = $cookies.getAll();
  console.log( "Cookies:" );
  console.log( allCookies );
  console.log( "===========================" );

  $scope.branch = "Crescent Hill";
  $scope.appName = "Stat Cracker 3000";
  $scope.currentBranch =
  {
    "branches_name": "Crescent Hill",
    "branches_id": "4"
  }

  dataService.getBranches(
    function( response )
    {
      console.log( response.data );
      $scope.branches = response.data;
      $scope.branches.sort(
        function( a, b )
        {
          var branchA = a.branches_name.toUpperCase();
          var branchB = b.branches_name.toUpperCase();

          if( branchA < branchB){ return -1; }
          if( branchA > branchB){ return 1; }
          return 0;
        }
      );
      $scope.currentBranch = $scope.getCurrentBranch();
    }
  );
  console.log( "Current branch:" );
  console.log( $scope.currentBranch );
  console.log( "=============================" );


  $scope.getDesks = function( branch )
  {
    dataService.getDesks(
      branch,
      function(response)
      {
        console.log(response.data);
        $scope.desks = response.data;
        $scope.desks.sort(
          function( a, b )
          {
            var deskA = a.sp_name.toUpperCase();
            var deskB = b.sp_name.toUpperCase();
  
            if( deskA < deskB){ return -1; }
            if( deskA > deskB){ return 1; }
            return 0;
          }
        );
        $scope.currentDesk = $scope.getCurrentDesk();
      }
    );
  };
  $scope.getDesks( $scope.currentBranch );
  
  $scope.helloWorld = function(){
    console.log("Hello there!");
  };

  $scope.nothing = function(){
    console.log("This doesn't actually do anything...");
  };

  $scope.echoDeskName = function(){
    console.log($this.desk.name);
  };

  $scope.getCurrentBranch = function()
  {
    var cookieBranch = $cookies.get("branch");
    console.log("cookieBranch: ");
    console.log(cookieBranch);
    console.log("cookieBranch.name: " + cookieBranch.name);
    console.log();

    var currentBranch;

    for( var i = 0; i < $scope.branches.length; i++ )
    {
      if( cookieBranch.id === $scope.branches[i].id
        && cookieBranch.name === $scope.branches[i].name)
      {
        console.log(
          cookieBranch.name + " == "
          + $scope.branches[i].name + ": "
          + (cookieBranch.id === $scope.branches[i].id)
        );
        currentBranch = $scope.branches[i];
      }
    }

    return currentBranch;
  };//getCurrentBranch

  $scope.getCurrentDesk = function(){
    var cookieDesk = $cookies.getObject("desk");
    console.log("cookieDesk: ");
    console.log(cookieDesk);
    console.log("cookieDesk.name: " + cookieDesk.name);
    console.log();

    var currentDesk;

    for (var i = 0; i < $scope.desks.length; i++) {
      if (cookieDesk.id === $scope.desks[i].id
        && cookieDesk.name === $scope.desks[i].name)
      {
        console.log(
          cookieDesk.name + " == "
          + $scope.desks[i].name + ": "
          + (cookieDesk.id === $scope.desks[i].id)
        );
        currentDesk = $scope.desks[i];
      }
    }

    return currentDesk;

//    var currentDesk;
//    var cookieDesk = $cookies.get("desk");
//console.log("cookieDesk" + cookieDesk);
//    console.log($scope.desks);
//    for ( var i = 0; i < $scope.desks.length; i++ ) {
//      if (cookieDesk.id == $scope.desks[i].id){
//        console.log("current desk set to: " + $scope.desks[i].name);
//        currentDesk = $scope.desks[i];
//        break;
//      }
//    }
//    return currentDesk;
  };//getCurrentDesk




//  $scope.cookies = $cookies.get("desk");
//  console.log("Before cookies");
//  console.log($scope.cookies);
//
  $cookies.putObject("desk", {"id":1, "name": "Circulation Desk"});
//  $scope.cookies = $cookies.get("desk");
//  console.log("After cookies");
//  console.log($scope.cookies);


})//controller: mainCtrl

.service('dataService', function( $http )
{
  this.getCurrentDesk = function() {
    console.log("getting current desk...");
    return $scope.desks[1];
  };

  this.getBranches = function( cb ){
    $http.get(
      'api/v1/index.php?whatitdo=sys/branch/list'
    ).then( cb );
  };

  this.getDesks = function(branch, cb){
    if( branch != '' )
    {
      $http.get(
        'api/v1/index.php?whatitdo=sys/branch/'
        + branch.branches_id
        + '/sp'
      ).then(cb);     
    }
  };

})//service: dataService

.directive('dailyStats', function()
{
  return {
    scope: {},
    controller: 'mainCtrl',
    replace: false
  }
})


;