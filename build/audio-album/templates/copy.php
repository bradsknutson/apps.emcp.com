<?php
    
    if( isset($_GET['modal']) ) {
        $modal = 'true';
    } else {
        $modal = 'false';
    }
    
    if( isset($_GET['bookshelf']) ) {
        $modal = 'bookshelf';
    } else {
        $modal = 'false';
    }

?>
<!DOCTYPE html>
<html data-ng-app="mdWavesurferApp">
	<head>
        <meta name="viewport" content="width=938, height=1067"/>
        <meta charset="UTF-8"/>
		<title>[TITLE]</title>
        
        <link href="//resources.emcp.com/ebooks/audio-albums/lib/css/material-design-iconic-font.min.css" rel="stylesheet" type="text/css"/>
        <link href="//resources.emcp.com/ebooks/audio-albums/lib/css/github.min.css" rel="stylesheet" type="text/css"/>
        <link href="//resources.emcp.com/ebooks/audio-albums/lib/css/angular-material.min.css" rel="stylesheet" type="text/css"/>
        <link href="//resources.emcp.com/ebooks/audio-albums/lib/css/main.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        
        <script type="text/javascript" src="//resources.emcp.com/ebooks/audio-albums/lib/js/angular.min.js"></script>
        <script type="text/javascript" src="//resources.emcp.com/ebooks/audio-albums/lib/js/angular-animate.min.js"></script>
        <script type="text/javascript" src="//resources.emcp.com/ebooks/audio-albums/lib/js/angular-aria.min.js"></script>
        <script type="text/javascript" src="//resources.emcp.com/ebooks/audio-albums/lib/js/angular-material.min.js"></script>
        <script type="text/javascript" src="//resources.emcp.com/ebooks/audio-albums/lib/js/wavesurfer.min.js"></script>
        <script type="text/javascript" src="//resources.emcp.com/ebooks/audio-albums/lib/js/wavesurfer.directive.js"></script>
        <script type="text/javascript">
            (function () {
              'use strict';
              angular.module('mdWavesurferApp', ['mdWavesurfer'])
                .config(function ($mdIconProvider) { })
                .controller('MainController', ['$scope',
                  function ($scope) {
                    $scope.urls = [[SCOPE]
                    ]; 
                  }]);
            })();        
        </script>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        
	</head>
	<body data-ng-controller="MainController" class="modal-<?php echo $modal; ?>">	
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="modal-center">
                        <div data-md-wavesurfer-audio="" data-player-wave-color="violet" data-player-progress-color="purple" data-player-backend="MediaElement" id="audioContainer">
    [AUDIO_FILE_LIST]
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="modal-center">
                        <img src="[COVER]" />
                    </div>
                </div>
            </div>
        </div>   
    </body>
</html>