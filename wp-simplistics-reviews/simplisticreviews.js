   var map, infowindow, service;
    var markerLatLng = new google.maps.LatLng(43.7047518, -79.4100318);

    function initialize() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: markerLatLng
        });

        var request = {
            placeId: 'ChIJDaS6RAguK4gRgBZFZYOfAus'
        };

        infowindow = new google.maps.InfoWindow();

        service = new google.maps.places.PlacesService(map);
        console.log('Got Service');

        service.getDetails(request, function (place, status) {
            console.log('Server Status:' + status.toString());
            if (status == google.maps.places.PlacesServiceStatus.OK) {
                console.log('Got details');
                createMarker(place);
            }
        });
    }

    function buildReviewList(parentElement, items) {
        var i, l, list, li, licontents;
        if (!items || !items.length) { return; }
        list = $("<ul></ul>").appendTo(parentElement); 
        for (i = 0, l = items.length; i < l; i++) {
            licontents = '<li>';
            licontents += '<label class="reviewuser">' + items[i].author_name + '</label>';
            licontents += '<span class="reviewstars rating_' + items[i].rating + '"> </span>';
            licontents += '<span class="reviewtext">' + items[i].text + '</span>';            
            licontents += '</li>';
            list.append(licontents);
            console.log('Building list items...');
        }
    }

    function createMarker(place) {
        var image = 'img/marker.png';
        var icon = new google.maps.MarkerImage("http://maps.google.com/mapfiles/ms/micons/blue.png",
                           new google.maps.Size(32, 32), new google.maps.Point(0, 0),
                           new google.maps.Point(16, 32));
        var placeLoc = place.geometry.location;
        var marker = new google.maps.Marker({
            map: map,
            icon: icon,
            title: place.name, 
            position: place.geometry.location
        });
        var request = {
            reference: place.reference
        };
        console.log('Getting details');

        service.getDetails(request, function (details, status) {
        console.log('Server Details Status:' + status.toString());

            /*-----------------*/
            buildReviewList($("#result1").empty(), place.reviews);

            
            var resultcontent;
            len = place.reviews.length;
            for (i = 0; i < len; ++i) {
                //Debug only
                /*
                resultcontent = 'Review # ' + i.toString() + ' : ';
                resultcontent += place.reviews[i].rating + ' stars';
                resultcontent += ', On ' + place.reviews[i].time + ' ' + place.reviews[i].author_name + ' said:';
                if (!!place.reviews[i].text) resultcontent += '<br />' + place.reviews[i].text;
                console.log(resultcontent);
             */

                /*
                var jdata = {
                    'action': 'my_action',
                    'whatever': JSON.stringify(place.reviews[i])
                };

                xmlhttp = new XMLHttpRequest();
                var myJsonString = JSON.stringify(place.reviews[i]);
                xmlhttp.onreadystatechange = respond;
                //xmlhttp.open("POST", ajaxurl, true);
                xmlhttp.open("POST", "/wp-admin/xwrite.php", true);
                xmlhttp.send(jdata);
                //xmlhttp.send(myJsonString);
                */
            }

            function respond() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById('result').innerHTML = xmlhttp.responseText;
                    console.log('Response returned');
                }
            }
            /*-----------------*/


        });

    }
