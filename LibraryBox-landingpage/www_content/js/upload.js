var $j = $ ? $ : $j;

$j(document).ready(function() {
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

    $('#js-fileInput').on('change', function(e) {
        if (e.target.files.length === 0) {
            return;
        }
        var file = e.target.files[0];
        var name = file.name;

        var url = window.location.href;
        var splitUrl = url.split('?');
        var query = splitUrl[1];
        var splitQuery = query
            .split('&')
            .filter(function(q) {
                var trimmedStr = q.trim();
                if (q[0] === 'p') {
                    return true;
                } else {
                    return false;
                }
            });
        var rawPath = splitQuery[0].split('=')[1];
        var path = decodeURIComponent(rawPath);
        var fullPath = path + name;
        // console.log(fullPath);

        var request = new XMLHttpRequest();
        request.addEventListener('load', function(res) {
            const fileExists = res.target.responseText;
            if (fileExists === 'true') {
                $('#js-overwriteWarning').css('display', 'block');
                $('#js-submitButton').addClass('btn-danger');
                $('#js-submitButton').removeClass('btn-primary');
            }
        });
        request.addEventListener('error', function(err) {
            console.log(err);
        });
        request.open('POST', 'check-file.php?p=' + encodeURIComponent(fullPath));
        request.send();

    });

    var fileLinkMouseDown = false;
    var fileLinkMouseTimeout;

    $('.js-fileLink').on('mousedown', function(e) {
        e.preventDefault();
        fileLinkMouseDown = true;
        fileLinkMouseTimeout = setTimeout(function() {
            var messageId = $(e.currentTarget).attr('dataMessageId');
            var message = $('#' + messageId).text();
            var href = $(e.currentTarget).attr('href');
            // console.log('href is', href);
            var splitHref;
            if (/DL_URL=/.test(href)) {
                splitHref = href.split('DL_URL=');
                href = splitHref[splitHref.length - 1];
            } else if (!/\//.test(href)) {
                href = window.location.pathname + href;
            }
            // console.log('href is', href);
            var splitHREF = href
                .trim()
                .split('/')
                .filter(function(str) {
                    return str ? true : false;
                });
            var confirmed = confirm(message + '\n\n' + decodeURIComponent(splitHREF[splitHREF.length - 1]));
            if (confirmed) {

                var request = new XMLHttpRequest();
                request.addEventListener('load', function(res) {
                    window.location.reload();
                });
                request.addEventListener('error', function(err) {
                    console.log(err);
                });
                request.open('POST', '/content/delete-file.php?p=' + href);
                request.send();

            }
        }, 1000);
    });

    $('body').on('mouseup', function(e) {
        e.preventDefault();
        fileLinkMouseDown = false;
        clearTimeout(fileLinkMouseTimeout);
    });

    $('#js-fileUploadForm').on('submit', function(e) {

        var text = $('#js-uploadingMessage').text();

        sweetAlert({
            title: text,
            html: true,
            text: '<div style="text-align:center;font-size:30px;"><i class="fa fa-spinner fa-spin"></i></div>',
            showConfirmButton: false,
            allowEscapeKey: false
        });
    });

});