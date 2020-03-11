/**
 * Created by aman on 3/22/17.
 */


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

var imageFile = document.getElementById('file');
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

$('#confirm-add').click(function(ev) {
    if(typeof jcrop_api != 'undefined')
    {
        var dim = jcrop_api.getSelection();
        $('input[name=cropped_x]').val(dim.x);
        $('input[name=cropped_y]').val(dim.y);
        $('input[name=cropped_h]').val(dim.h);
        $('input[name=cropped_w]').val(dim.w);
    }
    $('#add-faculty-form').submit();

});
