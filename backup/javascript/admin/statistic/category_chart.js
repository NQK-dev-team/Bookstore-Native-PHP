$(document).ready(function ()
{
      getCategoryChart();
});

let categoryChart = null;

function getCategoryChart()
{
      const start = encodeData($('#startDateInput').val());
      const end = encodeData($('#endDateInput').val());

      {
            const startDate = new Date(start);
            const endDate = new Date(end);
            const today = new Date();
            startDate.setHours(0, 0, 0, 0);
            endDate.setHours(0, 0, 0, 0);
            today.setHours(0, 0, 0, 0);

            if (!start)
            {
                  $('#errorModal').modal('show');
                  $('#error_message').text('Missing start date!');
                  return;
            }
            else if (startDate > today)
            {
                  $('#errorModal').modal('show');
                  $('#error_message').text('Start date must be before or the same day as today!');
                  return;
            }

            if (!end)
            {
                  $('#errorModal').modal('show');
                  $('#error_message').text('Missing end date!');
                  return;
            }
            else if (endDate > today)
            {
                  $('#errorModal').modal('show');
                  $('#error_message').text('End date must be before or the same day as today!');
                  return;
            }

            if (startDate > endDate)
            {
                  $('#errorModal').modal('show');
                  $('#error_message').text('Start date must be before or the same day as end date!');
                  return;
            }
      }

      const labels = [];
      const chart_data = [];
      let color = { backgroundColor: [], borderColor: [] };

      $.ajax({
            url: '/ajax_service/admin/statistic/get_category_sale.php',
            method: 'GET',
            dataType: 'json',
            data: { start, end },
            success: function (data)
            {
                  if (data.error)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(data.error);
                  }
                  else if (data.query_result)
                  {
                        for (let i = 0; i < data.query_result.length; i++)
                        {
                              labels.push(data.query_result[i].name);
                              chart_data.push(data.query_result[i].finalTotalSold);
                        }
                        color = generateColorPairs(data.query_result.length);

                        const config_data = {
                              labels: labels,
                              datasets: [{
                                    label: 'Total sold',
                                    data: chart_data,
                                    backgroundColor: color.backgroundColor,
                                    borderColor: color.borderColor,
                                    borderWidth: 1
                              }]
                        };

                        const config = {
                              type: 'bar',
                              data: config_data,
                              options: {
                                    scales: {
                                          y: {
                                                beginAtZero: true,
                                                title: {
                                                      display: true,
                                                      text: 'Total books sold',
                                                      font: {
                                                            size: 16
                                                      },
                                                      color: 'black'
                                                }
                                          },
                                          x: {
                                                title: {
                                                      display: true,
                                                      text: 'Categories',
                                                      font: {
                                                            size: 16
                                                      },
                                                      color: 'black'
                                                }
                                          }
                                    },
                                    plugins: {
                                          legend: {
                                                display: false
                                          },
                                          zoom: {
                                                pan: {
                                                      enabled: true,
                                                      mode: 'x'
                                                },
                                                zoom: {
                                                      wheel: {
                                                            enabled: true,
                                                      },
                                                      pinch: {
                                                            enabled: true
                                                      },
                                                      mode: 'x',
                                                }
                                          }
                                    }
                              },
                        };

                        if (categoryChart) categoryChart.destroy();

                        categoryChart = new Chart(
                              document.getElementById('category_chart'),
                              config
                        );
                  }
            },

            error: function (err)
            {
                  
                  if (err.status >= 500)
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text('Server encountered error!');
                  } else
                  {
                        $('#errorModal').modal('show');
                        $('#error_message').text(err.responseJSON.error);
                  }
            }
      });
}