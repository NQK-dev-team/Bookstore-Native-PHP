function sanitize(param)
{
      if (typeof param !== 'string') return param;

      // Remove leading and trailing whitespaces
      param = param.trim();
      // Remove backslashes
      param = param.replace(/\\/g, '');
      // Convert special characters to HTML entities
      param = param.replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');

      return param;
}