

function showEmojiPopup(article_id) {
	$('emoji-picker').attr('data-article-id', article_id)
	$('.pop-up-bg').show();
	$('.emoji-pop-up').slideDown()
}

function hideEmojiPopup() {
	$('.emoji-pop-up').hide()
	$('.pop-up-bg').hide();
}

function renderEmojiReactions(reactions, element) {
	element.empty()
	for (let reaction of reactions) {
		element.append(
			`<div onClick="handleEmoji('${reaction.reaction}', ${element.data('article-id')})" class="emoji-reaction" data-toggle="tooltip" data-placement="top" data-original-title="${reaction.users.join(', ')}">
				<span>${reaction.reaction}</span>
				<span>${reaction.users.length > 1 ? reaction.users.length : ''}</span>
			</div>`)
	}
	element.append(
		`<div onClick="showEmojiPopup(${element.data('article-id')})" class="emoji-reaction" data-toggle="tooltip" data-placement="top" data-original-title="Reactie toevoegen">
			<span><b>+</b></span>
		</div>`)
	$('.tooltip').remove()
	$('[data-toggle="tooltip"]').tooltip()
}

function fetchEmojiReactions(article_id, element) {
	$.get('api.php', {
		'action': 'fetch_reactions',
		'article_id': article_id,
	}, function(data) {
		renderEmojiReactions(data, element)
	})
}

function handleEmoji(emoji, article_id) {
	$.get('api.php', {
		'action': 'add_reaction',
		'article_id': article_id,
		'reaction': emoji,
	}, function(data) {
		hideEmojiPopup()
		renderEmojiReactions(data, $('.emoji-reactions[data-article-id=' + article_id + ']'))
	})
}

$(function() {
	$('body').append('<div class="pop-up-bg"><emoji-picker class="emoji-pop-up light"></emoji-picker></div>');
	$('.emoji-reactions').each(function() {
		console.log($(this))
		fetchEmojiReactions($(this).data('article-id'), $(this))
	})
	$('.pop-up-bg').click(hideEmojiPopup)
	document.querySelector('emoji-picker')
		.addEventListener('emoji-click', event => {
			handleEmoji(event.detail.unicode, document.querySelector('emoji-picker').getAttribute('data-article-id'))
		});
	document.querySelector('emoji-picker')
		.addEventListener('click', (event) => {
			event.cancelBubble = true;
			event.stopPropagation();
		})
})

