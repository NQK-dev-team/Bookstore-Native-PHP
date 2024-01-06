function sanitize(input)
{
      if (!input) return '';

      // Remove leading and trailing whitespaces
      let sanitizedInput = input.trim();
      // Remove backslashes
      sanitizedInput = sanitizedInput.replace(/\\/g, '');
      // Encode special characters
      sanitizedInput = encodeURIComponent(sanitizedInput);

      return sanitizedInput;
}