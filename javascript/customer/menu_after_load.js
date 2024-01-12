$(document).ready(() =>
{
      const location = window.location.href;

      if (location.includes('/book'))
            $('#book_nav').addClass('text-primary');
      else if (location.includes('/wishlist'))
            $('#wishlist_nav').addClass('text-primary');
      else if (location.includes('/cart'))
            $('#cart_nav').addClass('text-primary');
      else if (location.includes('/account'))
            $('#profile_nav').addClass('text-primary');
      else if (location.includes('/authentication') && !location.includes('recovery') && !location.includes('sign_up'))
            $('#signin_nav').addClass('text-primary');
      else if (location.includes('/') && !location.includes('recovery') && !location.includes('sign_up'))
            $('#home_nav').addClass('text-primary');
});