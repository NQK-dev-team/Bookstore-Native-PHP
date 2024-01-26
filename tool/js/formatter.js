function formatISBN(isbn)
{
      // Remove any existing hyphens or spaces
      isbn = isbn.replace(/[-\s]/g, '');

      // Check if the ISBN is 13 digits long
      if (isbn.length === 13)
      {
            // Format as 3-1-2-6-1
            return isbn.slice(0, 3) + '-' + isbn.slice(3, 4) + '-' + isbn.slice(4, 6) + '-' + isbn.slice(6, 12) + '-' + isbn.slice(12, 13);
      }

      // Invalid ISBN number
      return "Invalid ISBN number";
}