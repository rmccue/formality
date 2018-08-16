export default function(name, dot = true, child = "") {
	const el = {
		form: "formality",
		field: "formality__field",
		field_focus: "formality__field--focus",
		input: "formality__input",
		inputs: "formality__input input",
		textarea: "formality__input textarea",
	}
	if(dot) {
		return "." + el[name] + " " + child
	} else {
		return el[name] + " " + child
	}
}