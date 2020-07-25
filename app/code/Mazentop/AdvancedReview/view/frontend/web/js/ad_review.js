require([
    "jquery"
], function($){
    $(document).ready(function() {
        $(document).on('click','.ad-review .review-content img',function(){
            $(this).toggleClass('isclick');
            $(this).siblings('img').removeClass('isclick');
        });
        $(document).on("change","input[type='file']",function(){
            var filePath=$(this).val();
            console.log(filePath);
            if(filePath.indexOf("jpg")!=-1||filePath.indexOf("png")!=-1||filePath.indexOf("gif")!=-1){
                var arr=filePath.split('\\');
                var fileName=arr[arr.length-1];
                $(this).siblings('span').text(fileName);
            }else{
               alert('The file is not correct,It must Img');
                return false
            }
        })
        $(document).on('click','.ad-review .review-content .re_video .clickdiv',function(){
            $(this).siblings('iframe').toggleClass('isclick');
            $(this).toggleClass('isclick');
        });
    });
});