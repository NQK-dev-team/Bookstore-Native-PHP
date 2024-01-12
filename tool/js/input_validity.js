function clearAllCustomValidity()
{
      console.log('called');
      $('input').each(function (index, element)
      {
            element.setCustomValidity('');
      });
}

function reportCustomValidity(elem, message)
{
      elem.setCustomValidity(message);
      elem.reportValidity();
}