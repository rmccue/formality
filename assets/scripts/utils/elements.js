export default function(name, dot = true, child = "") {
	const el = {
		form: "formality",
		section: "formality__section",
		section_header: "formality__section__header",
		field: "formality__field",
		field_focus: "formality__field--focus",
		field_filled: "formality__field--filled",
		field_required: "formality__field--required",
		field_error: "formality__field--error",
		field_success: "formality__field--success",
		input: "formality__input",
		input_errors: "formality__input__errors",
		nav: "formality__nav",
		nav_list: "formality__nav__list",
		nav_section: "formality__nav__section",
		nav_legend: "formality__nav__legend",
		button: "formality__btn",
		submit: "formality__btn--submit",
	}
	if(dot) {
		return "." + el[name] + child
	} else {
		return el[name] + child
	}
}