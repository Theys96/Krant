var Draft = {};

$(function() {

Draft = {
	server: 'api.php',
	draftID: null,
	article_id: $('#article_id').val(),

	title: $($('[name="title"]')[0]),
	category: $($('[name="category"]')[0]),
	text: $($('[name="text"]')[0]),
	done: $($('[name="done"]')[0]),
	draftIDinput: $($('#draftid')[0]),

	/* Start drafting */
	init: function(inputSelector) {
		$(inputSelector).on('input', function() {
			Draft.draft();
			setInterval(function() {
				Draft.draft();
			}, 10000);
			$(inputSelector).off('input');
		});
	},

	/* Post draft naar server */
	postDraft: function() {	
		/* Determine action */
		var action;
		if (Draft.draftID == null) {
			action = 'new_draft';
			}
		else {
			action = 'update_draft';
			}
		
		klaar = Draft.done.is(':checked') ? 1 : 0;
		
		/* Post action */
		console.log(action, Draft.draftID, Draft.title.val(), Draft.category.val(), Draft.text.val(), klaar);
		console.log(Draft);
		post = $.getJSON(Draft.server, {
			action: action,
			article_id: Draft.article_id === '' ? null : Draft.article_id,
			draft_id: Draft.draftID,
			title: Draft.title.val(),
			category_id: Draft.category.val(),
			contents: Draft.text.val(),
			ready: klaar
		}).done(function(data) {
			/* Get a Draft ID  */
			if (data.draft_id) {
				if (data.draft_id) {
					Draft.draftID = data.draft_id;
				}
			}
		});
		return post;
	},

	/* Update draft, update info */
	draft: function() {
		post = Draft.postDraft();
		post.done(function(data) {
			time = new Date();
			if (data.warning) {
				$('#info').html('<span style="color: red; font-size: 15px;">' + data.warning + '</span>');
			}
		});
		post.fail(function() {
			$('#info').html('<span style="color: red; font-size: 15px;">Verbinding met de server verloren!</span>');
		});
	},

	/* Plaats de draft (stuur naar de server) */
	plaats: function(form) {
		/* Is er al een draft? */
		if (Draft.draftID == null) {
			/* Nee, maak er een */
			post = Draft.postDraft();
			post.done(function() {
				Draft.plaats(form);
				});
			return false;
		} else {
			/* Ja, update deze en plaats */
			Draft.draftIDinput.val(Draft.draftID);
			post = Draft.postDraft();
			post.done(function() {
				form.submit();
				});
			return false;
		}
	}
}

});

Date.prototype.timeNow = function () {
     return ((this.getHours() < 10)?"0":"") + this.getHours() +":"+ ((this.getMinutes() < 10)?"0":"") + this.getMinutes() +":"+ ((this.getSeconds() < 10)?"0":"") + this.getSeconds();
}

