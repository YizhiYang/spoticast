<?php
session_start();
include 'includes/header.php'; 
?>

<body class="vidcast">
	<div class="overlay loading"><div class="wrap"><h1>Loading up your playlist...</h1><img src="img/rings.svg"></div></div>
	<div class="controls hide">
		<a href="playlists.php">Go to all playlists</a>
		<br>
		<br>
		<br>
		<br>
		<a href="#" class="playlist_toggle hide">Hide Playlist</a>
		<br>
		<br>
		<a href="#" class="shortcuts">Shortcuts</a>
	</div>
	<div class="tutorial">
		<ul>
			<li>Hover over here to toggle menu visibility! ^^^</li>
			<li>Use <strong>left/right</strong> arrows to skip next/last song</li>
			<li>Use <strong>up/down</strong> arrows to see alternate versions of song</li>
			<li>Use <strong>"Spacebar"</strong> to pause/play</li>
		</ul>
	</div>
	<div class="sidebar">
		<div class="sidebarhider">
			<div id="links">
				<ul>
					
					<li class="song" style="display:none">
						<a href="#" class="video-link first" data-index="" data-id=""></a><br>
						<a href="#" class="video-link second" data-index="" data-id="">Alt 1</a>&nbsp;
						<a href="#" class="video-link third" data-index="" data-id="">Alt 2</a>
					</li>
					
					<a class="load-more" href="" style="display:inline-block;margin: 20px 0;">LOAD MORE</a>
					
				</ul>
			</div>
		</div>
	</div>

	<iframe id="video" src="https://www.youtube.com/embed/?autoplay=1&enablejsapi=1" frameborder="0"></iframe>
	
	<div class="title">
		<p></p>
	</div>
	
	<script>
		
	var isMobile = ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) ? true : false;
	
	var player;
	var Playlist = {
	    id: 0,
	    offset: 0,
	    owner: '',
	    total: 0,
	    currentIndex: [0, 0],
	    cachedData: false,
	    ready: false,
	    data: {},
	    noMore: false,
	    // UI Elements
	    songListElement: {},
	    overlay: {},
	    linksList: {},
	    title: {},
	    init: function(id, offset, owner, total) {
	        this.id = id;
	        this.offset = offset;
	        this.owner = owner;
	        this.total = total;
	        this.bindUIElements();
	        this.checkForLocalData();
	    },
	    initialized: function(data) {
	        this.ready = true;
	        window.player.loadVideoByUrl('https://www.youtube.com/embed/' +
	            Playlist.data[0][0].id + '?autoplay=1&enablejsapi=1');
	        this.title.text(Playlist.data[0][0].title);
	        if (Playlist.data.length == Playlist.total) {
	            $('.load-more').remove();
	        }
	        this.overlay.fadeOut();
	    },
	    alert: function(i) {
	        alert(i);
	    },
	    checkForLocalData: function() {
	        // YES it's already stored
	        if (localStorage.getItem("cachedPlaylist" + this.id) !== null) {
	            var data = $.parseJSON(localStorage.getItem(
	                "cachedPlaylist" + this.id));
	            this.cachedData = true;
	            this.populatePlaylist(data);
	            this.data = data;
	            this.initialized(data);
	        }
	        // NO it's not stored
	        else {
	            this.cachedData = false;
	            this.getSongs();
	        }
	    },
	    storeLocalData: function(data) {
	        if (typeof(Storage) !== 'undefined') {
	            var data = JSON.stringify(data);
	            localStorage.setItem('cachedPlaylist' + this.id, data);
	            this.cachedData = true;
	        }
	    },
	    getSongs: function() {
	        var params = {
	            offset: this.offset,
	            id: this.id,
	            owner: this.owner
	        };
	        if (this.noMore == false) {
	            $.get("getsongs.php", params, function(data) {
	                if (data == '[]') {
	                    Playlist.noMore = true;
	                    $('.load-more').slideUp();
	                    return false;
	                }
	                var data = $.parseJSON(data);
	                if (Playlist.cachedData == true) {
	                    $.each(data, function(d) {
	                        Playlist.data.push(this);
	                    })
	                } else {
	                    Playlist.data = data;
	                }
	                Playlist.storeLocalData(Playlist.data);
	                Playlist.populatePlaylist(Playlist.data);
	                configureControls();
	                if (Playlist.ready == false) {
	                    Playlist.initialized(Playlist.data);
	                }
	            });
	        } else {
	            return false;
	        }
	    },
	    populatePlaylist: function(data) {
	        $.each(data, function(i) {
	            this.offset = data.length;
	            var el = Playlist.songListElement.clone();
	            if (this[0] != undefined) {
	                el.css("display", "block");
	                el.find('a:nth-child(1)').text(this[0].title).attr(
	                    'data-id', this[0].id).attr(
	                    'data-index', (Playlist.offset) + ':' +
	                    0);
	                if (this[1] != undefined) {
	                    el.find('a.second').attr('data-id', this[1]
	                        .id).attr('data-index', (Playlist.offset) +
	                        ':' + 1);
	                } else {
	                    el.find('a.second').remove();
	                }
	                if (this[2] != undefined) {
	                    el.find('a.third').attr('data-id', this[2].id)
	                        .attr('data-index', (Playlist.offset) +
	                            ':' + 2);
	                } else {
	                    el.find('a.third').remove();
	                }
	                
	                loadMoreEl = $('.load-more').clone();
	                $('.load-more').remove();
	                Playlist.linksList.append(el);
	                Playlist.linksList.append(loadMoreEl);
	                
	                Playlist.offset = $('.song').length - 1;
	                if (data.length > 0) {
	                    $('.load-more').html(
	                        '<strong>LOAD MORE</strong>');
	                } else {
	                    $('.load-more').remove();
	                }
	            }
	        });
	    },
	    bindUIElements: function() {
	        this.songListElement = $('.song');
	        this.overlay = $('.overlay');
	        this.linksList = $('#links ul');
	        this.title = $('.title p')
	    }
	};
	
	// Inject YoutubeAPI Script
	var tag = document.createElement('script');
	tag.src = '//www.youtube.com/player_api';
	var firstScriptTag = document.getElementsByTagName('script')[0];
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
	var player;
	var ready = false;
	window.statusEl = document.getElementById('status');
	window.lastFocusStatus = document.hasFocus();
	check();
	setInterval(check, 200);
	var tid = setInterval(checkloaded, 500);
	
	function checkloaded() {
	    if (ready == false) {
	        console.log('not loaded yet');
	    } else {
	        clearInterval(tid);
	        Playlist.init("<?php echo $_GET['id']?>", "<?php echo $_GET['offset']?>",
	            "<?php echo $_GET['owner']?: $_SESSION['user_id']?>",
	            "<?php echo $_GET['total']?>");
	        configureControls();
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
	    ready = true;
	}
	
	function goToLast() {
	    if (Playlist.currentIndex == 1) {
	        console.log('This is the first song on the playlst');
	    } else {
	        loadAnotherVideo('last');
	    }
	}
	
	function goToNext(state) {
	    if (Playlist.currentIndex == Playlist.data.length - 1) {
	        console.log('This is the last song on the playlst');
	    } else {
	        if (state.data == 0 || state == 0) {
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
	        var songNumber = Playlist.currentIndex[0];
	        var songAltNumber = Playlist.currentIndex[1];
	        if (direction == 'up') {
	            if (songAltNumber < Playlist.data[songNumber].length - 1) {
	                Playlist.currentIndex[1]++;
	            } else {
	                Playlist.currentIndex[1] = 0;
	            }
	        } else if (direction == 'down') {
	            if (songAltNumber > 0) {
	                Playlist.currentIndex[1]--;
	            } else {
	                Playlist.currentIndex[1] = Playlist.data[songNumber].length - 1;
	            }
	        } else if (direction == 'next') {
	            if (songNumber < Playlist.data.length - 1) {
	                Playlist.currentIndex[0]++;
	                Playlist.currentIndex[1] = 0;
	            } else {
	                console.log('end of playlist');
	                return false;
	            }
	        } else {
	            if (songNumber == 0) {
	                console.log('beginning of playlist');
	                return false;
	            } else {
	                Playlist.currentIndex[0]--;
	                Playlist.currentIndex[1] = 0;
	            }
	        }
	        var dirId = Playlist.data[Playlist.currentIndex[0]][Playlist.currentIndex[
	            1]].id;
	        $('.title p').text(Playlist.data[Playlist.currentIndex[0]][Playlist.currentIndex[
	            1]].title);
	        console.log(Playlist.currentIndex);
	        window.player.loadVideoByUrl('https://www.youtube.com/embed/' + dirId +
	            '?autoplay=1&enablejsapi=1');
	    }
	    // Check for window focus for keyboard controls
	
	function check() {
	    if (document.hasFocus() == lastFocusStatus) return;
	    lastFocusStatus = !lastFocusStatus;
	}
	
	function configureControls() {
		
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
	    $("#links a.video-link").click(function() {
	        window.player.loadVideoByUrl(
	            'https://www.youtube.com/embed/' + $(this).data('id') +
	            '?autoplay=1&enablejsapi=1');
	        Playlist.currentIndex = $(this).data('index').split(":").map(
	            Number);
	        $('.title p').text(Playlist.data[Playlist.currentIndex[0]][
	            Playlist.currentIndex[1]
	        ].title);
	        return false;
	    });
	    $('.load-more').click(function() {
	        $(this).html(
	            '<p id="loading-dots"><span>.</span><span>.</span><span>.</span></p>'
	        );
	        Playlist.getSongs();
	        return false;
	    });
	    $('.playlist_toggle').unbind('click').click(function() {
	        if ($(this).hasClass('hide')) {
	        	$(this).text('Show Playlist');
	            $(this).removeClass('hide');
	            $('.sidebar').fadeOut();
	        } else {
	        	$(this).text('Hide Playlist');
	            $(this).addClass('hide');
	            $('.sidebar').fadeIn();
	            
	        }
	    });
	    if(isMobile) {
		    $('.playlist_toggle').trigger('click');
		    $('.tutorial').html('<ul><li>Tap here for controls! ^^^</li></ul>');
		    $('.tutorial').addClass('visible');
		    $('.controls').click(function(){
			    $('.tutorial').fadeOut();
		    });
		    $('.shortcuts').remove();
	    }
	    $('.playlists').on('click', '.pl', function() {
	        $('body').html(loading).addClass('loading').height(
	            '100%').width('100%');
	    });
	    $('html').keydown(function(event) {
	        if (event.keyCode == 32) {
	            if (player.getPlayerState() == 1) {
	                window.player.pauseVideo();
	            } else if (player.getPlayerState() == 2) {
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
	    });
	    if (localStorage.getItem("tutorial") == null) {
		    $('.tutorial').addClass('visible').delay(10000).queue(function(){
// 				$('.tutorial').removeClass('visible'); 
				localStorage.setItem('tutorial', 'trizz');
			});
		}
		$('.shortcuts').unbind('click').click(function(){
			if($('.tutorial').hasClass('visible')){
				$('.shortcuts').text('Show Tips');
				$('.tutorial').removeClass('visible');
			} else {
				$('.shortcuts').text('Hide Tips');
				$('.tutorial').addClass('visible');
			}
		});
	}	
	</script>

</body>
</html>
