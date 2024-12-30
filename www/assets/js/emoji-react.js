

function showEmojiPopup() {
	$('.pop-up-bg').show();
	$('.emoji-pop-up').slideDown()
}

function hideEmojiPopup() {
	$('.emoji-pop-up').hide()
	$('.pop-up-bg').hide();
}

function renderEmojiReactions(reactions) {
	$('#emoji-reactions').empty()
	for (let reaction of reactions) {
		$('#emoji-reactions').append(
			`<div onClick="handleEmoji('${reaction.reaction}')" class="emoji-reaction" data-toggle="tooltip" data-placement="top" data-original-title="${reaction.users.join(', ')}">
				<span>${reaction.reaction}</span>
				<span>${reaction.users.length > 1 ? reaction.users.length : ''}</span>
			</div>`)
	}
	$('.tooltip').remove()
	$('[data-toggle="tooltip"]').tooltip()
}

function fetchEmojiReactions() {
	$.get('api.php', {
		'action': 'fetch_reactions',
		'article_id': $('#article_id').val(),
	}, function(data) {
		renderEmojiReactions(data)
	})
}

function handleEmoji(emoji) {
	$.get('api.php', {
		'action': 'add_reaction',
		'article_id': $('#article_id').val(),
		'reaction': emoji,
	}, function(data) {
		hideEmojiPopup()
		renderEmojiReactions(data)
	})
}

$(function() {
	$('#emoji-button').click(showEmojiPopup)
	$('.pop-up-bg').click(hideEmojiPopup)
	document.querySelector('emoji-picker')
		.addEventListener('emoji-click', event => {
			handleEmoji(event.detail.unicode)
		});
	document.querySelector('emoji-picker')
		.addEventListener('click', (event) => {
			event.cancelBubble = true;
			event.stopPropagation();
		})
	fetchEmojiReactions()
})

