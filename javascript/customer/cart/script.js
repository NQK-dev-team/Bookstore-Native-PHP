$(document).ready(function ()
{
      $('#errorModal').on('hidden.bs.modal', function ()
      {
            $('#error_message').text('');
      });

      $('#cartForm').on('submit', function (e)
      {
            e.preventDefault();
      });

      fetchFileOrder();

      fetchPhysicalOrder();
});

function fetchFileOrder()
{

}

function fetchPhysicalOrder()
{

}

function removeBook()
{

}

function adjustAmount(isIncrease)
{
      if (isIncrease)
      {

      }
      else
      {

      }
}