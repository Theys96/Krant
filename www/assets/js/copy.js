/* helper function for copying text */

let lastTimeout = null;

function copyText(element, message) {
    navigator.clipboard.writeText($(element).text());

    $('#copy-message').text(message);
    $('#copy-message').fadeIn();
    if (lastTimeout) {
        clearTimeout(lastTimeout);
    }
    lastTimeout = setTimeout(() => {
        $('#copy-message').fadeOut();
    }, 2000);
}
