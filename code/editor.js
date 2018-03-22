/* Helper functions when writing */

function insertChar(button) {
    document.getElementById('text').value = document.getElementById('text').value + button.value;
}

// Sets character counter of text in element 1 to element 2
function charCount(source, counter) {
	source.on('input', function() {
		counter.text(this.value.length + " tekens");
	})
	source.trigger('input');
}