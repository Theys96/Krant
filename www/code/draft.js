var Draft = {};

$(function() {

Draft = {
	server: 'server.php',
	draftID: null,

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
			action = 'createdraft';
			}
		else {
			action = 'updatedraft';
			}
		
		klaar = Draft.done.is(':checked') ? 1 : 0;
		
		/* Post action */
		console.log(action, Draft.draftID, Draft.title.val(), Draft.category.val(), Draft.text.val(), klaar);
		post = $.getJSON(Draft.server, {
			action: action,
			id: Draft.draftID,
			titel: Draft.title.val(),
			cat: Draft.category.val(),
			tekst: Draft.text.val(),
			klaar: klaar
		}).done(function(data) {
			/* Get a Draft ID  */
			if (data.draftID) {
				getID = data.draftID;
				if (getID != 0) {
					Draft.draftID = getID;
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
			$('#info').html(data.error + data.warning + data.message);
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

