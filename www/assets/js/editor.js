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

function sendMail(){
    var mail = document.getElementById('mailbtn').value
    var title = document.getElementById('title').value
    var author = document.getElementById('user').value
    document.getElementById('context').value = 'Foto(s) door ' + author + ' gemaild\n' + document.getElementById('context').value;
    window.location.href = 'mailto:' + mail + '?subject= FOTO ' + title + ', AUTEUR ' + author + '&body=Voeg je foto(s) toe!'; 

}
