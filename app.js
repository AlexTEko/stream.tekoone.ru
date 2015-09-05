'use strict';

var app = angular.module('StreamApp', ['LocalStorageModule']);
var myPlayer = videojs('myplayer', {techOrder: ['html5', 'flash'], autoplay: false});
//var myPlayer = videojs('myplayer', {context:new Dash.di.DashContext()});

app.directive('modal', function () {
    return {
        template: '<div class="modal fade bs-example-modal-sm">' +
        '<div class="modal-dialog modal-sm">' +
        '<div class="modal-content">' +
        '<div class="modal-header">' +
        '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
        '<h4 class="modal-title">{{ title }}</h4>' +
        '</div>' +
        '<div class="modal-body" ng-transclude></div>' +
        '</div>' +
        '</div>' +
        '</div>',
        restrict: 'E',
        transclude: true,
        replace: true,
        scope: true,
        link: function postLink(scope, element, attrs) {
            scope.title = attrs.title;

            scope.$watch(attrs.visible, function (value) {
                if (value == true)
                    $(element).modal('show');
                else
                    $(element).modal('hide');
            });

            $(element).on('shown.bs.modal', function () {
                scope.$apply(function () {
                    scope.$parent[attrs.visible] = true;
                });
            });

            $(element).on('hidden.bs.modal', function () {
                scope.$apply(function () {
                    scope.$parent[attrs.visible] = false;
                });
            });
        }
    };
});

app.controller('indexController', function($scope) {

});

app.controller('playerController', function($scope, $location, $http, $interval, localStorageService) {
	//console.info($location.$$path);
	$scope.currentVideo = " ";
	$scope.isLive = false;
	$scope.isLiveOnline = false;
    $scope.showModalLogin = false;

    $scope.loginData = {
        user: "",
        pass: ""
    };

    $scope.disk = {
        used: "",
        free: "",
        percent: ""
    }

	function get_list() {
		$http.get("api.php", {
			params: {
				do: "get"
			}
		}).success(function (response) {
			$scope.videos = response.records;
			$scope.isLiveOnline = response.live;
            $scope.countLive = response.count;
            $scope.disk.used = response.disk.used;
            $scope.disk.free = response.disk.free;
            $scope.disk.percent = response.disk.percent;
            $scope.cpu = response.cpu;
            $scope.memory = response.memory;
            $scope.cache = response.cache;
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
    if (navigator.appVersion.indexOf("Win")!=-1)
		  myPlayer.src({ type: "rtmp/mp4", src: "rtmp://stream.tekoone.ru/live/test" });
  //  if (navigator.appVersion.indexOf("Mac")!=-1)
    else
		  myPlayer.src({type: "application/x-mpegURL", src: "http://stream.tekoone.ru:8080/hls/test.m3u8"});
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
        if (confirm("Are you sure want to delete?")) {
            var data = "file=" + name + "&token=" + localStorageService.get('token');
            $http.post('api.php?do=delete', data, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}}).success(function () {
                get_list();
            });
        }
    }

    $scope.showLogin = function() {
        $scope.showModalLogin = !$scope.showModalLogin;
    }

    $scope.login = function() {
        var data = "user=" + $scope.loginData.user + "&pass=" + $scope.loginData.pass;
        $http.post('api.php?do=login', data, { headers: { 'Content-Type': 'application/x-www-form-urlencoded' } }).success(function(response) {
            //console.log(response);
            if (response.token)
                localStorageService.set('token',response.token);
            $scope.showModalLogin = !$scope.showModalLogin;
            get_list();
        });
    }

    $scope.isAdmin = function() {
        return (localStorageService.get('token'))
    }

    get_list();
    $interval(function() {get_list()}, 3000);

});
