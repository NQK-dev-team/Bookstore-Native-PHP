$(document).ready(function ()
{
      const labels = [];
      const chart_data = [];
      let color = { backgroundColor: [], borderColor: [] };

      $.ajax({
            url: '/ajax_service/admin/home/get_best_category.php',
            method: 'GET',
            dataType: 'json',
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
                                    responsive: true,
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

                        new Chart(
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
});