/**
 * Format CakePHP errors into Vee-Validate structure
 */
export const formatErrors = (value) => {
	const errors = [];

	Object.keys(value).forEach((field) => {
		errors[field] = Object.values(value[field]);
	});

	return errors;
};

/**
 * Return the state the form control should be in for validation
 * (null = normal, true/false = green/red)
 */
export const getValidationState = ({ dirty, validated, valid = null }) => {
	if (valid === true) {
		return null;
	}

	return dirty || validated ? valid : null;
};
