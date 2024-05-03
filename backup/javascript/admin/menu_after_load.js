$(document).ready(() =>
{
      const location = window.location.pathname;

      if (location.includes('/admin/book') || location.includes('/admin/customer') || location.includes('/admin/coupon') || location.includes('/admin/category') || location.includes('/admin/request'))
      {
            $('#manage_dropdown_0').addClass('text-primary');
            if (window.innerWidth >= 992)
                  $('#manage_dropdown_0').addClass('border-bottom border-3 border-primary');
            $('#Layer_1').attr('fill', '#007bff').attr('stroke', '#007bff');
            if (location.includes('/admin/book')) $('#manage_dropdown_1').addClass('text-primary');
            else if (location.includes('/admin/category')) $('#manage_dropdown_2').addClass('text-primary');
            else if (location.includes('/admin/customer')) $('#manage_dropdown_3').addClass('text-primary');
            else if (location.includes('/admin/coupon')) $('#manage_dropdown_4').addClass('text-primary');
            else if (location.includes('/admin/request')) $('#manage_dropdown_5').addClass('text-primary');
      }
      else if (location.includes('/admin/statistic'))
      {
            $('#statistic_nav').addClass('text-primary');
            if (window.innerWidth >= 992)
                  $('#statistic_nav').addClass('border-bottom border-3 border-primary');
      }
      else if (location.includes('/admin/account'))
      {
            $('#profile_nav').addClass('text-primary');
            if (window.innerWidth >= 992)
                  $('#profile_nav').addClass('border-bottom border-3 border-primary');
      }
      else if (location.includes('/admin/authentication') && !location.includes('/admin/authentication/recovery'))
      {
            $('#signin_nav').addClass('text-primary');
            if (window.innerWidth >= 992)
                  $('#signin_nav').addClass('border-bottom border-3 border-primary');
      }
      else if (location.includes('/admin/') && !location.includes('/admin/authentication/recovery') && !location.includes('/about-us') && !location.includes('/terms-of-service') && !location.includes('/discount-program') && !location.includes('/privacy-policy'))
      {
            $('#home_nav').addClass('text-primary');
            if (window.innerWidth >= 992)
                  $('#home_nav').addClass('border-bottom border-3 border-primary');
      }
});