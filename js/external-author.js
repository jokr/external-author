(function (jQuery) {
	var ExternalAuthorPlugin = function (element, existing, input) {
		var elem = jQuery(element);
		var obj = this;
		var prototype = existing.first();
		var count = existing.length;
		var i = input;

		this.duplicatePrototype = function () {
			var newElement = prototype.clone();
			newElement.find('input.text').attr('value', '');

			newElement.find('label').each(function () {
				var e = jQuery(this);
				e.attr('for', e.attr('for').replace('0', count));
			});

			newElement.find('input.text').each(function () {
				var e = jQuery(this);
				e.attr('name', e.attr('name').replace('0', count));
			});

			newElement.find('.remove-author').click(obj.removeAuthor);

			i.append(newElement);
			count++;
		};

		this.removeAuthor = function () {
			jQuery(this).closest('.external-author').remove();
		};

		var checkbox = jQuery('#no-author');

		if (checkbox.is(':checked')) {
			jQuery('.maybe-disable').prop('disabled', true);
		}

		checkbox.change(function () {
			if (jQuery(this).is(':checked')) {
				jQuery('.maybe-disable').prop('disabled', true);
			} else {
				jQuery('.maybe-disable').prop('disabled', false);
			}
		});

		elem.find('input.featured').on('change', function () {
			if (jQuery(this).is(':checked')) {
				elem.find('input.featured').not(this).prop('checked', false);
			}
		});

		elem.find('#external-author-add-author').click(function () {
			obj.duplicatePrototype();
		});

		elem.find('.remove-author').click(obj.removeAuthor);
	};

	jQuery.fn.externalAuthor = function (proto, i) {
		return this.each(function () {
			var element = jQuery(this);
			if (element.data('externalAuthor')) return;
			var prototype = element.find(proto);
			var input = element.find(i);

			var externalAuthor = new ExternalAuthorPlugin(this, prototype, input);
			element.data('externalAuthor', externalAuthor);
		});
	}
})(jQuery);


jQuery(document).ready(function () {
	jQuery('#external-author-meta-box').externalAuthor('.external-author', '.external-author-input');
});