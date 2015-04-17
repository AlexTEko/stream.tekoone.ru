'use strict';

var app = angular.module('StreamApp', ['LocalStorageModule']);
var myPlayer = videojs('myplayer', {techOrder: ['html5', 'flash'], autoplay: false}); 
//var myPlayer = videojs('myplayer', {context:new Dash.di.DashContext()});


app.controller('indexController', function($scope) {

});

app.controller('playerController', function($scope, $location, $http, $interval, localStorageService) {
	//console.info($location.$$path);
	$scope.currentVideo = " ";
	$scope.isLive = false;
	$scope.isLiveOnline = false;

	function get_list() {
		$http.get("api.php", {
			params: {
				do: "get"
			}
		}).success(function (response) {
			$scope.videos = response.records;
			$scope.isLiveOnline = response.live;
		})
	}

	$scope.play = function (name) {
		if (myPlayer.currentType() == "rtmp/mp4")
			myPlayer.loadTech('Html5');
		myPlayer.src({ type: "video/mp4", src: name});
		$scope.currentVideo = name;
		$scope.isLive = false;
	}

	$scope.live = function () {
		$scope.currentVideo = "Live Stream";
		$scope.isLive = true;
		myPlayer.src({ type: "rtmp/mp4", src: "rtmp://stream.tekoone.ru/live/test" });
		//myPlayer.src({type: "application/dash+xml", src: "http://stream.tekoone.ru:8080/dash/test.mpd"});
		myPlayer.duration(0);
		myPlayer.play();
	}

	$scope.update = function () {
		$http.get("api.php", {
			params: {
				do: "update"
			}
		}).success(function (response) {
			get_list();
		})
	}

  	if ($location.$$path) {
		//console.log($location);
		if ($location.$$path == "/live")
		{
			$scope.live();
			$scope.isLive = true;
		}
		else {
			$scope.currentVideo = $location.$$path;
			//console.log($location.$$path.split("/").slice(-1)[0] );
			myPlayer.src({ type: "video/mp4", src: 'rec'+ $location.$$path});
			if ($location.$$path.split("/").slice(-1)[0] ) {
				myPlayer.currentTime($location.$$path.split("/").slice(-1)[0]);
				myPlayer.play();
			}
			$scope.isLive = false;
		}
	}

    $scope.delete = function (name) {
        var data = "file=" + name + "&token=" + localStorageService.get('token');
        $http.post('api.php?do=delete', data, { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }).success(function() {
            get_list();
        });
    }

    get_list();
    $interval(function() {get_list()}, 5000);

});