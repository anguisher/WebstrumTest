$(document).ready(function(){
    $(window).click((e) => {
        if(e.target.matches(".webstrumModal") || e.target.matches(".webstrumModal div"))
            $(".webstrumModal").hide();
    })
    $(window).keyup((e) => {
        if(e.key == "Escape")
            $(".webstrumModal").hide();
    })
    $(".webstrumAdditionalPhotoRow .photoCol").click((e) => {
        var photoId = e.delegateTarget.attributes.photoId.value;
        var imgSrc = $(`.webstrumAdditionalPhotoRow .photoCol[photoId="${photoId}"] img`).attr("src");
        openModal(imgSrc);
    });
});
function openModal(imgSrc){
    $(`.webstrumModal img`).attr("src", imgSrc);
    $(".webstrumModal").show(300);
}