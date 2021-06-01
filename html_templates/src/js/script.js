var serviceNav = $('.service-nav');

var state = {
  currentMarkerId: undefined,
  isInformPanelOpened: false,
  vectorLayers: [],
  pointsOnMap: [],
  activeLayerId: 'markersLayer',
  glat: '',
  gLng: '',
  tags: [],
  cUserId: undefined
}

var initMapParams = {
  minLng: 48.746246337890625,
  maxLng: 50.546600341796875,
  minLat: 32.069488525390625,
  maxLat: 35.475067138671875,
  pitch: 0,
  zoom: 9,
}

var bounds = new maptalks.Extent(
  initMapParams.minLat, initMapParams.minLng,
  initMapParams.maxLat, initMapParams.maxLng
);
var addedPoints = [];
var markers = [];
var map = undefined;
var markerPopup = null;
var unactiveMarkerInfoWindow = new maptalks.ui.InfoWindow({
  'autoCloseOn': 'click',
  'single' : true,
  'width'  : 'auto',
  'height' : 'auto',
  'custom' : true,
  'dx' : 0,
  'dy' : 0,
  'content': '<div class="unactive-marker">Ця точка знаходиться на модерації</div>'
});

if($('#wpadminbar').length) {
  $('body').css('padding-top', '32px');
}

var center = undefined;
var visitId = undefined;

function checkUrlGet () {
  var urlParams = new URLSearchParams(window.location.search);
  var visitId = urlParams.get('visitid');
  return visitId
}

function changeZIndexes () {
  var allMarkers = map.getLayer(state.activeLayerId).getGeometries();
  if (allMarkers.length) {
    for (var i = 0; i < allMarkers.length - 1; i++) {
      var distance = map.coordinateToContainerPoint(allMarkers[i].getCenter()).y;
      if (distance >= 0) {
        allMarkers[i].setZIndexSilently(Math.round(distance))
      }
    }
    var distance = map.coordinateToContainerPoint(allMarkers[allMarkers.length - 1].getCenter()).y;
    allMarkers[allMarkers.length - 1].setZIndex(Math.round(distance))
  }
}

function initMap () {
  if (checkUrlGet()) {
    visitId = checkUrlGet()
    var p = points.filter(function (item) {
      return item.id_wp == visitId
    })[0]
    if (p) {
      center = [+p.lat, +p.lang]
    }
  }
  $('body').append(
    $('<div class="help-info"><span>ЛКМ - переміщатися по карті</span><span>ПКМ - змінити кут огляду</span></div>')
  )
  $('.user-control-panel > h3').append('<a href="#" class="toggle-ctrl-panel-btn"></a>')
  state.cUserId = $('body').attr('data-cur-user-id');

  if (state.tags.length) {
    for (var i = 0; i < state.tags.length; i++) {
      state.tags[i] = $.trim(state.tags[i])
    }
  }

  if (!$('.burger-btn').length) {
    $('.header .container .main-nav').prepend(
      '<a href="#" class="burger-btn">' +
        '<span></span>' +
      '</a>'
    )
  }

  map = new maptalks.Map('map', {
    center: center || bounds.getCenter(),
    zoom: initMapParams.zoom,
    pitch: initMapParams.pitch,
    minZoom: 9,
    maxZoom : 18,
    zoomControl : false,
    attribution: false,
    maxPitch: 60,
    maxVisualPitch: 80,
    baseLayer: new maptalks.TileLayer('base', {
      urlTemplate: 'https://mt{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',
      subdomains: [0, 1, 2, 3],
      forceRenderOnMoving: true,
      forceRenderOnZooming: true,
      forceRenderOnRotating: true,
      cascadeTiles: false,
      maxCacheSize: 2048
    })
  });

  map.setFov(49)

  new maptalks.VectorLayer('regions').addTo(map);
  new maptalks.VectorLayer('markersLayer').addTo(map);
  new maptalks.VectorLayer('kml-layer').addTo(map);
  new maptalks.VectorLayer('route-layer').addTo(map);

  map.setMaxExtent(bounds);

  drawRegionsLines(map, 'regions');

  anime({
    targets: [initMapParams],
    pitch: 55,
    zoom: 13,
    duration: 5000,
    easing: 'easeInOutExpo',
    loop: false,
    autoplay: false,
    update: function () {
      map.setPitch(initMapParams.pitch);
      map.setZoom(initMapParams.zoom, { animation: false });
    },
    complete: function () {
      $('.map-disable-overlay').hide();
      $('.main-content').addClass('ready');
    }
  }).play();
  $('.intro').fadeOut(1000);

  routes = routes.reverse();
}

function drawRegionsLines (addToMap, layerId, lineColor, opacity, contains) {
  maptalks.Formats.kml(regionsUrl, function (err, res) {
    if (err) {
      console.log(err)
    }
    if (res) {
      for (var i = 0; i < res.features.length; i++) {
        res.features[i].geometry.type = 'Polygon'
      }
      maptalks.GeoJSON.toGeometry(res, function (geometry) {
        geometry.setSymbol({
          'lineColor' : lineColor || '#f97e14',
          'lineWidth' : 1,
          'polygonFill' : null,
          'polygonOpacity' : opacity || 0.1
        });
        geometry.addTo(addToMap.getLayer(layerId));
      });
      if (contains) {
        var geoms = addToMap.getLayer(layerId).getGeometries();
        var findContain = false
        for (var g = 0; g < geoms.length; g++) {
          if (findContain) break;
          var geometry = geoms[g];
          for (var i = 0; i < contains.length; i++) {
            if (geometry.containsPoint(contains[i])) {
              geometry.updateSymbol({
                'polygonFill': '#9ea9b3'
              })
              findContain = true;
            }
          }
        }
      }
    }
  })
}

function addMarkers (layerId, markersArr) {
  var markersLayer = map.getLayer(layerId);
  state.activeLayerId = layerId;
  markers = [];
  for (var i = 0; i < markersArr.length; i++) {
    if ($('body').attr('data-cur-user-id')) {
      var uId = $('body').attr('data-cur-user-id')
      if (markersArr[i].status_point == 'private' && uId != markersArr[i].created_by) continue;
    }
    var coordConverted = convert(markersArr[i].lang + ', ' + markersArr[i].lat);
    markers.push({
      id: markersArr[i].id_wp,
      lat: coordConverted.decimalLatitude,
      lng: coordConverted.decimalLongitude,
      status: markersArr[i].user_status
    })
    var marker = new maptalks.Marker([markersArr[i].lat, markersArr[i].lang], {
      id: markersArr[i].id_wp + '_' + i,
      visible: true,
      cursor: 'pointer',
      shadowBlur: '10px',
      shadowColor: '#000000',
      symbol: [
        {
          markerType: 'ellipse',
          markerFill: {
            type : 'radial',
            colorStops : [
              [0.00, 'rgba(0, 0, 0, 0)'],
              [0.50, 'rgba(0, 0, 0, 0.5)'],
              [1.00, 'rgba(0, 0, 0, 0.5)']
            ]
          },
          markerWidth: 70,
          markerHeight: 20,
          markerLineWidth: 0
        },
        {
          markerFile: markerIcons[markersArr[i].user_status],
          markerWidth: '49',
          markerHeight: '72'
        }
      ]
    }).on('click', onMarkerClick);
    marker.addTo(markersLayer);
    if (visitId) {
      if (visitId == markersArr[i].id_wp) marker.fire('click')
    }
  }
  changeZIndexes();
}

function onMarkerClick(e) {
  var id = e.target.getId().split('_')[0];
  state.currentMarkerId = id;
  var marker = markers.filter(function (item) { return item.id == id })[0]
  var point = points.filter(function (item) { return item.id_wp == id })[0]
  map.panTo({
    x: marker.lng,
    y: marker.lat
  }, {
    duration: 3000
  });
  if (point.user_status === 'unactive') {
    unactiveMarkerInfoWindow.addTo(e.target);
    setTimeout (function () {
      e.target.openInfoWindow();
    }, 300);
  } else {
    createPopup(point);
    miniMap(e.target);
    map.removeLayer('kml-layer');
    if (point.file_kml) {
      new maptalks.VectorLayer('kml-layer').addTo(map);
      maptalks.Formats.kml(point.file_kml, function (err, geoJson) {
        if (err) {
          console.log(err, err.message)
        }
        if (geoJson) {
          for (var i = 0; i < geoJson.features.length; i++) {
            if (geoJson.features[i].geometry.type === 'Point') continue;
            var json = geoJson.features[i];
            maptalks.GeoJSON.toGeometry(json).addTo(map.getLayer('kml-layer'));
            for (var i = 0; i < map.getLayer('kml-layer').getGeometries().length; i++) {
              map.getLayer('kml-layer').getGeometries()[i].setSymbol({
                'lineColor' : '#f97e14',
                'lineWidth' : 3,
                'polygonFill' : '#f97e14',
                'polygonOpacity' : 0.3
              });
            }
          }
        }
      });
    }
  }
}

function createPopup (data) {
  $('.markers-popups').html('')
  markerPopup = $('<div class="marker-popup ua"></div>');
  popupTitle({
    audio: data.audio,
    model3d: (data['3d'].file_obj && data['3d'].file_mtl) ? data['3d'].url_to_3d_view : false,
    panorama: data.panorama.file_panorama ? data.panorama.url_to_panorama : false,
    pohovannya: data.pohovannya.length ? data.pohovannya : [],
    title: {
      ua: data.title,
      en: data.title_en || data.title
    },
    subtitle: {
      ua: data.subtitle || '',
      en: (data.subtitle_en || data.subtitle) || ''
    }
  })
  popupContent({
    mainImage: data.main_img || '',
    description: {
      ua: data.description || '',
      en: (data.description_en || data.description) || ''
    },
    security: {
      image: data.image_security || '',
      description: {
        ua: data.description_security || '',
        en: (data.description_security_en || data.description_security) || ''
      },
      file: {
        url: {
          ua: data.file_security.url || '',
          en: (data.file_security_en.url || data.file_security.url) || ''
        },
        name: {
          ua: data.file_security.name || '',
          en: (data.file_security_en.name || data.file_security.name) || ''
        }
      }
    },
    scientist_articles: data.scientist_articles,
    gallery: data.gallery,
    toTopic: data.do_temi
  });
  popupEditPannel({
    canDelete: data.credentionalsToDelete,
    canEdit: data.credentionalsToEdit,
    wp_id: data.id_wp
  });
  $('.markers-popups').append(markerPopup);
  markerPopup.fadeIn(300);
  $('.bg-marker-overlay').fadeIn(300);
  $('.markers-popups').find('.marker-popup__content').css('max-height', 'calc(100% - ' + $('.markers-popups').find('.marker-popup__title').outerHeight() + 'px)');
  var conf = {
    autoHide: false,
    scrollbarMaxSize: 60
  }
  $('.marker-popup__content').each(function (index, el) {
    new SimpleBar(el, conf);
  });
}

function popupTitle (params) {
  var popupTitle = '<div class="marker-popup__title"><div class="service-btn-container">';
  if (params.audio) {
    popupTitle += '<a href="#" class="listen-btn" data-audio-src="' + params.audio + '"></a>'
  }
  popupTitle += '<a href="#" class="ua-btn">UA</a><a href="#" class="en-btn">EN</a></div>'
  popupTitle += '<div class="title ua">' +
      '<h2>' + params.title.ua + '</h2>' +
      '<h3>' + params.subtitle.ua + '</h3>' +
    '</div>' +
    '<div class="title en">' +
      '<h2>' + params.title.en + '</h2>' +
      '<h3>' + params.subtitle.en + '</h3>' +
    '</div>'
  popupTitle += '<div class="panorama-and-3d">'
  if (params.model3d) {
    popupTitle += '<a href="' + params.model3d + '" class="btn-3d"></a>'
  }
  if (params.panorama) {
    popupTitle += '<a href="' + params.panorama + '" class="btn-panorama"></a>'
  }
  if (params.pohovannya.length) {
    popupTitle += '<a href="' + params.pohovannya[0].url + '" class="btn-pohovannya"></a>'
  }
  popupTitle += '</div>';
  popupTitle +=  '</div>';
  markerPopup.append($(popupTitle))
}

function popupContent (params) {
  var content = '<div class="marker-popup__content"></div>';
  var description = '<div class="marker-popup__description">';
  // Description main photo
  description += '<div class="photo">' +
      '<img src="' + params.mainImage + '" alt="">' +
    '</div>';
  // Description text
  description += '<div class="text ua">' +
      '<span>' +
        params.description.ua +
      '</span>' +
      '<a href="#" class="read-more-btn">Читати далі</a>' +
    '</div>' +
    '<div class="text en">' +
      '<span>' +
        params.description.en +
      '</span>' +
      '<a href="#" class="read-more-btn">Read more</a>' +
    '</div>';
  description += '</div>';
  // Security information
  var securityInformation = '<div class="marker-popup__information">' +
    '<div class="marker-popup__information__title">' +
      '<h3 class="ua">Пам’яткоохоронна інформація</h3>' +
      '<h3 class="en">Monument protection information</h3>' +
    '</div>';
  securityInformation += '<div class="marker-popup__information__description">' +
    '<div class="photo">' +
      '<img src="' + params.security.image + '" alt="">' +
    '</div>' +
    '<div class="text ua">' +
      params.security.description.ua +
      '<a href="' + params.security.file.url.ua + '" target="_blank">' +
        params.security.file.name.ua +
      '</a>' +
    '</div>' +
    '<div class="text en">' +
      params.security.description.en +
      '<a href="' + params.security.file.url.en + '" target="_blank">' +
        params.security.file.name.en +
      '</a>' +
    '</div>' +
    '<div class="mini-map">' +
      '<div class="mini-map-overlay"></div>' +
    '</div>';
  securityInformation += '</div>'
  // Publications
  var publications = '<div class="marker-popup__publications">' +
    '<div class="marker-popup__publications__title">' +
      '<h3 class="ua">Наукові публікації</h3>' +
      '<h3 class="en">Scientific publications</h3>' +
    '</div>' +
    '<div class="marker-popup__publications__list">';
  for (var i = 0; i < params.scientist_articles.length; i++) {
    publications += '<div class="publication">';
    if (params.scientist_articles[i].image) {
      publications += '<div class="publication__thumbnail">' +
          '<img src="' + params.scientist_articles[i].image + '" alt="">' +
        '</div>';
    }
    publications += '<div class="publication__description ua">' +
      params.scientist_articles[i].text || '';
    if (params.scientist_articles[i].file_url) {
      publications += '<a href="' + params.scientist_articles[i].file_url + '" target="_blank">' +
          params.scientist_articles[i].file_name +
        '</a>';
    }
    publications += '</div>';
    publications += '<div class="publication__description en">' +
      (params.scientist_articles[i].eng_text || params.scientist_articles[i].text) || '';
    if (params.scientist_articles[i].file_url) {
      publications += '<a href="' + params.scientist_articles[i].file_url + '" target="_blank">' +
          params.scientist_articles[i].file_name +
        '</a>';
    }
    publications += '</div>';
    publications += '</div>';
  }
  publications += '</div></div>';
  // Media
  var media = '<div class="marker-popup__media"><div>';
  // Gallery
  media += '<div class="gallery">';

  media += '<div class="gallery__title">' +
      '<div>' +
        '<h3 class="ua">Фотогалерея</h3>' +
        '<h3 class="en">Gallery</h3>' +
      '</div>' +
      '<a href="#" class="show-more-btn">' +
        '<span class="ua">Дивитися більше</span>' +
        '<span class="en">Show more</span>' +
      '</a>' +
    '</div>';

  media += '<div class="gallery__photos">';
  for (var i = 0; i < 3; i++) {
    if (params.gallery.images[i])
    media += '<div style="background-image: url(' + params.gallery.images[i].url + '); background-size: cover"></div>'
  }
  media += '</div></div></div>';

  media += '<div><div class="video">' +
    '<div class="video__title">' +
      '<h4 class="ua">Відео</h4>' +
      '<h4 class="en">Video</h4>' +
      '<a href="#" class="show-more-btn">' +
        '<span class="ua">Дивитися більше</span>' +
        '<span class="en">Show more</span>' +
      '</a>' +
    '</div>';
  media += '<div class="video__list">';
  if (params.gallery.videos[0]) {
    media += '<div>' +
          '<iframe src="' +
            params.gallery.videos[0] +
          '" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>' +
        '</div>';
  }
  media += '</div></div>';
  media += '<div class="other-resources">' +
    '<div class="other-resources__title">' +
      '<h4 class="ua">До теми</h4>' +
      '<h4 class="en">To the topic</h4>' +
      '<a href="#" class="show-more-btn">' +
        '<span class="ua">Дивитися більше</span>' +
        '<span class="en">Show more</span>' +
      '</a>' +
    '</div>'
  if (params.toTopic.length) {
    media += '<div class="other-resources__sources">'
    for (var i = 0; i < 1; i++) {
      media += '<div>';
      var ext = params.toTopic[i].url.split('.')
      if(ext[ext.length - 1] === 'png' || ext[ext.length - 1] === 'jpg') {
        media += '<img src="' + params.toTopic[i].url + '" alt="' + params.toTopic[i].filename + '">';
      } else {
        media += '<span class="file-icon">' + ext[ext.length - 1] + '</span><span>' + params.toTopic[i].filename + '</span>';
      }
      media += '</div>';
    }
    media += '</div>'
  }
  media += '</div>';
  media += '</div></div>';


  markerPopup.append(
    $(content)
      .append($(description))
      .append($(securityInformation))
      .append($(publications))
      .append($(media))
  );
}

function popupEditPannel (params) {
  var editPanel = '<div class="marker-popup__edit-btn"><div>';
  if (params.canEdit) {
    editPanel += '<a href="#" class="edit-point-btn">' +
        '<span class="ua">Редагувати</span>' +
        '<span class="en">Edit</span>' +
      '</a>';
  }
  if (params.canDelete) {
    editPanel += '<a href="#" class="deactivate-point-btn" data-delete-id="' + params.wp_id + '">' +
        '<span class="ua">Деактивувати</span>' +
        '<span class="en">Deactivate</span>' +
      '</a>';
  }
  editPanel += '</div>'
  editPanel += '<div class="share-block">' +
      '<a href="#" class="share share--fb" data-share-id="' + params.wp_id + '"></a>' +
    '</div>'
  editPanel += '</div>';
  markerPopup.append($(editPanel));
}

function routeDetails (routeId) {
  var ind = 1;
  var cRoute = routes.filter(function (item, index) {
    if (item.wp_id == routeId) {
      ind = index
      return true
    }
  })[0]
  if (state.cUserId && cRoute.edit.id_user == state.cUserId) {
    $('#edit-route').fadeIn();
  } else {
    $('#edit-route').fadeOut();
  }
  $('.route-details .index-of-route').text(ind + 1);
  $('.route-details .title-of-route').text(cRoute.title);
  $('.route-details .route-details-ua').text(cRoute.description);
  $('.route-details .route-details-en').text(cRoute.descriptionEn);
  if (cRoute.audio && cRoute.audio.url) {
    $('.route-details .audio-btn').attr('data-audio-src', cRoute.audio.url)
    $('.route-details .audio-btn').fadeIn()
  } else {
    $('.route-details .audio-btn').fadeOut()
  }
  $('.route-details img').attr('src', (cRoute.bg_route || ''));
  $('.route-details img').attr('alt', '');
  $('.route-details').removeClass('en').addClass('ua');
  $('.route-details').fadeIn();
  $('.route-details__window').each(function (index, el) {
    new SimpleBar(el);
  });
}

function generateSlider(htmlContent) {
  $('.slider-container .carousel').html(htmlContent);
  $('.slider-container .carousel').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    adaptiveHeight: true,
    prevArrow: '<a href="#" class="slider-btn slider-btn--prev"></a>',
    nextArrow: '<a href="#" class="slider-btn slider-btn--next"></a>'
  });
  $('.slider-container').fadeIn(300);
}

function addRoutesList () {
  for (var i = 0; i < routes.length; i++) {
    if (routes[i].status == 'private') continue;
    $('.ways-window__ways ul').append(
      $('<li><a href="#">' + routes[i].title + '</a></li>')
    );
    $('.list-of-ways .ways-container').append(
      $(
        '<div class="way" data-way-id="' + routes[i].wp_id + '" data-points="' + routes[i].points + '">' +
          '<div class="way__title">' +
            '<h4>' + routes[i].title + '</h4>' +
          '</div>' +
          '<div class="way__content" style="background-image: url(' + routes[i].image_route + ')">' +
            '<img src="' + (routes[i].bg_route || '') + '">' +
            '<h3>' +
              (routes[i].subtitle || '') +
            '</h3>' +
          '</div>' +
          '<div class="types-of-route">' +
            (routes[i].virtual_route ? '<span class="virtual-route-icon"> - Віртуальний</span>' : '') +
            (routes[i].real_route ? '<span class="real-route-icon"> - Реальний</span>' : '') +
          '</div>' +
        '</div>'
      )
    )
  }
}

function playAudio(src) {
  $('.global-audio audio').attr('src', src);
  $('.global-audio').fadeIn();
}

function listeners () {
  $(document).on('click', '.toggle-ctrl-panel-btn', function (e) {
    e.preventDefault();
    $('.user-control-panel').toggleClass('show');
  });
  $('.markers-popups').on('click', '.en-btn', function (e) {
    e.preventDefault();
    $('.markers-popups').find('.marker-popup').addClass('en');
    $('.markers-popups').find('.marker-popup').removeClass('ua');
  });
  $('.markers-popups').on('click', '.ua-btn', function (e) {
    e.preventDefault();
    $('.markers-popups').find('.marker-popup').addClass('ua');
    $('.markers-popups').find('.marker-popup').removeClass('en');
  });
  $(document).on('click', 'a[data-audio-src]', function(e) {
    e.preventDefault();
    var src = $(this).attr('data-audio-src');
    playAudio(src);
  });
  $('.markers-popups').on('click', '.btn-panorama', function (e) {
    e.preventDefault();
    var href = $(this).attr('href');
    $('.panorama-container iframe').attr('src', href);
    $('.panorama-container').fadeIn(300);
  });
  $('.markers-popups').on('click', '.btn-3d', function (e) {
    e.preventDefault();
    var href = $(this).attr('href');
    $('.model-previewer-container iframe').attr('src', href);
    $('.model-previewer-container').fadeIn(300);
  });
  $('.markers-popups').on('click', '.btn-pohovannya', function (e) {
    e.preventDefault();
    var href = $(this).attr('href');
    $('.pohovannya-previewer-container iframe').attr('src', href);
    $('.pohovannya-previewer-container').fadeIn(300);
    $('.pohovannya-previewer-container iframe').css('opacity', '0')
    $('.pohovannya-previewer-container').append('<p class="load-info">Завантаження...</p>')
    $('.pohovannya-previewer-container iframe').on('load', function(e) {
      var content = $('.pohovannya-previewer-container iframe').contents();
      content.find('h1, br').css('display', 'none');
      content.find('body, html').css({
        'padding': '0',
        'margin': '0'
      });
      $('.pohovannya-previewer-container iframe').css('opacity', '1');
      $('.pohovannya-previewer-container').find('.load-info').remove();
      $('.pohovannya-previewer-container iframe').attr('style', content.find('#container').attr('style'))
    });
  });
  $('.markers-popups').on('click', '.marker-popup .text .read-more-btn', function (e) {
    e.preventDefault();
    $(this).parent().toggleClass('text--show-all');
  });
  $('.markers-popups').on('click', '.gallery__photos div, .gallery .show-more-btn', function (e) {
    e.preventDefault();
    var htmlContent = ''
    var point = points.filter(function (item) { return item.id_wp == state.currentMarkerId })[0]
    for (var i = 0; i < point.gallery.images.length; i++) {
      htmlContent += '<div>' +
          '<img src="' + point.gallery.images[i].url + '" alt="">' +
          '<p>' + (point.gallery.images[i].description || '') + '</p>' +
        '</div>';
    }
    generateSlider(htmlContent);
  });
  $('.markers-popups').on('click', '.video__list div, .video .show-more-btn', function (e) {
    e.preventDefault();
    var htmlContent = ''
    var point = points.filter(function (item) { return item.id_wp == state.currentMarkerId })[0];
    for (var i = 0; i < point.gallery.videos.length; i++) {
      htmlContent += '<div>' +
        '<iframe src="' + point.gallery.videos[i] + '" allow="accelerometer;' +
        ' encrypted-media; gyroscope; picture-in-picture"' +
        ' allowfullscreen></iframe>' +
        '</div>';
    }
    generateSlider(htmlContent);
  });
  $('.markers-popups').on('click', '.other-resources__sources div, .other-resources .show-more-btn', function (e) {
    e.preventDefault();
    var htmlContent = '';
    var point = points.filter(function (item) { return item.id_wp == state.currentMarkerId })[0];
    for (var i = 0; i < point.do_temi.length; i++) {
      htmlContent += '<div>';
      var ext = point.do_temi[i].url.split('.')
      if(ext[ext.length - 1] === 'png' || ext[ext.length - 1] === 'jpg') {
        htmlContent += '<img src="' + point.do_temi[i].url + '" alt="' + point.do_temi[i].filename + '">';
      } else {
        htmlContent += '<a href="' + point.do_temi[i].url + '" target="_blank" class="file">' +
            '<span class="file-icon">' + ext[ext.length - 1] + '</span>' +
            '<span>' + point.do_temi[i].filename + '</span>' +
          '</a>';
      }
      htmlContent += '</div>';
    }
    generateSlider(htmlContent);
  });

  $('.slider-container .close-carousel-btn').on('click', function(e) {
    e.preventDefault();
    $('.slider-container').fadeOut(300);
    $('.slider-container .carousel').html('');
    $('.slider-container .carousel').slick('unslick');
  });

  $('.bg-marker-overlay').on('click', function(e) {
    e.preventDefault();
    $('.bg-marker-overlay, .marker-popup, .marker-popup--editable').fadeOut(300, function () {
      $('.markers-popups').html('');
      state.currentMarkerId = undefined;
    });
  });

  $(document).on('click', '.burger-btn', function(e) {
    e.preventDefault();
    $(this).toggleClass('active');
    if($(this).hasClass('active')) {
      $('.user-control-panel').addClass('temp-hide');
    } else {
      $('.user-control-panel').removeClass('temp-hide');
    }
  });

  $('.close-panorama-btn').on('click', function(e) {
    e.preventDefault();
    $('.panorama-container').fadeOut(300);
    $('.panorama-container iframe').attr('src', '');
  });

  $('.close-model-previewer-btn').on('click', function(e) {
    e.preventDefault();
    $('.model-previewer-container').fadeOut(300);
    $('.model-previewer-container iframe').attr('src', '');
  });

  $('.close-pohovannya-previewer-btn').on('click', function(e) {
    e.preventDefault();
    $('.pohovannya-previewer-container').fadeOut(300);
    $('.pohovannya-previewer-container iframe').attr('src', '');
  });

  $('.login-btn').on('click', function(e) {
    e.preventDefault();
    $('.login-window').css('width', '100%');
    $('.login-window').toggleClass('show');
  });
  $('.login-window__bg').on('click', function(e) {
    e.preventDefault();
    $('.login-window').toggleClass('show');
    setTimeout(function () {
      $('.login-window').css('width', '0');
    }, 500);
  });

  $('.main-nav a[data-target]').on('click', function (e) {
    e.preventDefault();
    var target = '.' + $(this).attr('data-target');
    $('.content-popup').fadeOut(300);
    $(target).fadeIn(300);
  });

  $('.content-popup .close-btn').on('click', function(e) {
    e.preventDefault();
    $('.content-popup').fadeOut(300);
  });

  $('.main-content .bg-overlay').on('click', function(e) {
    e.preventDefault();
    if (state.isInformPanelOpened) {
      $('.search-btn, .inform-panel, .inform-panel--ways').removeClass('active');
      $('.main-content').removeClass('inform-panel-is-showing');
      state.isInformPanelOpened = false;
    }
  });

  $('.ways-btn').on('click', function(e) {
    e.preventDefault();
    $(this).toggleClass('active');
    if($(this).hasClass('active')) {
      $('.main-content').addClass('inform-panel-is-showing');
    } else {
      $('.main-content').removeClass('inform-panel-is-showing');
      if (!$('body').hasClass('routes-mode')) {
        $('.current-way-points').fadeOut(300);
      }
      $('.list-of-ways').fadeOut(300);
      $('.sky-overlay').fadeOut(300);
    }
    $('.search-btn').removeClass('active');
    $('.inform-panel--ways').toggleClass('active');
    $('.inform-panel').removeClass('active');
  });

  $('.search-btn').on('click', function(e) {
    e.preventDefault();
    $(this).toggleClass('active');
    if($(this).hasClass('active')) {
      $('.main-content').addClass('inform-panel-is-showing');
    } else {
      $('.main-content').removeClass('inform-panel-is-showing');
    }
    $('.ways-btn').removeClass('active');
    $('.inform-panel').toggleClass('active');
    $('.inform-panel--ways').removeClass('active');
  });

  $('.ways-window__ways ul').on('click', 'li', function(e) {
    e.preventDefault();
    $('.list-of-ways').fadeIn(300);
    $('.sky-overlay').fadeIn(300);
    $('.inform-panel--ways, .inform-panel').removeClass('active');
    $('.main-content').removeClass('inform-panel-is-showing');
    $('.ways-btn').removeClass('active');
  });
  $('.list-of-ways').on('click', function(e) {
    e.preventDefault();
    $('.list-of-ways').fadeOut(300);
    $('.sky-overlay').fadeOut(300);
    $('.main-content').removeClass('inform-panel-is-showing');
    $('.ways-btn').removeClass('active');

  });
  $('#edit-route').on('click', function (e) {
    e.preventDefault();
    var routeId = $(this).attr('data-way-id');
    var cRoute = routes.filter(function (item) {
      return item.wp_id == routeId
    })[0];
    var params = {
      routeId: routeId,
      title: cRoute.title || '',
      description: {
        ua: cRoute.description || '',
        en: (cRoute.descriptionEn || cRoute.description) || ''
      },
      status: cRoute.status,
      photo: cRoute.bg_route || '',
      audio: cRoute.audio.url || '',
      virtual: cRoute.virtual_route,
      real: cRoute.real_route,
      points: cRoute.points.split(',').map(function (item) { return $.trim(item) })
    }
    editRoute(params);
  });
  $('.list-of-ways, .user-routes__list, .favorite-routes__list').on('click', '.way, a', function(e) {
    e.preventDefault();
    var idOfRoute = $(this).attr('data-way-id');
    if ($(this).attr('data-way-id')) {
      $('#add-route-to-favorite').attr('data-way-id', $(this).attr('data-way-id'));
      $('#edit-route').attr('data-way-id', $(this).attr('data-way-id'));
    }
    var pts = $(this).attr('data-points').split(',').map(function (item) {
      return $.trim(item)
    });
    if ($('.favorite-routes__list li a[data-way-id="' + $(this).attr('data-way-id') + '"]').length) {
      $('#add-route-to-favorite').fadeOut();
    } else {
      $('#add-route-to-favorite').fadeIn();
    }
    routeDetails($(this).attr('data-way-id'));
    $('.ways-btn').removeClass('active');
    $('.main-content').removeClass('inform-panel-is-showing');
    createRoute(pts);
  });

  $('.favorite-routes__list').on('click', '.way, a', function(e) {
    e.preventDefault();
    $('#rm-route-from-favorite').attr('data-way-id', $(this).attr('data-way-id'));
    $('#rm-route-from-favorite').fadeIn(300);
  });

  $('.edit-route-popup .add-more-point').on('click', function (e) {
    e.preventDefault();
    addNewPointSelector('.edit-route-popup .routes-list');
  });
  $('.edit-route-popup .close-edit-route-popup').on('click', function (e) {
    e.preventDefault();
    $('.edit-route-popup').fadeOut();
  });
  $('#route-image').on('change', function (e) {
    previewFile($(this), $('.edit-route-popup .photo-preview'), true);
  });
  $('#route-audio').on('change', function (e) {
    var file = $(this).get(0).files[0];
    if(file){
      var reader = new FileReader();
      reader.onload = function() {
        $('.edit-route-popup audio').attr('src', reader.result)
      }
      reader.readAsDataURL(file);
    }
  });

  $('.exit-from-routes-mode-btn').on('click', function(e) {
    e.preventDefault();
    $('body').removeClass('routes-mode');
    map.removeLayer(['route-layer', 'vector-lines']);
    new maptalks.VectorLayer('markersLayer').addTo(map);
    // map.getLayer('markersLayer').show();
    addMarkers('markersLayer', points);
    state.activeLayerId = 'markersLayer';

    $('.current-way-points').fadeOut(300);
    map.fitExtent(bounds, 0, { duration: 1500 });
    $('#rm-route-from-favorite, #add-route-to-favorite, #edit-route').fadeOut();
  });

  $('.current-way-points').off().on('click', 'li', function(e) {
    var target = $(this);
    $('.current-way-points li').removeClass('active');
    $(this).addClass('active');
    changeView(map.getCenter(), [$(target).attr('data-lng'), $(target).attr('data-lat')], $(this).attr('data-popup-id'));
  });

  $('.search-form input').autocomplete({
    source: state.tags
  });

  $(document).on('click', '.ui-menu-item', function(e) {
    $('.search-form input').blur();
  });

  $('.filter-form, .search-form, .search-form input').on('change keyup submit', function(e) {
    e.preventDefault();
    var filterFormSerialize = $('.filter-form').serializeArray();
    var searchFormSerialize = $('.search-form').serializeArray();
    var filteredPoints = points.filter(function (item) {
      var res = true;
      if (filterFormSerialize.length) {
        for (var i = 0; i < filterFormSerialize.length; i++) {
          if (item.categories.split(',').indexOf(filterFormSerialize[i].name) > -1) res = true
          else {
            res = false;
            break;
          }
        }
      }
      if (searchFormSerialize[0].value && res) {
        if (item.tags.split(',').map(function (item) { return item.trim() }).indexOf(searchFormSerialize[0].value) > -1) res = true
        else res = false
      }
      return res
    }).map(function(item) {
      return item.id_wp
    });
    filterMarkers (filteredPoints);
  });

  $('.filter-3d-btn').on('click', function (e) {
    e.preventDefault();
    $(this).toggleClass('active');
    var _this = $(this)
    var filteredPoints = points.filter(function (item) {
      return _this.hasClass('active') ? item.pohovannya.length > 0 : true;
    }).map(function (item) {
      return item.id_wp
    })
    filterMarkers (filteredPoints);
  })

  $('.purpose-route-form').on('change', 'select', function(e) {
    addedPoints[$(this).parent().index()] = e.target.value;
    if (addedPoints.length) $('.add-more-point').css('display', 'block');
  });
  $('.add-more-point').on('click', function (e) {
    e.preventDefault();
    addNewPointSelector('.purpose-route-form .fields .selects');
  });
  $('.purpose-route-form .selects, .edit-route-popup .routes-list').on('click', '.remove-point-btn', function(e) {
    e.preventDefault();
    var i = $(this).parent().index();
    if (addedPoints[i]) {
      addedPoints.splice(i, 1);
    }
    $(this).parent().remove();
  });
  $('.purpose-route-form').on('submit', function(e) {
    // e.preventDefault();
    if (!$(this).find('input[type="text"]').val() || $(this).find('input[type="text"]').val().length < 3) {
      showInformationWindow('Введіть назву для маршруту. Не менше трьох символів');
      return false;
    }
    if (addedPoints.length <= 1) {
      showInformationWindow('Маршрут може складатись лише з двох або більше точок');
      return false;
    }
    for (var i = 0; i < addedPoints.length - 1; i++) {
      if (addedPoints[i] === addedPoints[i + 1]) {
        showInformationWindow('Маршрут не може складатись з двох однакових точок підряд');
        return false;
      }
    }
  });

  $('.inform-window a').on('click', function(e) {
    e.preventDefault();
    $('.inform-window').fadeOut(300);
  });

  $('.create-object-btn').on('click', function (e) {
    e.preventDefault();
    $('body').addClass('setting-dot-mode');
    showInformationWindow('Оберіть точку на карті клікнувши на потрібне місце');
  });

  map.on('click', function (e) {
    if ($('body').hasClass('setting-dot-mode')) {
      state.gLng = e.coordinate.x;
      state.gLat = e.coordinate.y;
      $('.marker-popup--editable input[name="id-of-marker"]').remove();
      $('.marker-popup--editable input[name="lng"]').val(state.gLng);
      $('.marker-popup--editable input[name="lat"]').val(state.gLat);
      var editPopup = $('.marker-popup--editable');
      // title and subtitle
      editPopup.find('.ua .title-field').val('');
      editPopup.find('.en .title-field').val('');
      editPopup.find('.ua .subtitle-field').val('');
      editPopup.find('.en .subtitle-field').val('');
      // Lat and lng
      editPopup.find('input[name="lat"]').val(state.gLat);
      editPopup.find('input[name="lng"]').val(state.gLng);
      // Description
      editPopup.find('.marker-popup__description .photo img').attr('src', "#");
      editPopup.find('.marker-popup__description textarea[name="description-ua"]').val('');
      editPopup.find('.marker-popup__description textarea[name="description-en"]').val('');
      // Monument protection information
      editPopup.find('.marker-popup__information__description .photo img').attr('src', '#');
      editPopup.find('textarea[name="monument-information-description-ua"]').val('');
      editPopup.find('textarea[name="monument-information-description-en"]').val('');
      // Publications
      editPopup.find('.marker-popup__publications__list .publication').remove();
      for (var i = 0; i < 1; i++) {
        editPopup.find('.marker-popup__publications__list').append(
          '<div class="publication">' +
          '<a href="#" class="remove-publication"></a>' +
          '<div class="publication__thumbnail photo">' +
          '<img src="#" alt="photo">' +
          '<div>' +
          '<label for="publication-' + (i + 1) + '-photo">Завантажити фото</label>' +
          '<input type="file" id="publication-' + (i + 1) + '-photo" class="publication-photo" name="publication-' + (i + 1) + '-photo"' +
          'accept="image/*">' +
          '</div>' +
          '<div>' +
          '<label for="publication-' + (i + 1) + '-file">Завантажити файл в форматі .pdf</label>' +
          '<input type="file" id="publication-' + (i + 1) + '-file" class="publication-file" name="publication-' + (i + 1) + '-file"' +
          'accept=".pdf">' +
          '</div>' +
          '</div>' +
          '<div class="publication__description ua">' +
          '<textarea name="publication-' + (i + 1) + '-description-ua" placeholder="Опис..."></textarea>' +
          '</div>' +
          '<div class="publication__description en">' +
          '<textarea name="publication-' + (i + 1) + '-description-en" placeholder="Description..."></textarea>' +
          '</div>' +
          '</div>'
        );
      }
      // Photo gallery
      editPopup.find('.upload-gallery-photo-item').remove();
      for (var i = 0; i < 1; i++) {
        var ins =
          '<div class="upload-gallery-photo-item">' +
          '<a href="#" class="remove-gallery-photo"></a>' +
          '<label for="gallery-photo-' + (i + 1) + '"></label>' +
          '<input type="file" id="gallery-photo-' + (i + 1) + '" name="gallery-photo-' + (i + 1) + '" class="upload-gallery-photo" accept="image/*">' +
          '</div>';
        $(ins).insertBefore('.marker-popup--editable .gallery__photos .add-gallery-photo');
      }
      // Videos
      editPopup.find('.link-to-video-item').remove();
      for (var i = 0; i < 1; i++) {
        var ins =
          '<div class="link-to-video-item">' +
          '<a href="#" class="remove-link-to-video"></a>' +
          '<input type="text" id="link-to-video-' + (i + 1) + '" name="link-to-video-' + (i + 1) + '" class="link-to-video"' +
          'placeholder="Посилання на відео з youtube">' +
          '</div>';
        $(ins).insertBefore('.marker-popup--editable .video__list .add-link-to-video');
      }
      // To the topic
      editPopup.find('.other-resources-upload-item').remove();
      for (var i = 0; i < 1; i++) {
        var ins =
          '<div class="other-resources-upload-item">' +
          '<a href="#" class="remove-other-source"></a>' +
          '<label for="other-resource-' + (i + 1) + '"></label>' +
          '<input type="file" id="other-resource-' + (i + 1) + '" name="other-resource-' + (i + 1) + '" class="other-source-image-uploader"' +
          'accept="image/*">' +
          '</div>';
        $(ins).insertBefore('.marker-popup--editable .other-resources__sources .add-other-source');
      }
      $('body').removeClass('setting-dot-mode');
      editPopup.fadeIn(300);
      $('.bg-marker-overlay').fadeIn(300);
    }
  });
  map.on('moveend zoomend pitchend', function () {
    changeZIndexes();
  })

  $('.marker-popup--editable .en-btn').on('click', function(e) {
    e.preventDefault();
    $('.marker-popup--editable').addClass('en');
    $('.marker-popup--editable').removeClass('ua');
  });
  $('.marker-popup--editable .ua-btn').on('click', function(e) {
    e.preventDefault();
    $('.marker-popup--editable').addClass('ua');
    $('.marker-popup--editable').removeClass('en');
  });
  $('.marker-popup--editable .listen-btn').on('click', function(e) {
    e.preventDefault();
    $('.marker-popup--editable .btn-3d, .marker-popup--editable .btn-panorama').removeClass('active');
    $(this).toggleClass('active');
    $('.upload-popup').fadeOut(300);
    if ($(this).hasClass('active')) {
      $('.upload-popup--audio').fadeIn(300);
    } else {
      $('.upload-popup--audio').fadeOut(300);
    }
  });
  $('.marker-popup--editable').on('click', '.remove-gallery-photo', function (e) {
    e.preventDefault();
    $(this).parent().remove();
  });
  $('.marker-popup--editable .add-other-source').on('click', function(e) {
    e.preventDefault();
    var countOfSourcesItems = $('.marker-popup--editable .other-resources-upload-item').length;
    var template = '<div class="other-resources-upload-item">' +
        '<a href="#" class="remove-other-source"></a>' +
        '<label for="other-resource-' + (countOfSourcesItems + 1) + '">Додати файл</label>' +
        '<input type="file" id="other-resource-' + (countOfSourcesItems + 1) + '" name="other-resource-' + (countOfSourcesItems + 1) + '" class="other-source-image-uploader">' +
      '</div>'
    $(template).insertBefore('.marker-popup--editable .other-resources__sources .add-other-source');
  });
  $('.marker-popup--editable').on('click', '.remove-other-source', function (e) {
    e.preventDefault();
    $(this).parent().remove();
  });
  $('.marker-popup--editable').on('click', '.remove-link-to-video', function (e) {
    e.preventDefault();
    $(this).parent().remove();
  });
  $('.marker-popup--editable .add-gallery-photo').on('click', function(e) {
    e.preventDefault();
    var countOfPhotoItems = $('.marker-popup--editable .upload-gallery-photo-item').length;
    var template = '<div class="upload-gallery-photo-item">' +
      '<a href="#" class="remove-gallery-photo"></a>' +
      '<label for="gallery-photo-' + (countOfPhotoItems + 1) + '"></label>' +
      '<input type="file" id="gallery-photo-' + (countOfPhotoItems + 1) + '" name="gallery-photo-' + (countOfPhotoItems + 1) + '" class="upload-gallery-photo" accept="image/*">' +
      '</div>';
    $(template).insertBefore('.marker-popup--editable .gallery__photos .add-gallery-photo');
  });
  $('.marker-popup--editable .add-link-to-video').on('click', function(e) {
    e.preventDefault();
    var countOfPhotoItems = $('.marker-popup--editable .link-to-video-item').length;
    var template =
      '<div class="link-to-video-item">' +
      '<a href="#" class="remove-link-to-video"></a>' +
      '<input type="text" id="link-to-video-' + (countOfPhotoItems + 1) + '" name="link-to-video-' + (countOfPhotoItems + 1) + '" class="link-to-video"' +
      'placeholder="Посилання на відео з youtube">' +
      '</div>';
    $(template).insertBefore('.marker-popup--editable .add-link-to-video');
  });
  $('.add-more-publication').on('click', function(e) {
    e.preventDefault();
    var lastIdOfPublication = $(this).parent().find('.publication').length;
    var newIdOfPublication = lastIdOfPublication + 1;
    var newPublicationFields = $(this).parent().find('.publication').eq(lastIdOfPublication - 1).clone(true);
    newPublicationFields.find('.publication-photo').val('');
    newPublicationFields.find('.publication-file').val('');
    newPublicationFields.find('.publication__thumbnail img').attr('src', '#');
    newPublicationFields.find('label[for="publication-' + lastIdOfPublication + '-photo"]')
      .attr('for', 'publication-' + newIdOfPublication + '-photo"]');
    newPublicationFields.find('#publication-' + lastIdOfPublication + '-photo')
      .attr({
        'id': 'publication-' + newIdOfPublication + '-photo',
        'name': 'publication-' + newIdOfPublication + '-photo'
      });
    newPublicationFields.find('label[for="publication-' + lastIdOfPublication + '-file"]')
      .attr('for', 'publication-' + newIdOfPublication + '-file');
    newPublicationFields.find('#publication-' + lastIdOfPublication + '-file')
      .attr({
        'id': 'publication-' + newIdOfPublication + '-file',
        'name': 'publication-' + newIdOfPublication + '-file'
      });
    newPublicationFields.find('textarea[name="publication-' + lastIdOfPublication + '-description-ua"]')
      .attr('name', 'publication-' + newIdOfPublication + '-description-ua');
    newPublicationFields.find('textarea[name="publication-' + lastIdOfPublication + '-description-en"]')
      .attr('name', 'publication-' + newIdOfPublication + '-description-en');
    if (newIdOfPublication === 2) $(newPublicationFields).prepend('<a href="#" class="remove-publication"></a>');
    $(this).parent().append(newPublicationFields[0].outerHTML);
  });
  $('.marker-popup--editable').on('click', '.remove-publication', function(e) {
    e.preventDefault();
    $(this).parent().remove();
  });
  $('.marker-popup--editable .btn-3d').on('click', function(e) {
    e.preventDefault();
    $('.marker-popup--editable .listen-btn, .marker-popup--editable .btn-panorama').removeClass('active');
    $(this).toggleClass('active');
    $('.upload-popup').fadeOut(300);
    if ($(this).hasClass('active')) {
      $('.upload-popup--3dmodel').fadeIn(300);
    } else {
      $('.upload-popup--3dmodel').fadeOut(300);
    }
  });
  $('.marker-popup--editable .btn-panorama').on('click', function(e) {
    e.preventDefault();
    $('.marker-popup--editable .btn-3d, .marker-popup--editable .listen-btn').removeClass('active');
    $(this).toggleClass('active');
    $('.upload-popup').fadeOut(300);
    if ($(this).hasClass('active')) {
      $('.upload-popup--panorama-photo').fadeIn(300);
    } else {
      $('.upload-popup--panorama-photo').fadeOut(300);
    }
  });
  $('.close-add-point-popup').on('click', function(e) {
    e.preventDefault();
    $('.marker-popup--editable').fadeOut(300);
    $('.bg-marker-overlay').fadeOut(300);
  });
  $('.marker-popup--editable .marker-popup__description #main-thumbnail').on('change', function (e) {
    previewFile($(this), $('.marker-popup--editable .marker-popup__description img'));
  });
  $('.marker-popup--editable').on('change', '.other-source-image-uploader', function (e) {
    var file = $(this).get(0).files[0];
    if (file) {
      $(this).parent().find('label').text(file.name)
    } else {
      $(this).parent().find('label').text('Додати файл')
    }
  });
  $('.marker-popup--editable #monument-information-thumbnail').on('change', function (e) {
    previewFile($(this), $('.marker-popup--editable .marker-popup__information__description img'));
  });
  $('.marker-popup--editable .marker-popup__publications__list').on('change', '.publication-photo', function (e) {
    previewFile($(this), $(this).parent().parent().find('img'));
  });
  $('.marker-popup--editable .gallery__photos').on('change', '.upload-gallery-photo', function (e) {
    previewFile($(this), $(this).parent().find('label'), true);
  });

  $('.route-details .close').on('click', function (e) {
    e.preventDefault();
    $('.route-details').fadeOut();
  });
  $('.route-details .lang-btn').on('click', function (e) {
    e.preventDefault();
    if ($(this).hasClass('lang-btn--ua')) {
      $('.route-details').removeClass('en').addClass('ua')
    }
    if ($(this).hasClass('lang-btn--en')) {
      $('.route-details').removeClass('ua').addClass('en')
    }
  });

  $('.global-audio .close').on('click', function (e) {
    e.preventDefault();
    $('.global-audio audio').attr('src', '');
    $('.global-audio').fadeOut();
  });

  $(document).on('click', '.edit-point-btn', function(e) {
    e.preventDefault();
    var id = state.currentMarkerId;
    var editPopup = $('.marker-popup--editable');
    $(document).find('.marker-popup').fadeOut();
    var point = points.filter(function (item) { return item.id_wp == id} )[0]
    var data = {
      id: point.id_wp,
      title: {
        ua: point.title || ''
      },
      subtitle: {
        ua: point.subtitle || ''
      },
      lat: point.lang,
      lng: point.lat,
      description: {
        photoUrl: point.main_img || '',
        description: {
          ua: $(point.description).text() || '',
        }
      },
      monumentProtectionInformation: {
        photoUrl: point.image_security || '',
        documentUrl: point.file_security.url || '',
        description: {
          ua: $(point.description_security).text() || ''
        }
      },
      publications: point.scientist_articles.map(function (item) {
        return {
          publicationPhoto: item.image || '',
          publicationDescription: {
            ua: $(item.text).text()
          },
          files: {
            ua: item.file_url || ''
          }
        }
      }),
      galleryPhotos: point.gallery.images.map(function (item) {
        return item
      }),
      videos: point.gallery.videos.map(function (item) {
        return item
      }),
      otherResources: point.do_temi
    };

    $('.bg-marker-overlay').fadeOut(300);
    // id of editable marker
    $('.marker-popup--editable input[name="id-of-marker"]').remove();
    editPopup.find('form').append('<input type="text" name="id-of-marker" value="' + data.id + '" hidden="true"/>')
    // title and subtitle
    editPopup.find('.ua .title-field').val(data.title.ua);
    editPopup.find('.ua .subtitle-field').val(data.subtitle.ua);
    // Lat and lng
    editPopup.find('input[name="lat"]').val(data.lat);
    editPopup.find('input[name="lng"]').val(data.lng);
    // Description
    editPopup.find('.marker-popup__description .photo img').attr('src', data.description.photoUrl);
    editPopup.find('.marker-popup__description textarea[name="description-ua"]').val(data.description.description.ua);
    // Monument protection information
    editPopup.find('.marker-popup__information__description .photo img').attr('src', data.monumentProtectionInformation.photoUrl);
    editPopup.find('textarea[name="monument-information-description-ua"]').val(data.monumentProtectionInformation.description.ua);
    // Publications
    editPopup.find('.marker-popup__publications__list .publication').remove();
    for (var i = 0; i < data.publications.length; i++) {
      editPopup.find('.marker-popup__publications__list').append(
        '<div class="publication">' +
        '<a href="#" class="remove-publication"></a>' +
        '<div class="publication__thumbnail photo">' +
        '<img src="' + (data.publications[i].publicationPhoto || '#') + '" alt="photo">' +
        '<div>' +
        '<label for="publication-' + (i + 1) + '-photo">Завантажити фото</label>' +
        '<input type="file" id="publication-' + (i + 1) + '-photo" class="publication-photo" name="publication-' + (i + 1) + '-photo"' +
        'accept="image/!*">' +
        '</div>' +
        '<div>' +
        '<label for="publication-' + (i + 1) + '-file">Завантажити файл в форматі .pdf</label>' +
        '<input type="file" id="publication-' + (i + 1) + '-file" class="publication-file" name="publication-' + (i + 1) + '-file"' +
        'accept=".pdf">' +
        '</div>' +
        '</div>' +
        '<div class="publication__description ua">' +
        '<textarea name="publication-' + (i + 1) + '-description-ua" placeholder="Опис...">' +
        data.publications[i].publicationDescription.ua +
        '</textarea>' +
        '</div>' +
        '</div>' +
        '</div>'
      );
    }
    // Photo gallery
    editPopup.find('.upload-gallery-photo-item').remove();
    for (var i = 0; i < data.galleryPhotos.length; i++) {
      var imgUrl = data.galleryPhotos[i];
      var ins =
        '<div class="upload-gallery-photo-item">' +
        '<a href="#" class="remove-gallery-photo"></a>' +
        '<label for="gallery-photo-' + (i + 1) + '" style="background-image: url(' + imgUrl.url + ')"></label>' +
        '<input type="file" id="gallery-photo-' + (i + 1) + '" name="gallery-photo-' + (i + 1) + '" class="upload-gallery-photo" accept="image/!*">' +
        '</div>';
      $(ins).insertBefore('.marker-popup--editable .gallery__photos .add-gallery-photo');
    }
    // Videos
    editPopup.find('.link-to-video-item').remove();
    for (var i = 0; i < data.videos.length; i++) {
      var videoUrl = data.videos[i];
      var ins =
        '<div class="link-to-video-item">' +
        '<a href="#" class="remove-link-to-video"></a>' +
        '<input type="text" id="link-to-video-' + (i + 1) + '" name="link-to-video-' + (i + 1) + '" class="link-to-video"' +
        'placeholder="Посилання на відео з youtube" value="' + videoUrl + '">' +
        '</div>';
      $(ins).insertBefore('.marker-popup--editable .video__list .add-link-to-video');
    }
    // To the topic
    editPopup.find('.other-resources-upload-item').remove();
    for (var i = 0; i < data.otherResources.length; i++) {
      var source = data.otherResources[i];
      var ins =
        '<div class="other-resources-upload-item">' +
          '<a href="#" class="remove-other-source"></a>' +
          '<label for="other-resource-' + (i + 1) + '">' + source.filename + '</label>' +
          '<input type="file" id="other-resource' + (i + 1) + '" name="other-resource-' + (i + 1) + '" class="other-source-image-uploader">' +
        '</div>';
      $(ins).insertBefore('.marker-popup--editable .other-resources__sources .add-other-source');
    }
    // Show editable popup
    editPopup.fadeIn(300);
  })

  addSimpleBar()
}

function addSimpleBar () {
  var conf = {
    autoHide: false,
    scrollbarMaxSize: 60
  }
  $('.filters').each(function (index, el) {
    new SimpleBar(el, conf);
  });
  $('.list-of-ways .ways-container').each(function (index, el) {
    new SimpleBar(el, conf);
  });
  setTimeout(function () {
    $('.b-filter__list').each(function (index, el) {
      new SimpleBar(el, conf);
    });
    $('.user-control-panel').each(function (index, el) {
      new SimpleBar(el, conf);
    });
  }, 500);
}

function editRoute (params) {
  $('#route-id-field').val(params.routeId);
  $('#route-title-field').val(params.title);
  $('#route-description-field-ua').val(params.description.ua);
  $('#route-description-field-en').val(params.description.en);
  $('.edit-route-popup .photo-preview').css({
    'background-image': 'url(' + params.photo + ')'
  }).addClass('show-photo');
  $('.edit-route-popup .route-audio').attr('src', params.audio);
  $('#virtual-route').prop('checked', params.virtual);
  $('#real-route').prop('checked', params.real);
  $('#made-public').prop('checked', params.status != 'private');
  var selectedRoutes = renderRoutesInEditRoutePopup(params.points);
  $('.edit-route-popup .routes-list').html('')
  for (var i = 0; i < selectedRoutes.length; i++) {
    addNewPointSelector('.edit-route-popup .routes-list', selectedRoutes[i].id)
  }
  $('.edit-route-popup').fadeIn(300);
}

function renderRoutesInEditRoutePopup (routesIds) {
  var res = []
  for (var i = 0; i < routesIds.length; i++) {
    for (var j = 0; j < points.length; j++) {
      if (routesIds[i] === 'marker-wordpress-id-' + points[j].id_wp) {
        res.push({
          title: points[j].title,
          id: points[j].id_wp
        })
        break;
      }
    }
  }
  return res
}

function filterMarkers (markersToShow) {
  console.log(markersToShow)
  var allMarkers = map.getLayer(state.activeLayerId).getGeometries()
  var xMin = allMarkers[0].getCenter().x;
  var xMax = allMarkers[0].getCenter().x;
  var yMin = allMarkers[0].getCenter().y;
  var yMax = allMarkers[0].getCenter().y;
  for (var i = 0; i < allMarkers.length; i++) {
    allMarkers[i].hide();
    var currentId = +allMarkers[i].getId().split('_')[0];
    if (markersToShow.indexOf(currentId) > -1) {
      allMarkers[i].show();
      if (allMarkers[i].getCenter().x < xMin) xMin = allMarkers[i].getCenter().x
      if (allMarkers[i].getCenter().y < yMin) yMin = allMarkers[i].getCenter().y
      if (allMarkers[i].getCenter().x > xMax) xMax = allMarkers[i].getCenter().x
      if (allMarkers[i].getCenter().y > yMax) yMax = allMarkers[i].getCenter().y
    }
  }
  var extent = new maptalks.Extent({xmin : xMin, ymin: yMin, xmax: xMax, ymax: yMax});
  map.animateTo({
    center: extent.getCenter(),
    zoom: 9,
    pitch: 10,
    bearing: Math.random() * 90
  }, {
    duration: 3000
  });
  /*map.fitExtent(extent, 0, { duration: 1000 });
  map.setPitch(0, 1000);*/
}

function changeView(from, to, popupId) {
  map.animateTo({
    center: from,
    zoom: 12,
    pitch: 0,
  }, {
    duration: 3000
  });
  setTimeout(function () {
    map.animateTo({
      center: to,
      zoom: 15,
      pitch: 60,
      bearing: Math.random() * 90
    }, {
      duration: 6000
    });
  }, 3000);
  setTimeout(function () {
    state.currentMarkerId = popupId;
    var _m = map.getLayer(state.activeLayerId).getGeometryById(popupId)
    createPopup(points.filter(function (item) {
      return item.id_wp == popupId
    })[0]);
    miniMap(_m)
  }, 9000);
}

function createRoute (pts) {
  var pointsForLines = [];
  map.removeLayer(['route-layer', 'vector-lines', 'markersLayer']);
  new maptalks.VectorLayer('route-layer').addTo(map);
  new maptalks.VectorLayer('vector-lines', { enableAltitude : true, zIndex: 5000 }).addTo(map);
  $('body').addClass('routes-mode');
  $('.current-way-points').fadeIn(300);
  var dots = [];
  var items = '';
  for (var i = 0; i < pts.length; i++) {
    var markerId;
    var dot = points.filter(function (item, index) {
      if ('marker-wordpress-id-' + item.id_wp === pts[i]) {
        markerId = item.id_wp;
        return true
      }
    })[0]
    dots.push(dot);
    items += '<li data-popup-id="' + markerId + '" data-lat="' + dot.lang + '" data-lng="' + dot.lat + '">' + dot.title + '</li>';
    pointsForLines.push([+dot.lat, +dot.lang]);
  }
  $('.current-way-points ul').html(items);
  addMarkers('route-layer', dots);
  var wayExtentPolygon = new maptalks.Polygon(pointsForLines);
  map.fitExtent(wayExtentPolygon.getExtent(), 0, { duration: 3000 });

  for (var i = 0; i < pointsForLines.length - 1; i++) {
    var lineCoord = splitLine(2,
      +pointsForLines[i][1],
      +pointsForLines[i + 1][1],
      +pointsForLines[i][0],
      +pointsForLines[i + 1][0]);
    var line = new maptalks.LineString([
      lineCoord[0],
      lineCoord[1],
      lineCoord[2]
    ], {
      smoothness : 1,
      symbol: {
        'lineColor' : '#f97e14',
        'lineWidth' : 3,
        'lineDasharray' : [30, 10]
      },
      properties : {
        'altitude' : [0, 5000, 0]
      }
    });
    line.addTo(map.getLayer('vector-lines'))
  }
}

function splitLine(n, latStart, latEnd, lngStart, lngEnd) {
  var res = [];
  res.push([+lngStart, +latStart]);
  var lngLen = lngEnd - lngStart;
  var latLen = latEnd - latStart;

  var hlen = Math.sqrt(Math.pow(lngLen,2) + Math.pow(latLen,2));

  for (var i = n; i > 0; i--) {
    var smallerLen = hlen / i;

    var ratio = smallerLen / hlen;

    var smallerXLen = lngLen * ratio;
    var smallerYLen = latLen * ratio;

    var smallerX = lngStart + smallerXLen;

    var smallerY = latStart + smallerYLen;
    res.push([smallerX * 1, smallerY * 1]);
  }
  return res;
}

function addAvailablePoints () {
  var _points = points.filter(function(item) {
    if (state.cUserId) {
      if (item.status_point == 'private') {
        if (item.created_by == state.cUserId) return true
      } else {
        return true
      }
    } else {
      if (item.status_point == 'private') return false
      else return true
    }
  }).sort(function (a, b) {
    var nA = a.title.toUpperCase();
    var nB = b.title.toUpperCase();
    if (nA < nB) {
      return -1;
    }
    if (nA > nB) {
      return 1;
    }
    return 0;
  });
  for (var i = 0; i < _points.length; i++) {
    var option = document.createElement('option');
    option.value = _points[i].id_wp;
    option.innerText = _points[i].title;
    $('.purpose-route-form .fields select').append($(option));
  }
}

function addNewPointSelector (appendSelector, value) {
  var newSelectId = $(appendSelector).find('div').length + 1;
  var selectContainer = document.createElement('div');
  var removePointBtn = document.createElement('a');
  var select = document.createElement('select');
  var _points = points.filter(function(item) {
    if (state.cUserId) {
      if (item.status_point == 'private') {
        if (item.created_by == state.cUserId) return true
      } else {
        return true
      }
    } else {
      if (item.status_point == 'private') return false
      else return true
    }
  }).sort(function (a, b) {
    var nA = a.title.toUpperCase();
    var nB = b.title.toUpperCase();
    if (nA < nB) {
      return -1;
    }
    if (nA > nB) {
      return 1;
    }
    return 0;
  })
  removePointBtn.href = "#";
  removePointBtn.classList.add('remove-point-btn');
  select.name = 'route-coordinate-' + newSelectId;
  if (value) {
    select.value = value;
  }
  for (var i = 0; i < _points.length; i++) {
    var option = document.createElement('option');
    option.value = _points[i].id_wp;
    option.innerText = _points[i].title;
    if (value && _points[i].id_wp == value) {
      option.selected = true
    }
    select.appendChild(option);
  }
  selectContainer.appendChild(select);
  selectContainer.appendChild(removePointBtn);
  $(appendSelector).append(selectContainer);
  addedPoints[newSelectId - 1] = select.value;
}

function showInformationWindow(text) {
  $('.inform-window').fadeOut(300);
  $('.inform-window h4').text(text);
  $('.inform-window').fadeIn(300);
}

function previewFile(input, imgEl, asBackgroundImage) {
  var file = input.get(0).files[0];
  if(file){
    var reader = new FileReader();
    reader.onload = function() {
      if (asBackgroundImage) {
        imgEl.css('background-image', 'url(' + reader.result + ')')
      } else {
        imgEl.attr("src", reader.result);
      }
    }
    reader.readAsDataURL(file);
  }
}

function miniMap (marker) {
  var json = {
    type: "FeatureCollection",
    features: []
  }
  var coords = [];
  var g = [];
  if (marker) {
    g.push(marker);
  } else {
    g = map.getLayer(state.activeLayerId).getGeometries()
  }
  for (var i = 0; i < g.length; i++) {
    json.features.push(g[i].toGeoJSON())
  }
  var minimapContainer = $(document).find('.mini-map')[0];
  var minimap = new maptalks.Map(minimapContainer, {
    center: bounds.getCenter(),
    zoom: 6.7,
    draggable: false,
    zoomControl : false,
    attribution: false,
    maxPitch: 0,
    maxVisualPitch: 0
  });
  new maptalks.VectorLayer('minimap-regions', { zIndex: 1 }).addTo(minimap);
  new maptalks.VectorLayer('minimap-markers', { zIndex: 2 }).addTo(minimap);
  maptalks.GeoJSON.toGeometry(json, function (geometry) {
    geometry.setSymbol({
      markerFile: markerIcons['minimap'],
      markerWidth: '24',
      markerHeight: '24'
    })
    geometry.addTo(minimap.getLayer('minimap-markers'))
    coords.push(geometry.getCenter());
  });
  drawRegionsLines(minimap, 'minimap-regions', 'rgba(255, 255, 255, .5)', 1, coords);
}
