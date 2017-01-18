var $j = $ ? $ : $j;

$j(document).ready(() => {
    var $ = $j ? $j : $;

    var transitioning = false;
    $('#js-showUploadOptions').on('click', function(e) {
        e.preventDefault();
        if (!transitioning) {
            transitioning = true;
            if ($('#js-newFileFolderButtonContainer').css('display') !== 'flex') {
                $('#js-newFileFolderButtonContainer').css('display', 'flex');
                setTimeout(function() {
                    $('#js-newFileFolderButtonContainer').css('opacity', '1');
                    transitioning = false;
                }, 0);
            } else {
                $('#js-newFileFolderButtonContainer').css('opacity', '0');
                setTimeout(function() {
                    $('#js-newFileFolderButtonContainer').css('display', 'none');
                    transitioning = false;
                }, 400);
            }
        }
    });

});