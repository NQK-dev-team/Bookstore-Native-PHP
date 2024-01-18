function sanitize(param)
{
      if (typeof param !== 'string') return param;

      // Remove leading and trailing whitespaces
      param = param.trim();

      // Replace special characters
      param = encodeURIComponent(param);

      // Encode '
      param = param.replace(/'/g, '%27');

      // Decode @
      param = param.replace(/%40/g, '@');

      return param;
}