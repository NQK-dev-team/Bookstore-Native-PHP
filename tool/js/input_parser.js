function parseBool(value)
{
      if (typeof value === 'string')
      {
            // Convert string 'true' to true, 'false' to false
            return value.toLowerCase() === 'true';
      } else
      {
            // If it's not a string, use a truthy check
            return Boolean(value);
      }
}