<div class="card shadow w-100 m-0 webstrumAdditionalGalleryCard">
	<div class="card-header">Additional Gallery</div>
	<div class="card-body">
		<div class="row w-100 m-0 webstrumAdditionalPhotoRow">
			{foreach $additional_photos as $photo}
				<div class="col-lg-2 p-0 photoCol" photoId="{$photo['id']}">
					<img src="/img/webstrum/{$photo['photo_url']}" class="h-100 w-100"/>
				</div>
			{/foreach }
		</div>
	</div>
</div>
<div class="webstrumModal">
	<div class="content">
		<div class="row w-100 h-100 justify-content-center">
			<div class="col h-100 align-self-center p-0">
				<div class="row w-100 h-100">
					<img class="mx-auto">
				</div>
			</div>
		</div>
	</div>
</div>