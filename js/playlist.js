var loading = '<div class="wrap"><h1>Loading up your playlist...</h1><img src="img/rings.svg"></div>';
var userId = "<?=$user_id;?>";

updateClick();

$( document ).ready(function() {

	if(localStorage.getItem("cachedPlaylists") !== "undefined"){
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
				    	var el = "<a class='pl' href='vidcast.php?id="+this.id+"&offset=0'>"+this.name+" ("+this.tracks.total+" tracks)</a>";
				    }
				    else {
					    var el = "<a class='pl' href='vidcast.php?id="+this.id+"&owner="+this.owner.id+"&offset=0'>"+this.name+" ("+this.tracks.total+" tracks) by "+this.owner.id+"</a>";
				    }
				    $(el).insertBefore( "a:last-of-type" );
				});

			    if(data.length > 0){
				   $('.load-more').html('<strong>LOAD MORE</strong>');
			    } else {
				   $('.load-more').remove();
			    }
			    localStore();
			    updateClick();
			    offset = $('.pl').length;
		});
		return false;
	});
    
});

function updateClick(){
    $('.pl').click(function(){
    	localStore();
	    $('body').html(loading).addClass('loading').height('100%').width('100%');
    });
}

function localStore(){
    if(typeof(Storage) !== "undefined") {
	    localStorage.setItem("cachedPlaylists", $('.playlists').prop('innerHTML'));
	}
}