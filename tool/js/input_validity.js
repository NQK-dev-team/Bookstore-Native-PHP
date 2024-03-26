function clearAllCustomValidity()
{
      $('input').each(function (index, element)
      {
            element.setCustomValidity('');
      });
}

function clearCustomValidity(elem)
{
      elem.setCustomValidity('');

}

function reportCustomValidity(elem, message)
{
      elem.setCustomValidity(message);
      elem.reportValidity();
}

$(document).ready(function ()
{
      $('input, textarea, select').on('input change', function ()
      {
            this.setCustomValidity('');
      });
});