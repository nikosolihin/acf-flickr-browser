(function($){

	var flickrUrl = 'https://api.flickr.com/services/rest',
		apiKey = $("#fb-api-key").val(),
		userId = $("#fb-user-id").val(),
		dataObject = {
			api_key: apiKey,
			user_id: userId,
			format: 'json',
			nojsoncallback: 1,
			method: ''
		},
		sidebarPartial = $("#SidebarPartial").html(),
		sidebarTemplate = "<ul>{{>recursive_partial}}</ul>",
		albumTemplate = $("#AlbumTemplate").html(),
		previewTemplate = $("#PreviewTemplate").html(),
		counter = " .fb-counter",
		preview = " .fb-preview",
		limit = " #fb-limit",
		extras = " #fb-extras",
		loader = " .fb-loader",
		sidebar = " .fb-sidebar",
		bar = " .fb-bar",
		browser = " .fb-browser",
		album = " .fb-album",
		photo = " .fb-photo",
		welcome = " .photo-browser-welcome",
		resultPanel = " #fb-result-panel";
		Mustache.parse(albumTemplate);
		Mustache.parse(previewTemplate);

	var initializeField = function ( el ) {
		var root = ".acf-" + $(el).data('key').split('_').join('-'),
			flickrSizes = $(root + extras).val(),
			$resultPanel = $(root + resultPanel);
		if( $resultPanel.val() ) {
			var selectedJson = JSON.parse($resultPanel.val()),
				selectedIds = Object.keys(selectedJson);
			$resultPanel.data(selectedJson);
		} else {
			var selectedIds = [];
		}
		getTree(root, dataObject);
		attachEvents(root, flickrSizes, selectedIds);
		loadCounter(root, selectedIds);
		updatePreview(root);
	};

	var getTree = function (root, dataObject) {
		dataObject.method = 'flickr.collections.getTree';
		$.ajax({
			url: flickrUrl,
			data: dataObject,
			type: "GET",
			dataType : "json",
			beforeSend: function() { triggerLoader(root); }
		})
		.always( function() { triggerLoader(root); })
		.done( function (data) {
			var tree = {
				children: [],
			};
			parseTree(data.collections, tree.children);
			$(root + sidebar).html( Mustache.render(sidebarTemplate, tree, { recursive_partial: sidebarPartial }) );
		})
		.fail( function(XMLHttpRequest, textStatus, errorThrown) {
			displayError(errorThrown, textStatus);
		})
	};

	var triggerLoader = function (root) {
		$(root + loader).toggleClass('loader--on');
	};

	var loadCounter = function (root, selectedIds) {
		$(root + counter).text(selectedIds.length + " Photos Selected");
	};

	var attachEvents = function (root, flickrSizes, selectedIds) {
		var $resultPanel = $(root + resultPanel);
		$(root + sidebar).on('click', '.sidebar-item', function(event) {
			event.stopPropagation();
			$(this).toggleClass('sidebar-item--active');
			var albumId = $(this).data('id') || false,
				status = $(this).data('status') || false;
			if( albumId ) {
				$(root + album).addClass('fb-album--hide');
				$(root + welcome).remove();
				if (status) {
					$(root + album + '[data-album-id="' + albumId + '"]').removeClass('fb-album--hide');
				} else {
					getPhotos(root, dataObject, albumId, flickrSizes, selectedIds);
					$(this).data('status', 'fetched');
				}
			}
		});
		$(root + browser).on('click', '.fb-photo', function () {
			var photoId = String($(this).data('id'));
			if( $(this).hasClass('fb-photo--selected') ) {
				$(this).toggleClass('fb-photo--selected');
				$resultPanel.removeData(photoId);
			} else {
				if (Object.keys($resultPanel.data()).length < parseInt($(root+limit).val())) {
					$(this).toggleClass('fb-photo--selected');
					$resultPanel.data( photoId, {
						id: photoId,
						title: $(this).find("span").text(),
						url_sq: $(this).data('sq'),
						url_q: $(this).data('q'),
						url_t: $(this).data('t'),
						url_s: $(this).data('s'),
						url_n: $(this).data('n'),
						url_m: $(this).data('m'),
						url_z: $(this).data('z'),
						url_c: $(this).data('c'),
						url_l: $(this).data('l'),
						url_h: $(this).data('h'),
						url_o: $(this).data('o'),
						description: $(this).data('description'),
						license: $(this).data('license'),
						dateupload: $(this).data('dateupload'),
						datetaken: $(this).data('datetaken'),
						ownername: $(this).data('ownername'),
						originalformat: $(this).data('originalformat'),
						originalwidth: $(this).data('originalwidth'),
						originalheight: $(this).data('originalheight'),
						lastupdate: $(this).data('lastupdate'),
						geo: $(this).data('geo'),
						tags: $(this).data('tags'),
						views: $(this).data('views'),
						media: $(this).data('media')
					});
				} else {
					$(root + ' .fb-notice').show();
				}
			}
			var resultData = $resultPanel.data();
			var resultDataJson = JSON.stringify(resultData);
			$resultPanel.val( resultDataJson );
			$(root + counter).text(Object.keys(resultData).length + " Photos Selected");
			updatePreview(root);
		});
		$(root + bar).on('click', '.fb-deselect', function(event) {
			event.preventDefault();
			deselectAll(root);
		});
	}

	var getPhotos = function (root, dataObject, albumId, flickrSizes, selectedIds) {
		dataObject.method = 'flickr.photosets.getPhotos';
		dataObject.photoset_id = albumId;
		dataObject.extras = flickrSizes;
		$.ajax({
			url: flickrUrl,
			data: dataObject,
			type: "GET",
			dataType : "json",
			beforeSend: function() { triggerLoader(root); }
		})
		.always( function() { triggerLoader(root); })
		.done( function (data) {
			var albumData = {
				albumId: albumId,
				photos: []
			};
			$.each( data.photoset.photo, function(index, photo) {
				albumData.photos[index] = {
					id: photo.id,
					title: photo.title
				};
				flickrSizes.split(',').map( function(val){
					if(val.startsWith("url_")) {
						// These are sizes
						albumData.photos[index][val] = photo[val];
					} else if(val == "geo") {
						// Geolocation, concat lat and long
						albumData.photos[index][val] = photo['latitude']+', '+photo['longitude'];
					} else if(val == "o_dims") {
						// Original dimensions, get width and height
						albumData.photos[index]['originalwidth'] = photo['width_o'];
						albumData.photos[index]['originalheight'] = photo['height_o'];
					} else {
						// These are metas, get rid of underscores
						var newval = val.replace('_', '');
						albumData.photos[index][newval] = photo[newval];
					}
				});
			})
			$(root + browser).append( Mustache.render(albumTemplate, albumData) );
			loadSelected(root, selectedIds);
		})
		.fail( function(XMLHttpRequest, textStatus, errorThrown) {
			displayError(errorThrown, textStatus);
		})
	};

	var loadSelected = function (root, selectedIds) {
		selectedIds.map( function(id) {
			$(root + photo + "[data-id='" + id + "']").addClass('fb-photo--selected');
		});
	};

	var updatePreview = function (root) {
		var photos = JSON.parse(JSON.stringify($(root + resultPanel).data()));
		$(root + preview).empty();
		$.each ( photos, function(index, photo) {
			var previewData = {
				photoId: index,
				url_sq: photo.url_sq
			};
			$(root + preview).append( Mustache.render(previewTemplate, previewData) );
		});
	};

	var deselectAll = function (root) {
		var $resultPanel = $(root + resultPanel);
		$('.fb-photo--selected').toggleClass('fb-photo--selected');
		$resultPanel.removeData();
		$resultPanel.val('');
		updatePreview(root);
		$(root + counter).text("0 Photos Selected");
	};

	var displayError = function (error, status) {
		$(".photo-browser-welcome h1").html("Error retrieving images from Flickr.<br>Please try again.<br>"+error+": "+status);
	};

	var parseTree = function (data, vars) {
		if ('collection' in data) {
			$.each ( data.collection, function(index, obj) {
				vars[index] = {
					title: obj.title,
					icon: 'folder',
					children: []
				};
				parseTree(obj, vars[index].children);
			});
		} else {
			$.each ( data.set, function(index, obj) {
				vars[index] = {
					title: obj.title,
					icon: 'images',
					id: obj.id,
					children: null
				};
			});
		}
	}

	/***************************************************************/
	/*********************** Don't Touch! **************************/
	/***************************************************************/
	if( typeof acf.add_action !== 'undefined' ) {
		/** ACF5 **/
		acf.add_action('ready append', function( $el ){
			acf.get_fields({ type : 'flickr_browser'}, $el).each(function(){
				initializeField( $(this) );
			});
		});
	} else {
		/** ACF4 **/
		$(document).on('acf/setup_fields', function(e, postbox){
			$(postbox).find('.field[data-field_type="flickr_browser"]').each(function(){
				initializeField( $(this) );
			});
		});
	}
})(jQuery);
