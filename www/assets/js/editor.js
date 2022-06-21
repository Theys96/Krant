/* Helper functions when writing */

function insertChar(button) {
    document.getElementById('text').value = document.getElementById('text').value + button.value;
}

function charCounter(text, counter) {
	$(text).on('keyup', function() {
		$(counter).text(this.value.length + " tekens");
	});
	$(text).trigger('keyup');
}
