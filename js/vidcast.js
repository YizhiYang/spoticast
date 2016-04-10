// Inject YoutubeAPI Script
var tag = document.createElement('script');
tag.src = '//www.youtube.com/player_api';
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

var player;
var currentIndex = [0,0];
var ready = false;

window.statusEl = document.getElementById('status');
window.lastFocusStatus = document.hasFocus();

check();
setInterval(check, 200);
var tid = setInterval(checkloaded, 500);
function checkloaded() {
	if(ready == false){
		console.log('not loaded yet');
	} 
	else {
		clearInterval(tid);
		configureControls();
	}
}

function configureControls(){

	window.player.addEventListener('onStateChange', 'goToNext');

	$('#play').click(function() {
		player.playVideo();
	});
	
	$('#pause').click(function() {
		player.pauseVideo();
	});
	
	$('#next').click(function() {
		goToNext(0);
		window.player.addEventListener('onStateChange', 'goToNext');
	});
	
	$('#back').click(function() {
		goToLast();
	});
	
	$( "#links a" ).click(function() {
		window.player.loadVideoByUrl('http://www.youtube.com/embed/'+$(this).data('id')+'?autoplay=1&enablejsapi=1');
		currentIndex = $(this).data('index').split(":").map(Number);
		$('.title p').text(playlistData.index[currentIndex[0]][currentIndex[1]].title);
		return false;
	});
	
}


$( document ).ready(function() {

	$('.controls').click(function(){
		if($(this).hasClass('hide')){
			$(this).text('Show Playlist').removeClass('hide');
			$('.sidebar').fadeOut();
		} else {
			$(this).text('Hide Playlist').addClass('hide');
			$('.sidebar').fadeIn();
		}
	});
	
	$('html').keydown(function(event) {
		
		if (event.keyCode == 32) {
			if(player.getPlayerState() == 1){
				window.player.pauseVideo();
			} else if(player.getPlayerState() == 2) {
				window.player.playVideo();
			}
		}
		if (event.keyCode == 37) {
		 	goToLast();
		}
		if (event.keyCode == 38) {
		 	goToAltUp();
		}
		if (event.keyCode == 39) {
		 	goToNext(0);
		}
		if (event.keyCode == 40) { 
		    goToAltDown();
		}
		if (event.keyCode == 83) { 
		    $('.controls').trigger('click');
		}
	});
	
	/*
	$(".load-more").click(function() {
	    $('body').html(loading).addClass('loading').height('100%').width('100%');
	});
	*/
	
});




// Check for window focus for keyboard controls
function check() {
    if(document.hasFocus() == lastFocusStatus) return;

    lastFocusStatus = !lastFocusStatus;
    if(lastFocusStatus){
	    $('.arrows').removeClass('hidden');
    } else {
	    $('.arrows').addClass('hidden');
    }
}

function onYouTubePlayerAPIReady() {
	player = new YT.Player('video', {
		events: {
			'onReady': onPlayerReady
		}
	});
}

function onPlayerReady(event) {
	window.player.loadVideoByUrl('http://www.youtube.com/embed/'+playlistData.index[currentIndex[0]][0].id+'?autoplay=1&enablejsapi=1');
	$('.title p').text(playlistData.index[currentIndex[0]][0].title);
	ready = true;
}

function goToLast() {
	if(currentIndex == 1){
		console.log('This is the first song on the playlst');
	} else {
		loadAnotherVideo('last');
	}
}

function goToNext(state) {
	if(currentIndex == playlistData.index.length - 1){
		console.log('This is the last song on the playlst');
	} else {
		if(state.data == 0 || state == 0){
			loadAnotherVideo('next');
		}
	}
}

function goToAltUp() {
	loadAnotherVideo('up');
}

function goToAltDown() {
	loadAnotherVideo('down');
}

function loadAnotherVideo(direction) {
	var directionIndex = (direction == 'next' || direction == 'up') ? 1 : -1;
	
	if(direction == 'next' || direction == 'last') {
		var dirIndex = [currentIndex[0]+directionIndex, currentIndex[1]];
		var dirId = playlistData.index[currentIndex[0]+directionIndex][0].id;
		$('.title p').text(playlistData.index[currentIndex[0]+directionIndex][0].title);
	} else {
		if(currentIndex[1] == 0 && direction == 'down') {
			currentIndex[1] = 3;
		}
		if(currentIndex[1] == 2 && direction == 'up') {
			currentIndex[1] = -1;
		}
		var dirIndex = [currentIndex[0], currentIndex[1]+directionIndex];
		var dirId = playlistData.index[currentIndex[0]][currentIndex[1]+directionIndex].id;
		$('.title p').text(playlistData.index[currentIndex[0]][currentIndex[1]+directionIndex].title);
	}
	
	window.player.loadVideoByUrl('http://www.youtube.com/embed/'+dirId+'?autoplay=1&enablejsapi=1');
	currentIndex = dirIndex;
}