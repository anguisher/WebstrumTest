<div class="card shadow w-100">
	<div class="card-header">{$name_product}</div>
	<div class="card-body">
		<div class="row w-100 m-0 mb-3">
			<div class="col">
				<input type="file" class="form-control form-control-sm" productId="{$id_product}" name="webstrumImage" id="webstrumImage" style="display: none"/>
			</div>
		</div>
		<div class="row w-100 m-0 webstrumPhotosRow">
			<div class="col-1 align-self-center m-1" id="webstrumUploadPhoto">
				<div class="row h-100 w-100 m-0 justify-content-center">
					<div class="col p-0 align-self-center">
						<div class="row h-100 w-100 m-0 justify-content-center">
							<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 16 16">
								<path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z"/>
							 	<path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z"/>
							</svg>
						</div>
					</div>
				</div>
			</div>
			<div class="col align-self-center p-0">
				<div class="row w-100 m-1 webstrumPhotosContainer"></div>
			</div>
		</div>
	</div>
</div>
<div class="webstrumModal">
	<div class="content">
		<div class="row w-100 h-100">
			<div class="col h-100 align-self-center p-0">
				<div class="row w-100 m-0 h-100 justify-content-center">
					<img >
				</div>
			</div>
		</div>
	</div>
</div>