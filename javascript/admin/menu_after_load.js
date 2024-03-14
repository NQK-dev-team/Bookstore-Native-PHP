$(document).ready(() =>
{
      const location = window.location.href;

      if (location.includes('/admin/book') || location.includes('/admin/customer') || location.includes('/admin/coupon') || location.includes('/admin/category'))
      {
            $('#manage_dropdown_0').addClass('text-primary');
            if (location.includes('/admin/book')) $('#manage_dropdown_1').addClass('text-primary');
            else if (location.includes('/admin/category')) $('#manage_dropdown_2').addClass('text-primary');
            else if (location.includes('/admin/customer')) $('#manage_dropdown_3').addClass('text-primary');
            else if (location.includes('/admin/coupon')) $('#manage_dropdown_4').addClass('text-primary');
      }
      else if (location.includes('/admin/statistic'))
            $('#statistic_nav').addClass('text-primary');
      else if (location.includes('/account'))
            $('#profile_nav').addClass('text-primary');
      else if (location.includes('/policy'))
            $('#policy_nav').addClass('text-primary');
      else if (location.includes('/authentication') && !location.includes('recovery'))
            $('#signin_nav').addClass('text-primary');
      else if (location.includes('/') && !location.includes('recovery'))
            $('#home_nav').addClass('text-primary');
});