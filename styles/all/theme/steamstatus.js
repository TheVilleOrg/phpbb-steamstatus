(function ($) {

'use strict';

$(function() {
	$.steamstatusRefresh = function () {
		var steamids = {};
		$('.steamstatus').each(function () {
			var steamid = $(this).data('steamid');
			if (steamid) {
				steamids[steamid] = true;
			}
		});
		steamids = Object.keys(steamids);
		if (steamids.length > 0) {
			$.ajax({
				url: steamstatus.controller,
				dataType: 'json',
				cache: false,
				data: {'steamids': steamids.join(',')}
			})
			.done(function(data) {
				$.each(data.status, function(index, val) {
					var container = $('.steamstatus[data-steamid=' + val.steamid + ']');
					container.removeClass('steam-online').removeClass('steam-ingame');
					if (val.state === 1) {
						container.addClass('steam-online');
					} else if (val.state === 2) {
						container.addClass('steam-ingame');
					}
					container.children('img').attr({'src': val.avatar, 'alt': steamstatus.avatarAlt.replace('%s', val.name)});
					container.find('.steam-name').text(val.name).attr({'href': val.profile, 'title': val.name});
					container.find('.steam-status').text(val.status).attr('title', val.status);
					container.find('.steam-profile').attr({'href': val.profile, 'title': steamstatus.profileLinkTitle.replace('%s', val.name)});
					container.find('.steam-add').attr({'href': val.profile, 'title': steamstatus.AddLinkTitle.replace('%s', val.name)});
				});
			});
		}

		if (steamstatus.refresh > 0) {
			setTimeout($.steamstatusRefresh, steamstatus.refresh);
		}
	};
	$.steamstatusRefresh();
});

}(jQuery));
