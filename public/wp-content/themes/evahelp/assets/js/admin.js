jQuery(function ($) {
	var frame;

	$(document).on("click", ".kov-pick-image", function (event) {
		event.preventDefault();
		var button = $(this);
		var target = $("#" + button.data("target"));
		var wrap = button.closest(".kov-image-field");

		frame = wp.media({
			title: "Выберите изображение",
			button: { text: "Использовать" },
			multiple: false,
			library: { type: "image" }
		});

		frame.on("select", function () {
			var attachment = frame.state().get("selection").first().toJSON();
			target.val(attachment.id);
			var src = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
			wrap.find("img").attr("src", src);
		});

		frame.open();
	});

	$(document).on("click", ".kov-clear-image", function (event) {
		event.preventDefault();
		var wrap = $(this).closest(".kov-image-field");
		wrap.find("input[type='hidden']").val("0");
		wrap.find("img").attr("src", wrap.find("img").data("fallback"));
	});
});
