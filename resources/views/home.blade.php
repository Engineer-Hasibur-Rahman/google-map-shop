@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="content-box content-single">
                    

                    <div class="pac-card" id="pac-card">
                        <div>
                          <div id="title">Autocomplete search</div>
                          <div id="type-selector" class="pac-controls">
                            <input
                              type="radio"
                              name="type"
                              id="changetype-all"
                              checked="checked"
                            />
                            <label for="changetype-all">All</label>     
                          </div>   
                        </div>
                        <div id="pac-container">
                          <input id="pac-input" type="text" placeholder="Enter a location" />
                        </div>
                      </div>                

                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="content-box content-single">   
                      <div id="map-canvas" style="height: 600px; width: 100%; position: relative; overflow: hidden;"></div>
                      <div id="infowindow-content">
                        <span id="place-name" class="title"></span><br />
                        <span id="place-address"></span>
                      </div>                  

                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script type='text/javascript' src='https://maps.google.com/maps/api/js?language=en&key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&region=GB'></script>
    <script defer>
       
       function initMap() {
    var mapOptions = {
        zoom: 12,
        minZoom: 6,
        maxZoom: 20,
        zoomControl: true,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.DEFAULT
        },
        center: new google.maps.LatLng({{ $latitude }}, {{ $longitude }}),
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        scrollwheel: true,
        panControl: true,
        mapTypeControl: true,
        scaleControl: true,
        overviewMapControl: true,
        rotateControl: true
    };

    var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
    var image = new google.maps.MarkerImage("assets/images/pin.png", null, null, null, new google.maps.Size(40, 52));
    var places = @json($mapShops);
    var min = .999999;
    var max = 1.000001;

     //new add start
     const card = document.getElementById("pac-card");
    const input = document.getElementById("pac-input");
    const biasInputElement = document.getElementById("use-location-bias");
    const strictBoundsInputElement = document.getElementById("use-strict-bounds");
    const options = {
      fields: ["formatted_address", "geometry", "name"],
      strictBounds: false,
      types: ["establishment"],
    };   
    map.controls[google.maps.ControlPosition.TOP_LEFT].push(card);
    const autocomplete = new google.maps.places.Autocomplete(input, options);
    autocomplete.bindTo("bounds", map);
    //new add end
   


    

    for (var i = 0; i < places.length; i++) {
        var place = places[i];
        if (place.latitude && place.longitude) {
            
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(place.latitude * (Math.random() * (max - min) + min), place.longitude * (Math.random() * (max - min) + min)),
                icon: image,
                map: map,
                title: place.name,
                anchorPoint: new google.maps.Point(0, -29),
            });        

            var infowindow = new google.maps.InfoWindow();
            google.maps.event.addListener(marker, 'click', (function (marker, place) {
                return function () {
                    infowindow.setContent(generateContent(place));
                    infowindow.open(map, marker);
                };
            })(marker, place));
        }
    }


    // new add start
    autocomplete.addListener("place_changed", () => {
      infowindow.close();
      marker.setVisible(false);
  
      const place = autocomplete.getPlace();
  
      if (!place.geometry || !place.geometry.location) {
        // User entered the name of a Place that was not suggested and
        // pressed the Enter key, or the Place Details request failed.
        window.alert("No details available for input: '" + place.name + "'");
        return;
      }
  
      // If the place has a geometry, then present it on a map.
      if (place.geometry.viewport) {
        map.fitBounds(place.geometry.viewport);
      } else {
        map.setCenter(place.geometry.location);
        map.setZoom(17);
      }
  
      marker.setPosition(place.geometry.location);
      marker.setVisible(true);
      infowindowContent.children["place-name"].textContent = place.name;
      infowindowContent.children["place-address"].textContent =
        place.formatted_address;
      infowindow.open(map, marker);
    });


    // Sets a listener on a radio button to change the filter type on Places
    // Autocomplete.
    function setupClickListener(id, types) {
      const radioButton = document.getElementById(id);
  
      radioButton.addEventListener("click", () => {
        autocomplete.setTypes(types);
        input.value = "";
      });
    }
  
    setupClickListener("changetype-all", []);
    setupClickListener("changetype-address", ["address"]);
    setupClickListener("changetype-establishment", ["establishment"]);
    setupClickListener("changetype-geocode", ["geocode"]);
    setupClickListener("changetype-cities", ["(cities)"]);
    setupClickListener("changetype-regions", ["(regions)"]);
    biasInputElement.addEventListener("change", () => {
      if (biasInputElement.checked) {
        autocomplete.bindTo("bounds", map);
      } else {
        // User wants to turn off location bias, so three things need to happen:
        // 1. Unbind from map
        // 2. Reset the bounds to whole world
        // 3. Uncheck the strict bounds checkbox UI (which also disables strict bounds)
        autocomplete.unbind("bounds");
        autocomplete.setBounds({ east: 180, west: -180, north: 90, south: -90 });
        strictBoundsInputElement.checked = biasInputElement.checked;
      }
  
      input.value = "";
    });
    strictBoundsInputElement.addEventListener("change", () => {
      autocomplete.setOptions({
        strictBounds: strictBoundsInputElement.checked,
      });
      if (strictBoundsInputElement.checked) {
        biasInputElement.checked = strictBoundsInputElement.checked;
        autocomplete.bindTo("bounds", map);
      }
  
      input.value = "";
    });
    // new add end

}


google.maps.event.addDomListener(window, 'load', initMap);


function generateContent(place) {
    {
            var content = `
            <div class="gd-bubble" style="">
                <div class="gd-bubble-inside">
                    <div class="geodir-bubble_desc">
                    <div class="geodir-bubble_image">
                        <div class="geodir-post-slider">
                            <div class="geodir-image-container geodir-image-sizes-medium_large ">
                                <div id="geodir_images_5de53f2a45254_189" class="geodir-image-wrapper" data-controlnav="1">
                                    <ul class="geodir-post-image geodir-images clearfix">
                                        <li>
                                            <div class="geodir-post-title">
                                                <h4 class="geodir-entry-title">
                                                    <a href="{{ route('shop', '') }}/`+place.id+`" title="View: `+place.name+`">`+place.name+`</a>
                                                </h4>
                                            </div>
                                            <a href="{{ route('shop', '') }}/`+place.id+`"><img src="`+place.thumbnail+`" alt="`+place.name+`" class="align size-medium_large" width="1400" height="930"></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="geodir-bubble-meta-side">
                    <div class="geodir-output-location">
                    <div class="geodir-output-location geodir-output-location-mapbubble">
                        <div class="geodir_post_meta  geodir-field-post_title"><span class="geodir_post_meta_icon geodir-i-text">
                            <i class="fas fa-minus" aria-hidden="true"></i>
                            <span class="geodir_post_meta_title">Place Title: </span></span>`+place.name+`</div>
                        <div class="geodir_post_meta  geodir-field-address" itemscope="" itemtype="http://schema.org/PostalAddress">
                            <span class="geodir_post_meta_icon geodir-i-address"><i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                            <span class="geodir_post_meta_title">Address: </span></span><span itemprop="streetAddress">`+place.address+`</span>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            </div>
            </div>`;

            return content;
        }
}



    </script>
@endsection
