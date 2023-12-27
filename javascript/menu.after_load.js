$(document).ready(() =>
{
      const location = window.location.href;
      if (location.includes('/home'))
            $('#home_nav').css('color', 'rgb(68 121 255)');
      if (location.includes('/book'))
            $('#book_nav').css('color', 'rgb(68 121 255)');
      if (location.includes('/wishlist'))
            $('#wishlist_nav').css('color', 'rgb(68 121 255)');
      if (location.includes('/cart'))
            $('#cart_nav').css('color', 'rgb(68 121 255)');
});