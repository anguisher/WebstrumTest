var url = `http://127.0.0.1/en/module/webstrum/gallery?ajax=1`
$(document).ready(function(){
    $("#webstrumImage").change((e) => {
        uploadPhoto();
    });
    $("#webstrumUploadPhoto").click((e) => {
        $("#webstrumImage").click();
    });
    $(".webstrumPhotosRow").ready(() => {
        getExistingPhotos();
    });
    $(window).click((e) => {
        if(e.target.matches(".webstrumModal") || e.target.matches(".webstrumModal div"))
            $(".webstrumModal").hide();
    })
    $(window).keyup((e) => {
        if(e.key == "Escape")
            $(".webstrumModal").hide();
    })
});
function uploadPhoto(){
    var fileInp = document.getElementById("webstrumImage");
    var productId = fileInp.getAttribute('productId');
    var formData = new FormData();
    formData.append('uploadFiles', '1');
    formData.append("file", fileInp.files[0]);
    formData.append("webstrumProductId", productId);
    ajaxFileRequest(url, formData, (jsonData) => {
        addPhoto(jsonData.photo);
    }, (err) => {
        console.log(err);
    });

}
function deletePhoto(photoId){
    var formData = new FormData();
    formData.append("deletePhoto", "1");
    formData.append("photoId", photoId);
    ajaxFileRequest(url, formData, (jsonData) => {
        $(`.webstrumPhotoCol[photoId="${photoId}"]`).remove();
    }, (err) => {
        console.log(err);
    });
}
function getExistingPhotos(){
    $(".webstrumPhotosContainer").html('');
    var productId = $("#webstrumImage").attr("productId");
    var formData = new FormData();
    formData.append("getPhotos", '1');
    formData.append("webstrumProductId", productId);
    ajaxFileRequest(url, formData, (jsonData) => {
        for(photo of jsonData.photos){
            addPhoto(photo);
        }
    }, (err) => {
        console.log(err);
    });
}
function ajaxFileRequest(url, formData, successCallback, errorCallback){
    $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: (jsonData) => {
            jsonData.status == "success" ? 
                successCallback(jsonData) : 
                console.log(jsonData.message);
        },
        error : (err) => {errorCallback(err)}
    });
}
function addPhoto(photo){
    $(".webstrumPhotosContainer").append(
        `<div class="col-1 p-0 webstrumPhotoCol m-1" photoId="${photo.id}">
            <div class="row m-0 zoomPhoto" photoId="${photo.id}">
                <div class="col p-0 align-self-center">
                    <div class="row w-100 m-0 justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14zm2.5-4h-2v2H9v-2H7V9h2V7h1v2h2v1z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="row m-0 deletePhoto" photoId="${photo.id}">
                <div class="col p-0 align-self-center">
                    <div class="row w-100 m-0 justify-content-center">
                        <span>&times;</span>
                    </div>
                </div>
            </div>
            <img src="/img/webstrum/${photo.photo_url}">
        </div>`
    );
    $(`.webstrumPhotosContainer .deletePhoto[photoId="${photo.id}"]`).click((e) => {
        var photoId = e.delegateTarget.attributes.photoId.value;
        deletePhoto(photoId);
    });
    $(`.webstrumPhotoCol .zoomPhoto[photoId="${photo.id}"]`).click((e) => {
        var imgSrc = $(`.webstrumPhotoCol[photoId="${photo.id}"] img`).attr("src");
        openModal(imgSrc);
    });
}
function openModal(imgSrc){
    $(`.webstrumModal img`).attr("src", imgSrc);
    $(".webstrumModal").show(300);
}