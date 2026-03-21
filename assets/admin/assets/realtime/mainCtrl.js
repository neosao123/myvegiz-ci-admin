/**
 * @author Kishor Mali
 * 
 * File : mainCtrl.js
 * 
 * This is main controller 
 */

var mainCtrl = myApp.controller("mainCtrl", ['$scope', '$http', '$rootScope', function($scope, $http, $rootScope){
	
	$rootScope.conn = new Connection();
	
	$scope.postText="";
	$rootScope.posts = new Array();

}]);