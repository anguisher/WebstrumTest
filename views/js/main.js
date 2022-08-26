$(document).ready(function(){
    $("#webstrumImage").change((e) => {
        uploadPhoto();
    });
    $("#webstrumUploadPhoto").click((e) => {
        $("#webstrumImage").click();
        //getExistingPhotos();
    });
    $(".webstrumPhotosRow").ready(() => {
        getExistingPhotos();
    });
});

function uploadPhoto(){
    var fileInp = document.getElementById("webstrumImage");
    var formData = new FormData();
    var productId = fileInp.getAttribute('productId');
    var url = `http://127.0.0.1/en/module/webstrum/apg?ajax=1`
    formData.append('uploadFiles', '1');
    formData.append("file", fileInp.files[0]);
    formData.append("webstrumProductId", productId);
    $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function(jsonData)
        {
            if(jsonData.status == "error"){
                console.log(jsonData.message);
            }
            else{
                addPhoto(jsonData.filename);
            }
        },
        error : (err) =>{
            console.log(err);
        }
    });

}
function getExistingPhotos(){
    $(".webstrumPhotosContainer").html('');
    var formData = new FormData();
    var url = `http://127.0.0.1/en/module/webstrum/apg?ajax=1`
    formData.append("getExistingPhotos", '1');
    formData.append("webstrumProductId", 19);
    $.ajax({
        type: 'POST',
        url: url,
        dataType: 'json',
        data: formData,
        processData: false,
        contentType: false,
        success: function(jsonData)
        {
            for(item of jsonData){
                addPhoto(item.photo_url);
            }
        },
        error : (err) =>{
            console.log(err);
        }
    });
}
function addPhoto(fileName){
    $(".webstrumPhotosContainer").append(
        `<div class="col-1 align-self-center webstrumPhotoCol mx-1">
            <img src="/img/webstrum/${fileName}">
        </div>`
    );
}