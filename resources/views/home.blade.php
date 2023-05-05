<!DOCTYPE html>
<html lang="en">
<head>
	<title>Demo</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</head>
<body>
	<div class="container mt-5">
		<form action="#" id="searchForm">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="fname">Origin:</label>
						<input class="form-control" type="text" id="search_origin" placeholder="Search Location" autocomplete="off" required>
						<input type="hidden" id="origin_latitude" name="origin_latitude">
						<input type="hidden" id="origin_longitude" name="origin_longitude">
						<span style="color: red;" class="error" id="o_error"></span>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="fname">Destination:</label>
						<input class="form-control" type="text" id="search_destination" placeholder="Search Destination" autocomplete="off" required>
						<input type="hidden" id="destination_latitude" name="destination_latitude">
						<input type="hidden" id="destination_longitude" name="destination_longitude">
						<span style="color: red;" class="error" id="d_error"></span>
					</div>				
				</div>
			</div>
			<button type="button" class="btn bg-warning" id="btnSubmit" onclick="getDistance()">
				Submit
			</button>
		</form>

		<div id="results" class="mt-5"></div>
	</div>

	<script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
	<script src="http://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places&key=AIzaSyAKSwOvQw4ngnepwqk1R8ZxQxHsp37xrXI" type="text/javascript" defer></script>

	<script>
		$(document).ready(function(){
			const search_origin = document.getElementById("search_origin");
			const searchOrigin = new google.maps.places.Autocomplete(search_origin);
	
			const search_destination = document.getElementById("search_destination");
			const searchDestination = new google.maps.places.Autocomplete(search_destination);
	
			google.maps.event.addListener(searchOrigin, 'place_changed', function() {
				var near_place = searchOrigin.getPlace();
				$('#origin_latitude').val(near_place.geometry.location.lat());
				$('#origin_longitude').val(near_place.geometry.location.lng());
			});
	
			google.maps.event.addListener(searchDestination, 'place_changed', function() {
				var near_place = searchDestination.getPlace();
				$('#destination_latitude').val(near_place.geometry.location.lat());
				$('#destination_longitude').val(near_place.geometry.location.lng());
			});
		});

		function getDistance(){
			var isvalid = 1;
			$(".error").html("");
			if($('#origin_latitude').val() == "" && $('#origin_longitude').val() == ""){
				$("#o_error").html("Please select origin.");
				isvalid = 0;
			}
			if($('#destination_latitude').val() == "" && $('#destination_longitude').val() == ""){
				$("#d_error").html("Please select destination.");
				isvalid = 0;
			}
			if(isvalid == 1){
				$("#btnSubmit").text("Loading...").prop("dosabled", true);
				$.ajax({
					headers:
					{
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					url: "{{ url('/getDistance') }}",
					type: "POST",
					data: {
						'origin_latitude': $('#origin_latitude').val(),
						'origin_longitude': $('#origin_longitude').val(),
						'destination_latitude': $('#destination_latitude').val(),
						'destination_longitude': $('#destination_longitude').val()
					},
					dataType: 'JSON',
					async: true,
					success: function(data) {
						var html = '<p><b>Total Distance : </b>'+ data.distance +'</p>';
						html += '<p><b>Total Duration : </b>'+ data.duration +'</p>';
						$("#results").html(html);
	
						$("#btnSubmit").text("Submit").prop("dosabled", false);
					}
				});
			}
		}


	</script>
</body>
</html>
