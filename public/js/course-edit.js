
var jcrop_api;

function showImage(src) {
    var fr=new FileReader();
    // when image is loaded, set the src of the image where you want to display it
    fr.onload = function(e) {
        image = new Image();
        image.src = e.target.result;
        image.id = "target";
        image.onload = restartJcrop;
        document.getElementById('views').appendChild(image);
    };
    fr.readAsDataURL(src.files[0]);
}

var imageFile = document.getElementById('course-image-upload');
imageFile.addEventListener('change', function() {
    $('#views').empty();
    showImage(this);
    restartJcrop();
});
function restartJcrop() {
    if (jcrop_api != null) {
        jcrop_api.destroy();
    }
    $('#target').Jcrop({
        setSelect: [100, 100, 200, 200],
        aspectRatio: 1,
        boxWidth: 500,
        boxHeight: 500
    }, function() {
        jcrop_api = this;
        thumbnail = this.initComponent('Thumbnailer', { width: 130, height: 130 });
    });
}

$('#confirm-edit').click(function(ev) {
    if(typeof jcrop_api != 'undefined')
    {
        var dim = jcrop_api.getSelection();
        $('input[name=cropped_x]').val(Math.round(dim.x));
        $('input[name=cropped_y]').val(Math.round(dim.y));
        $('input[name=cropped_h]').val(Math.round(dim.h));
        $('input[name=cropped_w]').val(Math.round(dim.w));
    }
    $('#edit-course-form').submit();

});
/**
 * Created by aman on 3/22/17.
 */
