var app = angular.module('app', ['autocomplete']);

app.factory('itemRetriever', function($http, $q, $timeout){

  var itemRetriever = new Object();

  itemRetriever.getitems = function(i) {
    var itemdata = $q.defer();
    var items;
    if(i && i.indexOf('T')!=-1)
      items=moreitems;
    else
      items=moreitems;

    $timeout(function(){
      itemdata.resolve(items);
    },1000);

    return itemdata.promise
  }

  itemRetriever.getusers = function(i) {
    var userdata = $q.defer();
    var users;
    if(i && i.indexOf('T')!=-1)
      users=moreusers;
    else
      users=moreusers;

    $timeout(function(){
      userdata.resolve(users);
    },1000);

    return userdata.promise
  }

  return itemRetriever;
});

app.controller('MyCtrl', function($scope, itemRetriever){

  $scope.items = itemRetriever.getitems("...");
  $scope.items.then(function(data){
    $scope.items = data;
  });

  $scope.getitems = function(){
    return $scope.items;
  } 
  $scope.doSomething = function(typedthings){
    $scope.newitems = itemRetriever.getitems(typedthings);
    $scope.newitems.then(function(data){
      $scope.items = data;
    });
  }
  $scope.doSomethingElse = function(suggestion){
    var data = suggestion.split('-');
    addItems(data[1].toNumber(),data[0]);
    $('.ac-items').val('');
  }
  // user
  $scope.users = itemRetriever.getusers("...");
  $scope.users.then(function(data){
    $scope.users = data;
  });
  $scope.getusers = function(){
    return $scope.users;
  }
  $scope.doSomethingUsers = function(typedthings){
    $scope.newitems = itemRetriever.getusers(typedthings);
    $scope.newitems.then(function(data){
      $scope.users = data;
    });
  }
  $scope.doSomethingElseUsers = function(suggestion){
    var data = suggestion.split('-');
    _addCustomer_(data[0],data[1]);
    $('.ac-items').val('');
  }
});