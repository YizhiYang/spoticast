<?php

include 'getplaylists.php';
include 'includes/header.php';

?>
	
	<body class="playlists">
		<span class="refresh">Refresh</span>
	
		<h1>Your Playlists</h1>
		<div class="playlists">
			
			<?php
			foreach($playlists as $key => $playlist) {
                echo "<a class='pl' href='vidcast.php?id={$playlist->id}&owner={$playlist->owner->id}&offset=0&total={$playlist->tracks->total}'>{$playlist->name} ({$playlist->tracks->total} tracks) by {$playlist->owner->id}</a>";
			}
			?>
		
			<a href="#" class="load-more">
				<strong>LOAD MORE</strong>
			</a>
		</div>
	</body>
	
	<script>

		var loading = '<div class="wrap"><h1>Loading up your playlist...</h1><img src="img/rings.svg"></div>';
		var userId = "<?php echo $user_id; ?>";
	    
	    $( document ).ready(function() {
	    
	    	if(localStorage.getItem("cachedPlaylists") !== null ){
		    	$('.playlists').html(localStorage.getItem("cachedPlaylists"));
	    	}
	    
	        var offset = $('.pl').length;
	        
	        $('.load-more').click(function(){
		    	$(this).html('<p id="loading-dots"><span>.</span><span>.</span><span>.</span></p>');
			    $.get( "getplaylists.php", { offset: offset } )
				    .done(function( data ) {
						data = jQuery.parseJSON(data);
						
						$.each(data, function() {
						    if(this.owner.id == userId){
						    	var el = "<a class='pl' href='vidcast.php?id="+this.id+"&offset=0&total="+this.tracks.total+"'>"+this.name+" ("+this.tracks.total+" tracks)</a>";
						    }
						    else {
							    var el = "<a class='pl' href='vidcast.php?id="+this.id+"&owner="+this.owner.id+"&offset=0&total="+this.tracks.total+"'>"+this.name+" ("+this.tracks.total+" tracks) by "+this.owner.id+"</a>";
						    }
						    $(el).insertBefore( "a:last-of-type" );
						});

					    if(data.length > 0){
						   $('.load-more').html('<strong>LOAD MORE</strong>');
					    } else {
						   $('.load-more').remove();
					    }
					    localStore();
					    offset = $('.pl').length;
				});
				return false;
			});
	        
	    });

		$('.playlists').on('click', '.pl', function () {
		    $('body').html(loading).addClass('loading').height('100%').width('100%');
		});
		
		$('.refresh').click(function(){
			localStorage.removeItem('cachedPlaylists');
			window.location.replace('playlists.php');
		});
	    
	    function localStore(){
		    if(typeof(Storage) !== 'undefined') {
			    localStorage.setItem('cachedPlaylists', $('.playlists').prop('innerHTML'));
			}
	    }
			
	</script>
	
</html>