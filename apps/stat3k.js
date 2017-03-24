'use strict';

angular.module('stat3k', ['ngCookies'])

.controller('mainCtrl', function ($cookies, $scope, dataService){
  
  dataService.getDesks(function(response){
    console.log(response.data);
    $scope.desks = response.data;
    $scope.currentDesk = $scope.getCurrentDesk();
  });

  $scope.helloWorld = function(){
    console.log("Hello there!");
  };

  $scope.nothing = function(){
    console.log("This doesn't actually do anything...");
  };

  $scope.echoDeskName = function(){
    console.log($this.desk.name);
  };

  $scope.getCurrentDesk = function(){
    var cookieDesk = $cookies.getObject("desk");
    console.log("cookieDesk: ");
    console.log(cookieDesk);
    console.log("cookieDesk.name: " + cookieDesk.name);
    console.log();

    var currentDesk;

    for (var i = 0; i < $scope.desks.length; i++) {
      if (cookieDesk.id === $scope.desks[i].id && cookieDesk.name === $scope.desks[i].name) {
        console.log(cookieDesk.name + " == " + $scope.desks[i].name + ": " + (cookieDesk.id === $scope.desks[i].id));
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
  };

  $scope.branch = "Crescent Hill";
  $scope.appName = "Stat Cracker 3000";



//  $scope.cookies = $cookies.get("desk");
//  console.log("Before cookies");
//  console.log($scope.cookies);
//
  $cookies.putObject("desk", {"id":1, "name": "Circulation Desk"});
//  $scope.cookies = $cookies.get("desk");
//  console.log("After cookies");
//  console.log($scope.cookies);


})

.service('dataService', function( $http ) {
  this.getCurrentDesk = function() {
    console.log("getting current desk...");
    return $scope.desks[1];
  };

  this.getDesks = function(cb){
    $http.get('mock/desks.json').then(cb)
  };

})

.directive('dailyStats', function(){
  return {
    scope: {},
    controller: 'mainCtrl',
    replace: false
  }
})


;