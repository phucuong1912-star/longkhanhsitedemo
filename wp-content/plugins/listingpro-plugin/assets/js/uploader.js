jQuery(document).ready(function() {

    var abc = 1; // Declaring and defining global increment variable.
    if (jQuery('.lp-img-gall-upload-section').length > 0) {

        jQuery('body').on('change', '.file', function(event) {
            var files = event.target.files; //FileList object
            var output = document.getElementsByClassName("filediv");
            output = output[0];

            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                //Only pics
                if (!file.type.match('image'))
                    continue;

                var picReader = new FileReader();

                picReader.addEventListener("load", function(event) {

                    var picFile = event.target;

                    var div = document.createElement("ul");
                    div.className = 'jFiler-items-list jFiler-items-grid grid' + i;
                    div.innerHTML = '<li class="jFiler-item">\
                        <div class="jFiler-item-container">\
                            <div class="jFiler-item-inner">\
                                <div class="jFiler-item-thumb">\
                                    <img  class="thumbnail" alt="image" src="' + picFile.result + '" title="' + picFile.name + '"/>\
                                </div>\
                            </div>\
                        </div><a class="icon-jfi-trash jFiler-item-trash-action"><i class="fa fa-trash"></i></a>\
                    </li>';

                    output.insertBefore(div, null);

                });

                //Read the image
                picReader.readAsDataURL(file);

            }
            jQuery('.jFiler-item-trash-action').on('click', function() {
                jQuery(this).parent().parent().parent().remove();
            });
            jQuery(output).find('input').hide();
            jQuery(output).before(jQuery("<div/>", {
                class: 'filediv'
            }).fadeIn('slow').append(jQuery("<input/>", {
                name: 'listingfiles[]',
                type: 'file',
                class: 'file',
                multiple: 'multiple'
            })));
        });
        // To Preview Image
        function imageIsLoaded(e) {
            jQuery('#previewimg' + abc).attr('src', e.target.result);
        };
    }
});

function initialize() {
    if (jQuery('#inputAddress').length) {
        var input = document.getElementById('inputAddress');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace();
            document.getElementById('latitude').value = place.geometry.location.lat();
            document.getElementById('longitude').value = place.geometry.location.lng();
        });
    }
}
google.maps.event.addDomListener(window, 'load', initialize);