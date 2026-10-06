(function () {
	var header = document.querySelector(".site-head");
	var nav = document.querySelector(".nav");
	var burger = document.querySelector(".burger");

	if (burger && nav) {
		burger.addEventListener("click", function () {
			var open = nav.classList.toggle("is-open");
			burger.setAttribute("aria-expanded", open ? "true" : "false");
		});

		nav.addEventListener("click", function (event) {
			if (!event.target.closest("a")) {
				return;
			}
			nav.classList.remove("is-open");
			burger.setAttribute("aria-expanded", "false");
		});

		document.addEventListener("keydown", function (event) {
			if (event.key === "Escape") {
				nav.classList.remove("is-open");
				burger.setAttribute("aria-expanded", "false");
			}
		});
	}

	if (header) {
		var onScroll = function () {
			header.classList.toggle("is-stuck", window.scrollY > 8);
		};
		onScroll();
		window.addEventListener("scroll", onScroll, { passive: true });
	}

	var nodes = document.querySelectorAll(".reveal");
	var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
	if (reduce || !("IntersectionObserver" in window)) {
		nodes.forEach(function (node) {
			node.classList.add("is-in");
		});
		return;
	}

	var observer = new IntersectionObserver(
		function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) {
					return;
				}
				entry.target.classList.add("is-in");
				observer.unobserve(entry.target);
			});
		},
		{ threshold: 0.01, rootMargin: "0px 0px 12% 0px" }
	);

	nodes.forEach(function (node) {
		observer.observe(node);
	});
})();


(function () {
	var form = document.querySelector(".kov-form");
	if (!form) {
		return;
	}

	var show = function (kind, text) {
		var note = form.querySelector(".note");
		if (!note) {
			note = document.createElement("p");
			note.className = "note";
			form.insertBefore(note, form.firstChild);
		}
		note.className = "note " + (kind === "ok" ? "note-ok" : "note-err");
		note.textContent = text;
	};

	form.addEventListener("submit", function (event) {
		event.preventDefault();
		var data = new FormData(form);
		if (String(data.get("company") || "").trim() !== "") {
			form.reset();
			show("ok", "Заявка принята. Перезвоним, уточним детали и назовём стоимость до выезда.");
			return;
		}

		var name = String(data.get("lead_name") || "").trim();
		var phone = String(data.get("lead_phone") || "").trim();
		var from = String(data.get("lead_from") || "").trim();
		var to = String(data.get("lead_to") || "").trim();
		var note = String(data.get("lead_note") || "").trim();
		var digits = phone.replace(/\D+/g, "");

		if (name.length < 2 || digits.length < 10 || from.length < 3 || to.length < 3 || note.length < 3 || !data.get("lead_consent")) {
			show("err", "Проверьте имя, телефон, адреса и согласие на обработку данных.");
			return;
		}

		var body = [
			"Имя: " + name,
			"Телефон: " + phone,
			"Откуда: " + from,
			"Куда: " + to,
			"Ситуация: " + note
		].join("\n");
		window.location.href = "mailto:Vladkoval314@gmail.com?subject=" + encodeURIComponent("Заявка: эвакуатор") + "&body=" + encodeURIComponent(body);
		form.reset();
		show("ok", "Заявка принята. Перезвоним, уточним детали и назовём стоимость до выезда.");
	});
})();
