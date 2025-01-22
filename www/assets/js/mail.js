function sendMail(){
    var mail = document.getElementById('mailbtn').value
    var title = document.getElementById('title').value
    var author = document.getElementById('user').value
    document.getElementById('context').value = 'Foto(s) door ' + author + ' gemaild\n' + document.getElementById('context').value;
    window.location.href = 'mailto:' + mail + '?subject= FOTO ' + title + ', AUTEUR ' + author; 

}