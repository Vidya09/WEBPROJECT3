<?php 
    
            if(isset($_GET["search"]) && !empty($_GET["keyword"])){
                
                    if($_GET["location"]!= ""){
                        $locationV = urlencode($_GET["location"]);
                        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$locationV."&key=AIzaSyAhlMBrrYVBhgDBzPlc2aFC7KOSQ3igEtc";
                        $json = file_get_contents($url);

                        $obj = json_decode($json);
                        $lat=$obj->results[0]->geometry->location->lat;
                        $lng=$obj->results[0]->geometry->location->lng;

                    }else{

                        $lat = $_GET["ipApiLat"];
                        $lng = $_GET["ipApiLon"];

                    }

                    $radius = $_GET["distance"]*1609.34;
                    $searchUrl = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".$lat.",".$lng."&radius=".$radius."&type=".$_GET["selectOptions"]."&keyword=".$_GET["keyword"]."&key=AIzaSyBUBKUo91P8Jvxy2oJDRlxv1u78YTcZAmM";

                    $json2 = file_get_contents($searchUrl);

                    echo $json2;
                    exit;
            }

            if(isset($_GET["location"])){
               
                    $locationV = urlencode($_GET["location"]);
                    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$locationV."&key=AIzaSyAhlMBrrYVBhgDBzPlc2aFC7KOSQ3igEtc";
                    $location_json = file_get_contents($url);

                    echo $location_json;
                    exit;
                        
            }
                          
           if (!empty($_GET["place"])){
               
                    $place_id = $_GET["place"];
                    $count = 0;
                    $url = "https://maps.googleapis.com/maps/api/place/details/json?placeid=".$place_id."&key=AIzaSyDvkVLw9SbH_nSkTTT52mwdiJ7kaP0HKAY";

                    $json = file_get_contents($url);
                    $obj = json_decode($json);
                                    
                        if(isset($obj->result->photos)){
                            $photos = $obj->result->photos;
                            $count =  count($photos);
                            
                            for($i=0; $i<5; $i++){
                                  $filename = "photo".$i.$place_id.".png";
                                            if (file_exists($filename)) {
                                                unlink($filename);
                                            }  
                            }
                            
                                               
                            if($count>=5){
                                for($i=0;$i<5;$i++){
                                    $reference= $photos[$i]->photo_reference;
                                    //echo $reference;
                                    $url = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=750&photoreference=".$reference."&key=AIzaSyAMBv052xQeW-xVOWWqqLlGMHlMnisi4cw";
                                    $writtenfromimage = file_get_contents($url);
                                    file_put_contents("photo".$i.$place_id.".png", $writtenfromimage);
                                    //echo "heydoneNo";
                                }
                            }else if($count >0 && $count <5){
                                for($i=0;$i<$count;$i++){
                                    $reference= $photos[$i]->photo_reference;
                                    //echo $reference;
                                    $url = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=750&photoreference=".$reference."&key=AIzaSyAMBv052xQeW-xVOWWqqLlGMHlMnisi4cw";
                                    $writtenfromimage = file_get_contents($url);
                                    file_put_contents("photo".$i.$place_id.".png", $writtenfromimage);
                                }
                                 $oldImagesCount = 5- $count;
                                
                                if($oldImagesCount > 0){
                                    for($i=4;$i >= $count; $i--){
                                        $filename = "photo".$i.$place_id.".png";
                                        if (file_exists($filename)) {
                                            unlink($filename);
                                        }
                                    }
                                }
                               
                            }else if($count == 0){
                            for($i=0; $i<5; $i++){
                              $filename = "photo".$i.$place_id.".png";
                                        if (file_exists($filename)) {
                                            unlink($filename);
                                        }  
                            }
                        }
                           
                    }
            

                    echo $json;
                    exit;               
            }
     ?>
    
    
<!DOCTYPE html>
<html>
    
    <head>
        <title>hw6</title>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBzjCC9rtnwB21YS94aiGrcLvo1ywMmaOI"></script>
        <style>
            .mapModesHover:hover{
                background-color: #d8d7d8;
            }
        </style>
         
    </head>
     
<body>
    <div>
        <div style="height:200px;width:600px;align:center;background-color:#faf9fa;border: 2px solid #c9c7c9;margin-left:25%;">
            <p style="margin-left: 130px;letter-spacing: 0.4px;font-size: 22px;margin-top:5px;"><i>Travel and Entertainment Search</i></p>
            <hr style="margin: -15px 9px 0px 9px;">
            <form onsubmit="event.preventDefault();searchResult();">
                <div style="margin:5px;">
                    Keyword: <input type="text" id="keyword" name="keyword" required><br>
                    <div style="margin-top:5px;">
                    Category: <select id="selectOptions" name="selectOptions">
                                  <option value="default">default</option>
                                  <option value="cafe">cafe</option>
                                  <option value="bakery">bakery</option>
                                  <option value="restaurant">restaurant</option>
                                  <option value="beauty_salon">beauty salon</option>
                                  <option value="casino">casino</option>
                                  <option value="movie_theater">movie theater</option>
                                  <option value="lodging">lodging</option>
                                  <option value="airport">airport</option>
                                  <option value="train_station">train station</option>
                                  <option value="subway_station">subway station</option>
                                  <option value="bus_station">bus station</option>
                            </select><br>
                    </div>
                    <div style="float:left;margin-top:5px;">
                     Distance(miles):<input type="text" id="distance" name="distance" placeholder="10" value="">
                    </div>
                    <div style="float:left;margin-top:5px;margin-left:5px;">
                     from:<input class="w3-radio" type="radio" id="hereIsSelected" name="location" value="Here" checked onchange="setLocation(this);">
                           <label>Here</label><br>

                          <input style="margin-left:41px;" class="w3-radio" type="radio" name="location" value="location" onchange="setLocation(this);">
                          <input type="text" id="location" name="location" placeholder="location"  disabled>

                    </div>
                    <input id ="ipApiLat" style="display:none;" name="ipApiLat">
                    <input id ="ipApiLon" style="display:none;" name="ipApiLon">
                    

                    <div style="margin-top:75px;margin-left:60px;">
                        <input type="submit" id="searchButton" style="float:left;" value="Search" name="search" >
                        <input type="button" style="float:left;margin-left:5px;" value="Clear" onclick="clearValues();">
                    </div>
               </div>
            </form>  
        </div>
        <div id="searchResults"></div>

    </div>
    <script type="text/javascript">
        document.getElementById("searchButton").disabled = true;
        var sourceLat;
        var sourceLng;
        var reviews;
        var placePhotos;
        var placeId;
        function myDisplayFunction(data){
            
            console.log(data);
            if(data.lat && data.lon){
                document.getElementById("ipApiLat").value = data.lat;
                document.getElementById("ipApiLon").value = data.lon;
              
                document.getElementById("searchButton").disabled = false;
            }else{
                document.getElementById("searchButton").disabled = true;
            }
        }
        
        function setLocation(e){
            
           if(e.defaultValue== "location"){
                document.getElementById("location").disabled = false;
                document.getElementById("location").required = true;
            }else if(e.defaultValue == "Here"){
                document.getElementById("location").value = "";
                document.getElementById("location").disabled = true;
                document.getElementById("location").required = false;
            }
        }
        
        function clearValues(){
            document.getElementById("distance").value ='';
            document.getElementById("location").value = '';
            document.getElementById("selectOptions").value = "default";
            document.getElementById("keyword").value = '';
            document.getElementById("searchResults").innerHTML = '';
            document.getElementById("location").required = false;
            document.getElementById("location").disabled = true;
            document.getElementById("hereIsSelected").checked = true;
        }
        
        function searchResult(){
            var distance;
            var hereIsSelected = document.getElementById("hereIsSelected").checked;
            var ipApiLat= document.getElementById("ipApiLat").value;
            var ipApiLon = document.getElementById("ipApiLon").value;
            if(document.getElementById("distance").value == ''){
                distance = 10;
            }else{
                distance = document.getElementById("distance").value;
            }
            var location = document.getElementById("location").value;
            var selectOptions = document.getElementById("selectOptions").value;
            var keyword = document.getElementById("keyword").value;
            document.getElementById("ipApiLon").value 
            var xhttpRequest = new XMLHttpRequest();
            var maparams = "search=test&selectOptions="+selectOptions+"&keyword="+keyword+"&ipApiLat="+ipApiLat+"&ipApiLon="+ipApiLon+"&distance="+distance+"&location="+location+"&hereIsSelected="+hereIsSelected;
            xhttpRequest.onreadystatechange = function() {
                if (xhttpRequest.readyState == 4 && xhttpRequest.status == 200) {
                  
                     console.log(xhttpRequest.responseText);
                    var data = JSON.parse(xhttpRequest.responseText);
                   
                    startSearch(data);
                    
                 }
            };
          
              xhttpRequest.open("GET", "hw6index.php?"+maparams, true);
              xhttpRequest.setRequestHeader("Content-type", "application/x-www-from-urlencoded");
              xhttpRequest.send(); 
        }
        
        function showGoogleMaps(mapParentElement){
       
            var mapId =  mapParentElement.id;
            var mapParentEle = document.getElementById(mapParentElement.parentElement.id);
            var deslat = document.getElementById(mapParentElement.parentElement.id).getAttribute("lat");
           
            var deslng = document.getElementById(mapParentElement.parentElement.id).getAttribute("lng");
          
            if(!document.getElementById("child"+mapId)){
                
                var modeOptions = document.createElement('div');
                modeOptions.innerHTML = "<div id='travelModes"+mapId+"' style='position: absolute;top:21px;left:15px;width:120px;height:110px;z-index:10;background-color:#e9e7e9;'><div class='mapModesHover' style='height:40px;text-align: center;padding: 5px 0px 0px 0px;' deslat='"+deslat+"' deslng='"+deslng+"' mapid='"+mapId+"' onclick='directionWithTravelMode(this)'>Walk there</div><div onclick='directionWithTravelMode(this)' class='mapModesHover' style='height:40px;text-align: center;' deslat='"+deslat+"' deslng='"+deslng+"' mapid='"+mapId+"'>Bike there</div><div class='mapModesHover' onclick='directionWithTravelMode(this)' style='height:40px;text-align: center;' deslat='"+deslat+"' deslng='"+deslng+"' mapid='"+mapId+"'>Drive there</div></div>";
                mapParentEle.appendChild(modeOptions.firstChild);
                var newcontent = document.createElement('div');
                newcontent.innerHTML = "<div name='placesMaps' id='child"+mapId+"' style='display:none;position: absolute;top: 20px;left:2%;z-index: 5;background-color: #fff;padding: 5px;border: 1px solid #999; height:400px;width:450px; '></div><div test='visible' style='position: absolute;top: 5px;left:5px;width:40px;height:40px;border:1px solid black;z-index:5px;'></div>";

                mapParentEle.appendChild(newcontent.firstChild);
                
                document.getElementById("child"+mapId).style.display = "block";
                document.getElementById("travelModes"+mapId).style.display = "block";
            }else if(document.getElementById("child"+mapId)) {
                if(document.getElementById("child"+mapId).style.display == "block"){
                    document.getElementById("child"+mapId).style.display = "none";
                    document.getElementById("travelModes"+mapId).style.display = "none";
                }else if(document.getElementById("child"+mapId).style.display == "none"){
                    document.getElementById("child"+mapId).style.display = "block";
                    document.getElementById("travelModes"+mapId).style.display = "block";
                }
            }
            
            var directionsDisplay = new google.maps.DirectionsRenderer;
            var directionsService = new google.maps.DirectionsService;
            var map = new google.maps.Map(document.getElementById("child"+mapId), {
                zoom: 14,
                center: {lat: parseFloat(deslat), lng: parseFloat(deslng)}
            });
            
            var marker = new google.maps.Marker({
                    position: {lat: parseFloat(deslat), lng: parseFloat(deslng)},
                    map: map,
                    title: ''
            });
            directionsDisplay.setMap(map);
        }
        
        function directionWithTravelMode(element){
            var mapdeslat = element.getAttribute("deslat");
            var mapdeslng = element.getAttribute("deslng");
            var mapdesid  = element.getAttribute("mapid");
            var mapsid = "child"+mapdesid;
            //document.getElementById(mapsid).innerHTML= '';
            
            var directionsDisplay = new google.maps.DirectionsRenderer;
            var directionsService = new google.maps.DirectionsService;
            var map = new google.maps.Map(document.getElementById(mapsid), {
                zoom: 14,
                center: {lat: parseFloat(mapdeslat), lng: parseFloat(mapdeslng)}
            });
            var selectedMode;
            directionsDisplay.setMap(map);
                if(element.innerText == "Walk there"){
                    selectedMode= "WALKING";
                }else if (element.innerText == "Bike there"){
                              selectedMode="BICYCLING";
                          }else if(element.innerText == "Drive there"){
                                selectedMode= "DRIVING";
                          }
            calculateAndDisplayRoute(directionsService, directionsDisplay,selectedMode,mapdeslat,mapdeslng);
            
        }
        
        function calculateAndDisplayRoute(directionsService, directionsDisplay,selectedMode,mapdeslat,mapdeslng) {
          
            directionsService.route({
              origin: {lat:sourceLat ,lng:sourceLng },  
              destination: {lat: parseFloat(mapdeslat), lng: parseFloat(mapdeslng)}, 
              travelMode: google.maps.TravelMode[selectedMode]
            }, function(response, status) {
              if (status == 'OK') {
                directionsDisplay.setDirections(response);
              } else {
                window.alert('Directions request failed due to ' + status);
              }
            });
      }
        
        function startSearch(data){
           
            var hereIsSelected = document.getElementById("hereIsSelected").checked;
            var ipApiLat= document.getElementById("ipApiLat").value;
            var ipApiLon = document.getElementById("ipApiLon").value;
            if(hereIsSelected){
                sourceLat = parseFloat(ipApiLat);
                sourceLng = parseFloat(ipApiLon);
            }else{
                
                var location = document.getElementById("location").value ;
                var params = "location="+location
                var xhttp = new XMLHttpRequest();
            
                xhttp.onreadystatechange = function() {
                    if (xhttp.readyState == 4 && xhttp.status == 200) {
                       
                        console.log(xhttp.responseText);
                        var data = JSON.parse(xhttp.responseText);
                        var latLng = data.results[0].geometry.location;
                        sourceLat = latLng.lat;
                        sourceLng = latLng.lng;
                     }
                };
                
                xhttp.open("GET", "hw6index.php?"+params, true);
                xhttp.setRequestHeader("Content-type", "application/x-www-from-urlencoded");
                xhttp.send();  
            }
            
           
            var searchTableItems = data.results;
            
            if(searchTableItems.length > 0){
                                    var searchTable = "<table border='2' style='border-collapse:collapse;width:90%;margin: 20px 0px 0px 60px;'>";
                                    searchTable += "<tbody>";
                                    searchTable += "<tr>";
                                    searchTable += "<th>Category</th>";
                                    searchTable += "<th>Name</th>";
                                    searchTable += "<th>Address</th>";
                                    searchTable += "</tr>";

                                    for(i=0;i<searchTableItems.length;i++){
                                        searchTable += "<tr style='cursor:pointer;'>";
                                        searchTable += "<td><img  style='height: 20px;width: 40px;padding-left:5px;' src='" + searchTableItems[i].icon + "'></td>";
                                        searchTable += "<td style='padding-left:10px;' id='"+searchTableItems[i].place_id+"' onclick='showPhotosReviews(this)'>"+searchTableItems[i].name+"</td>";
                                        searchTable += "<td lat='"+searchTableItems[i].geometry.location.lat+"' lng='"+searchTableItems[i].geometry.location.lng+"' style='position:relative;padding-left:10px;' id='tdParent"+searchTableItems[i].place_id+"'><div style='width:100%;height:100%;' id='maps"+searchTableItems[i].place_id+"' onclick='showGoogleMaps(this)'>"+searchTableItems[i].vicinity+" </div></td>";
                                        searchTable += "</tr>";
                                   }
                                   searchTable += "</tbody>";
                                   searchTable += "</table>";
                                   
                                   document.getElementById("searchResults").innerHTML = searchTable;
                                   
                                }else{
                                   document.getElementById("searchResults").innerHTML = "<div style='text-align:center;margin-left:285px;'><p style='width:700px;border:1px solid #c9c7c9;padding:1px;text-align: center;font-weight: bold;background-color:#f0eef0;'>No Records has been Found </p></div>"
                                }
           
        }
        
    function showPhotosReviews(nameElement){
            
            placeId = nameElement.id;
            var params = "place="+placeId;
            var xhttp = new XMLHttpRequest();
            
            xhttp.onreadystatechange = function() {
                if (xhttp.readyState == 4 && xhttp.status == 200) {
                    
                    console.log(xhttp.responseText);
                    var data = JSON.parse(xhttp.responseText);
                    reviews = data.result.reviews;
                    placePhotos = data.result.photos;
                   
                    var divSection = "<div style='margin: 1% 0% 0% 25%;width: 600px;'>";
                     divSection += "<div style='font-weight: bold;font-family:helvatica;text-align: center;'>"+nameElement.innerText+"</div>";
                    divSection += "<p id='reviewsHeading' style='text-align: center;'>Click to show reviews </p>";
                    divSection += "<div style='text-align:center;'><img id='reviewsdown' style='height: 20px;width: 30px;padding-left:5px;' src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png' onclick='showPlaceReviews()'></div>";
                    divSection +="<div id='reviewsWithWidthHeight'></div>"
                    divSection += "<p id='photosHeading' style='text-align: center;'>Click to show photos</p>";
                    divSection += "<div style='text-align:center;'><img id='photosImgDown' style='height: 20px;width: 30px;padding-left:5px;' src='http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png' onclick ='showPlacePhotos(this)'></div>";
                    divSection +="<div id='photosWithWidthHeight'></div>"
                    divSection += "</div>"
                    
                    document.getElementById("searchResults").innerHTML = divSection;
                 }
            };
            xhttp.open("GET", "hw6index.php?"+params, true);
            xhttp.setRequestHeader("Content-type", "application/x-www-from-urlencoded");
            xhttp.send(); 
           
        }
        
        function showPlaceReviews(){
           
            var maxReviews;
            var reviewsAnchor = '';
           
            if(!reviews || reviews.length == 0){
                maxReviews = 0;
                reviewsAnchor+="<p style='width:auto;border:1px solid #c9c7c9;padding:1px;text-align: center;font-weight: bold;'>No Reviews Found </p>"
            }else if(reviews && reviews.length >= 5 ){
                maxReviews = 5;
            }else if(reviews && reviews.length <5){
                maxReviews = reviews.length;
            }
            
            if(document.getElementById("reviewsdown")){
                
                document.getElementById("photosHeading").innerHTML = "Click to show photos";
                document.getElementById("photosWithWidthHeight").innerHTML = '';
                if(document.getElementById("photosImgUp")){
                    document.getElementById("photosImgUp").setAttribute("id", "photosImgDown");
                    document.getElementById("photosImgDown").setAttribute("src", "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png");
                }
                document.getElementById("reviewsHeading").innerHTML = "Click to hide reviews";
                document.getElementById("reviewsdown").setAttribute("id", "reviewsUp");
                document.getElementById("reviewsUp").setAttribute("src", "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png");
                document.getElementById("reviewsWithWidthHeight").innerHTML = '';
                
                var photoDoesNotExists = 0;
                if(maxReviews > 0){
                    reviewsAnchor += "<table style='border-collapse:collapse;width:600px;border: 2px solid #c9c7c9;'>";
                    reviewsAnchor += "<tbody>";
                    for(var i =0; i< maxReviews;i++){
                        reviewsAnchor += "<tr><td><div style='height:auto;'><div style='height:auto;border-bottom:2px solid #c9c7c9;min-height:50px;'><div style='float:left;margin-left: 220px;padding-top:5px;'><img src='"+reviews[i].profile_photo_url+"' alt='image' height='40' width='40' /></div><div style='float:left'><p> "+reviews[i].author_name+"</p></div></div><div style='height:auto;min-height:20px;border-bottom:2px solid #c9c7c9;'>"+reviews[i].text+"</div></div></td></tr>";
                    }
                     reviewsAnchor += "</tbody>";
                     reviewsAnchor += "</table>";
                }
                
                document.getElementById("reviewsWithWidthHeight").innerHTML = reviewsAnchor;
            }else if(document.getElementById("reviewsUp")){ 
     
                document.getElementById("reviewsHeading").innerHTML = "Click to show reviews";
                document.getElementById("reviewsWithWidthHeight").innerHTML = '';
                document.getElementById("reviewsUp").setAttribute("id", "reviewsdown");
                document.getElementById("reviewsdown").setAttribute("src", "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png");
            }
        }
        
        function showPlacePhotos(divSection){
            
            var maxImages;
            var photosAnchor = "";
            if(!placePhotos || placePhotos.length == 0){
                maxImages = 0;
                photosAnchor+="<p style='width:auto;border:1px solid #c9c7c9;padding:1px;text-align: center;font-weight: bold;'>No Photos Found </p>"
            }else if(placePhotos && placePhotos.length >= 5 ){
                maxImages = 5;
            }else if(placePhotos && placePhotos.length <5){
                maxImages = placePhotos.length;
            }
            if(document.getElementById("photosImgDown")){
                
                document.getElementById("reviewsHeading").innerHTML = "Click to show reviews";
                document.getElementById("reviewsWithWidthHeight").innerHTML = '';
                if(document.getElementById("reviewsUp")){
                    document.getElementById("reviewsUp").setAttribute("id", "reviewsdown");
                    document.getElementById("reviewsdown").setAttribute("src", "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png");
                }
                document.getElementById("photosHeading").innerHTML = "Click to hide photos";
                document.getElementById("photosImgDown").setAttribute("id", "photosImgUp");
                document.getElementById("photosImgUp").setAttribute("src", "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_up.png");
                document.getElementById("photosWithWidthHeight").innerHTML = '';
                
                if(maxImages > 0){
                    for(var i =0; i< maxImages;i++){
                       
                           photosAnchor+= '<div style="border:2px solid #c9c7c9;padding: 10px;"><a style="width:auto;cursor:pointer;" id="'+placePhotos[i].photo_reference+'" href="photo'+i+placeId+'.png" target="_blank"><img id="'+placePhotos[i].photo_reference+'" src="photo'+i+placeId+'.png" alt="image" height="400" width="575" /></a></div>';
                       
                    }
                }
                document.getElementById("photosWithWidthHeight").innerHTML = photosAnchor;
            }else if(document.getElementById("photosImgUp")){
                
                document.getElementById("photosHeading").innerHTML = "Click to show photos";
                document.getElementById("photosWithWidthHeight").innerHTML = '';
                document.getElementById("photosImgUp").setAttribute("id", "photosImgDown");
                document.getElementById("photosImgDown").setAttribute("src", "http://cs-server.usc.edu:45678/hw/hw6/images/arrow_down.png");
            }
        }
       
    </script>
    <script type="text/javascript" src="http://ip-api.com/json?callback=myDisplayFunction">

    </script>
</body>
</html>