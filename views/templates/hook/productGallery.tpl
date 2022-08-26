<div class="card shadow w-100 m-0">
	<div class="card-header">Additional Gallery</div>
	<div class="card-body">
		<div class="row w-100 m-0 webstrumAdditionalPhotoRow" style="height: 120px; overflow: auto;">
			{foreach $additional_photos as $photo}
				<div class="col-lg-2 p-0" style="height: 80px; border: 1px solid red; margin: 1px">
					<img src="/img/webstrum/{$photo['photo_url']}" class="h-100 w-100" style="object-fit: cover;"/>
				</div>
			{/foreach }
		</div>
	</div>
</div>