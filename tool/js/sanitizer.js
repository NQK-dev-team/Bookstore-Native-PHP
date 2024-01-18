function sanitize(param)
{
      if (typeof param !== 'string') return param;

      // Remove leading and trailing whitespaces
      param = param.trim();

      // Replace special characters
      param = encodeURIComponent(param);

      // Encode '
      param = param.replace(/'/g, '%27');

      return param;
}