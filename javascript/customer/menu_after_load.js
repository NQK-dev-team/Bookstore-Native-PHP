$(document).ready(() =>
{
      const location = window.location.href;

      if (location.includes('/book'))
            $('#book_nav').addClass('text-primary border-bottom border-3 border-primary');
      else if (location.includes('/cart'))
            $('#cart_nav').addClass('text-primary border-bottom border-3 border-primary');
      else if (location.includes('/account'))
            $('#profile_nav').addClass('text-primary border-bottom border-3 border-primary');
      else if (location.includes('/authentication') && !location.includes('/authentication/recovery') && !location.includes('/authentication/sign-up'))
            $('#signin_nav').addClass('text-primary border-bottom border-3 border-primary');
      else if (location.includes('/') && !location.includes('/authentication/recovery') && !location.includes('/authentication/sign-up') && !location.includes('/about-us') && !location.includes('/terms-of-service') && !location.includes('/discount-program') && !location.includes('/privacy-policy'))
            $('#home_nav').addClass('text-primary border-bottom border-3 border-primary');
});