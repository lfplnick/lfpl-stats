'use strict';

angular.module('stat3k', ['ngCookies'])

.controller('mainCtrl', function ($cookies, $scope, dataService){
  $scope.loadSettings = function()
  {
    var branch = $cookies.getObject( "branch" );
    var desk = $cookies.getObject( "desk" );

    if( typeof branch !== 'undefined' )
    {
      $scope.currentBranch = branch;
    } else {
      $scope.clearCurrentBranch();
    }
    
    if( typeof desk !== 'undefined' )
    {
      $scope.currentDesk = desk;
    } else {
      $scope.clearCurrentDesk();
    }
  }

  $scope.getDesks = function( branchId )
  {
    if( -1 === branchId )
    {
      $scope.desks = {};
      $scope.selectDeskId = -1;
    } else {
      dataService.getDesks(
        branchId,
        function(response)
        {
          console.log( response );
          $scope.desks = response.data;
          if(
            $scope.selectBranchId !== -1
            && $scope.selectDeskId === -1
          ){
            $scope.desks.unshift(
              {
                "sp_id": -1,
                "sp_name": "-- Select Desk --",
                "desks_type": ""
              }
            );
          }
        }
      );
    }
  };

  $scope.helloWorld = function(){
    console.log("Hello there!");
  };

  $scope.nothing = function(){
    console.log("This doesn't actually do anything...");
  };

  $scope.echoDeskName = function(){
    console.log($this.desk.name);
  };

  $scope.changeBranch = function()
  {
    console.log( "changing branch...");
    $scope.selectDeskId = -1;
    $scope.getDesks( $scope.selectBranchId );
  };

  $scope.validateCurrentBranch = function()
  {
    if( $scope.currentBranch.branches_id === -1 )
    {
      $scope.clearCurrentDesk();
      return false;
    }

    for( var i = 0; i < $scope.branches.length; i++ )
    {
      if( $scope.currentBranch.branches_id === $scope.branches[i].branches_id
        && $scope.currentBranch.branches_name === $scope.branches[i].branches_name)
      {
        return true;
      }
    }// for branches

    $scope.clearCurrentBranch();
    $scope.clearCurrentDesk();
    return false;
  };//validateCurrentBranch


  /**
   * Validates current desk against database. Should be used as a callback to
   * dataService.getDesks().
   */
  $scope.validateCurrentDesk = function()
  {
    if(
      ( $scope.currentDesk.sp_id === -1 )
      || ( $scope.currentBranch.branches_id === -1 )
    ){
      $scope.clearCurrentDesk();
      return false;
    }

    for( var i = 0; i < $scope.desks.length; i++ )
    {
      if(
        $scope.currentDesk.sp_id === $scope.desks[i].sp_id
        && $scope.currentDesk.sp_name === $scope.desks[i].sp_name
      ){
        return true;
      }
    }// for desks

    $scope.clearCurrentDesk();
    return false;
  }

  $scope.clearCurrentBranch = function()
  {
    $scope.currentBranch = {};
    $scope.currentBranch.branches_id = -1;
    $scope.currentBranch.branches_name = '';
    $scope.currentBranch.branches_abbr = '';
    $scope.clearCurrentDesk();
  }

  $scope.clearCurrentDesk = function()
  {
    $scope.currentDesk = {};
    $scope.currentDesk.sp_id = -1;
    $scope.currentDesk.sp_name = '';
    $scope.currentDesk.desks_type = '';
  }

  $scope.getCurrentDesk = function(){
    var cookieDesk = $cookies.getObject("desk");
    var currentDesk;

    for (var i = 0; i < $scope.desks.length; i++) {
      if (cookieDesk.id === $scope.desks[i].id
        && cookieDesk.name === $scope.desks[i].name)
      {
        currentDesk = $scope.desks[i];
      }
    }

    return currentDesk;
  };//getCurrentDesk

  $scope.openSettings = function()
  {
    $scope.selectBranchId = $scope.currentBranch.branches_id;
    $scope.selectDeskId = $scope.currentDesk.sp_id;
    $scope.editSettings = true;
  };

  $scope.cancelSettings = function()
  {
    $scope.editSettings = false;
    $scope.selectBranchId = $scope.currentBranch.branches_id;
    $scope.selectDeskId = $scope.currentDesk.sp_id;
    $scope.getDesks( $scope.currentBranch.branches_id );
  };// cancelSettings

  $scope.saveSettings = function()
  {
    $scope.currentBranch = $scope.searchBranchIds( $scope.selectBranchId );
    $scope.currentDesk = $scope.searchDeskIds( $scope.selectDeskId );

    $cookies.putObject( "branch", $scope.currentBranch );
    $cookies.putObject( "desk", $scope.currentDesk );
    $scope.editSettings = false;

    if(
      -1 === $scope.branches[0].branches_id
      && -1 !== $scope.selectBranchId
    )
    {
      $scope.branches.shift();
    }

    if(
      -1 === $scope.desks[0].sp_id
      && -1 !== $scope.selectDeskId
    )
    {
      $scope.desks.shift();
    }
  };// saveSettings

  $scope.newSettingsValid = function()
  {
    return ( -1 !== $scope.selectBranchId ) && ( -1 !== $scope.selectDeskId );
  }// settingsValid

  $scope.currentSettingsValid = function()
  {
    return ( -1 !== $scope.currentBranch.branches_id ) && ( -1 !== $scope.currentDesk.sp_id );
  }

  $scope.searchBranchIds = function( id )
  {
    for (var i = 0; i < $scope.branches.length; i++) {
      if( $scope.branches[i].branches_id === id ){
        return $scope.branches[i];
      }
    }

    return false;
  }

  $scope.searchDeskIds = function( id )
  {
    for (var i = 0; i < $scope.desks.length; i++) {
      if( $scope.desks[i].sp_id === id ){
        return $scope.desks[i];
      }
    }

    return false;
  }
  


  $scope.appName = "Stat Cracker 3000";
  $scope.currentBranch = {};
  $scope.currentDesk = {};

  $scope.loadSettings();
  if(
    -1 === $scope.currentBranch.branches_id
    || -1 === $scope.currentDesk.sp_id
  ){
    $scope.editSettings = true;
  } else {
    $scope.editSettings = false;
  }

  dataService.getBranches(
    function( response )
    {
      $scope.branches = response.data;
      if( $scope.currentBranch.branches_id !== -1 )
      {
        if( $scope.validateCurrentBranch() === true )
        {
          dataService.getDesks(
            $scope.currentBranch.branches_id,
            function( response )
            {
              $scope.desks = response.data;
              if( false === $scope.validateCurrentDesk() )
              {
                $scope.desks.unshift(
                  {
                    "sp_id": -1,
                    "sp_name": "-- Select Desk --",
                    "desks_type": ""
                  }
                );
                $scope.openSettings();
              }
            }// getDesks cb
          )// getDesks
        }// current branch valid
      } else {
        $scope.branches.unshift(
          {
            "branches_id": -1,
            "branches_name": "-- Select Branch --",
            "branches_abbr": ""
          }
        );
        $scope.openSettings();
      }
    }
  );

})//controller: mainCtrl

.service('dataService', function( $http )
{
  // this.getCurrentDesk = function() {
  //   console.log("getting current desk...");
  //   return $scope.desks[1];
  // };

  this.getBranches = function( cb ){
    $http.get(
      'api/v1/index.php?whatitdo=sys/branch/list'
    ).then( cb );
  };

  this.getDesks = function(branchId, cb){
    if( branchId != -1 )
    {
      $http.get(
        'api/v1/index.php?whatitdo=sys/branch/'
        + branchId
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