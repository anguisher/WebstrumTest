$(document).ready(function(){
    $(window).click((e) => {
        if(e.target.matches(".webstrumModal") || e.target.matches(".webstrumModal div"))
            closeModal();
    })
    $(window).keyup((e) => {
        if(e.key == "Escape")
            closeModal();
    })
    $(".webstrumAdditionalPhotoRow .photoCol").click((e) => {
        var photoId = e.delegateTarget.attributes.photoId.value;
        var imgSrc = $(`.webstrumAdditionalPhotoRow .photoCol[photoId="${photoId}"] img`).attr("photoUrl");
        openModal(imgSrc);
    });
});
function closeModal(){
    $(`.webstrumModal img`).attr("src", "").hide();
    $(".webstrumModal").hide();
}
function openModal(imgSrc){
    $(`.webstrumModal img`).attr("src", imgSrc).show();
    $(".webstrumModal").show(300);
}