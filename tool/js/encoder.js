function encodeData(param)
{
      if (typeof param !== 'string') return param;

      // Remove leading and trailing whitespaces
      param = param.trim();

      // Replace special characters
      param = encodeURIComponent(param);

      return param;
}