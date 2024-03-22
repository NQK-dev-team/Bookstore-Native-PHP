let physicalBook = [], fileBook = [];
let deleteID = null, refreshList = null;

$(document).ready(function ()
{
      $('#errorModal').on('hidden.bs.modal', function ()
      {
            $('#error_message').text('');
      });

      $("#cartForm").submit(function (e)
      {
            e.preventDefault();

            $('#paymentModal').modal('show');
      });

      $('#deleteModal').on('hidden.bs.modal', function ()
      {
            deleteID = null;
            refreshList = null;
      });

      fetchFileOrder();

      fetchPhysicalOrder();
});

function fetchFileOrder()
{
      $.ajax({
            url: '/ajax_service/customer/cart/get_file_order.php',
            method: 'GET',
            headers: {
                  'X-CSRF-Token': CSRF_TOKEN
            },
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
                        if (data.query_result.detail.length)
                        {
                              $('#fileCopyTitle').css('display', 'block');
                              $('#fileList').css('display', 'block');
                              $('#fileList').empty();

                              let temp = '';
                              for (let i = 0; i < data.query_result.detail.length - 1; i++)
                              {
                                    temp += `<div class='row my-1'>
                                    <div class='col-lg-2 col-md-4 col-12 d-flex'>
                                          <a href="#" class='my-auto mx-auto'>
                                                <img src="${ data.query_result.detail[i].imagePath }" class='book_image'>
                                          </a>
                                    </div>
                                    <div class='col'>
                                          <div class='d-flex flex-column justify-content-center px-5 mt-3'>
                                                <strong class='fs-5 text-md-start text-center'>${ data.query_result.detail[i].name }</strong>
                                                <strong class='text-md-start text-center'>${ data.query_result.detail[i].edition } edition</strong>
                                                <div class='fs-5 text-md-start text-center'>
                                                      <p class='mb-0'>$${ data.query_result.detail[i].price }</p>
                                                      <div class='d-flex justify-content-center justify-content-md-start'>
                                                            <p>$200</p>
                                                            <div class='d-flex ms-2'>
                                                                  <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                        <g id="SVGRepo_iconCarrier">
                                                                              <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                        </g>
                                                                  </svg>
                                                                  <p class='ms-1 text-danger'>30%</p>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                                    <div class='col-lg-2 col-12 d-flex'></div>
                                    <div class='col-lg-2 col-12'></div>
                                    <div class='col-lg-1 col-12 d-flex'>
                                          <i onclick='openDeleteModal("${ data.query_result.detail[i].id }",1)' class="bi bi-trash3-fill my-lg-auto fs-4 pointer text-danger mx-lg-0 mx-auto"></i>
                                    </div>
                              </div>
                              <hr class='my-2'>`;
                              }

                              temp += `<div class='row my-1'>
                                    <div class='col-lg-2 col-md-4 col-12 d-flex'>
                                          <a href="#" class='my-auto mx-auto'>
                                                <img src="${ data.query_result.detail[data.query_result.detail.length - 1].imagePath }" class='book_image'>
                                          </a>
                                    </div>
                                    <div class='col'>
                                          <div class='d-flex flex-column justify-content-center px-5 mt-3'>
                                                <strong class='fs-5 text-md-start text-center'>${ data.query_result.detail[data.query_result.detail.length - 1].name }</strong>
                                                <strong class='text-md-start text-center'>${ data.query_result.detail[data.query_result.detail.length - 1].edition } edition</strong>
                                                <div class='fs-5 text-md-start text-center'>
                                                      <p class='mb-0'>$${ data.query_result.detail[data.query_result.detail.length - 1].price }</p>
                                                      <div class='d-flex justify-content-center justify-content-md-start'>
                                                            <p>$200</p>
                                                            <div class='d-flex ms-2'>
                                                                  <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                        <g id="SVGRepo_iconCarrier">
                                                                              <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                        </g>
                                                                  </svg>
                                                                  <p class='ms-1 text-danger'>30%</p>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                                    <div class='col-lg-2 col-12 d-flex'></div>
                                    <div class='col-lg-2 col-12'></div>
                                    <div class='col-lg-1 col-12 d-flex'>
                                          <i onclick='openDeleteModal("${ data.query_result.detail[data.query_result.detail.length - 1].id }",1)' class="bi bi-trash3-fill my-lg-auto fs-4 pointer text-danger mx-lg-0 mx-auto"></i>
                                    </div>
                              </div>`;

                              $('#fileList').append(temp);
                        }
                        else
                        {
                              $('#fileCopyTitle').css('display', 'none');
                              $('#fileList').css('display', 'none');
                        }
                  }
            },

            error: function (err)
            {
                  console.error(err);
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

function fetchPhysicalOrder()
{
      $.ajax({
            url: '/ajax_service/customer/cart/get_physical_order.php',
            method: 'GET',
            headers: {
                  'X-CSRF-Token': CSRF_TOKEN
            },
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
                        if (data.query_result.detail.length)
                        {
                              $('#physicalCopyTitle').css('display', 'block');
                              $('#physicalList').css('display', 'block');
                              $('#physicalList').empty();

                              let temp = '';
                              for (let i = 0; i < data.query_result.detail.length - 1; i++)
                              {
                                    temp += `<div class='row my-1'>
                                    <div class='col-lg-2 col-md-4 col-12 d-flex'>
                                          <a href="#" class='my-auto mx-auto'>
                                                <img src="${ data.query_result.detail[i].imagePath }" class='book_image'>
                                          </a>
                                    </div>
                                    <div class='col'>
                                          <div class='d-flex flex-column justify-content-center px-5 mt-3'>
                                                <strong class='fs-5 text-md-start text-center'>${ data.query_result.detail[i].name }</strong>
                                                <strong class='text-md-start text-center'>${ data.query_result.detail[i].edition } edition</strong>
                                                <div class='fs-5 text-md-start text-center'>
                                                      <p class='mb-0'>$${ data.query_result.detail[i].price }</p>
                                                      <div class='d-flex justify-content-center justify-content-md-start'>
                                                            <p>$200</p>
                                                            <div class='d-flex ms-2'>
                                                                  <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                        <g id="SVGRepo_iconCarrier">
                                                                              <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                        </g>
                                                                  </svg>
                                                                  <p class='ms-1 text-danger'>30%</p>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                                    <div class='col-lg-2 col-12 d-flex'>
                                          <div class="btn-group my-lg-auto mx-lg-0 mx-auto mt-2" role="group">
                                                <input onclick='adjustAmount(false,"${ data.query_result.detail[i].id }")' type="button" class="btn-check" id="decrease_book_ammount_${ data.query_result.detail[i].id }" autocomplete="off">
                                                <label class="btn btn-outline-danger" for="decrease_book_ammount_${ data.query_result.detail[i].id }">-</label>

                                                <input onchange='checkAmmount("${ data.query_result.detail[i].id }")' type="number" class="fw-bold ammount_input ps-2" id="book_ammount_${ data.query_result.detail[i].id }" autocomplete="off" value="${ data.query_result.detail[i].amount }" min="1" max="${ data.query_result.detail[i].inStock }">

                                                <input onclick='adjustAmount(true,"${ data.query_result.detail[i].id }")' type="button" class="btn-check" id="increase_book_ammount_${ data.query_result.detail[i].id }" autocomplete="off">
                                                <label class="btn btn-outline-success" for="increase_book_ammount_${ data.query_result.detail[i].id }">+</label>
                                          </div>
                                    </div>
                                    <div class='col-lg-2 col-12'>
                                          <div class='w-100 h-100 d-flex justify-content-lg-start justify-content-center mt-lg-0 my-2'>
                                                <strong class='my-auto'>In stock:&nbsp;</strong>
                                                <strong class='my-auto' id='in_stock_${ data.query_result.detail[i].id }'>${ data.query_result.detail[i].inStock }</strong>
                                          </div>
                                    </div>
                                    <div class='col-lg-1 col-12 d-flex'>
                                          <i onclick='openDeleteModal("${ data.query_result.detail[i].id }",2)' class="bi bi-trash3-fill my-lg-auto fs-4 pointer text-danger mx-lg-0 mx-auto"></i>
                                    </div>
                              </div>
                              <hr class='my-2'>`;
                              }
                              temp += `<div class='row my-1'>
                                    <div class='col-lg-2 col-md-4 col-12 d-flex'>
                                          <a href="#" class='my-auto mx-auto'>
                                                <img src="${ data.query_result.detail[data.query_result.detail.length - 1].imagePath }" class='book_image'>
                                          </a>
                                    </div>
                                    <div class='col'>
                                          <div class='d-flex flex-column justify-content-center px-5 mt-3'>
                                                <strong class='fs-5 text-md-start text-center'>${ data.query_result.detail[data.query_result.detail.length - 1].name }</strong>
                                                <strong class='text-md-start text-center'>${ data.query_result.detail[data.query_result.detail.length - 1].edition } edition</strong>
                                                <div class='fs-5 text-md-start text-center'>
                                                      <p class='mb-0'>$${ data.query_result.detail[data.query_result.detail.length - 1].price }</p>
                                                      <div class='d-flex justify-content-center justify-content-md-start'>
                                                            <p>$200</p>
                                                            <div class='d-flex ms-2'>
                                                                  <svg width="32px" height="32px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ff0000">
                                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                                        <g id="SVGRepo_iconCarrier">
                                                                              <path d="M3.9889 14.6604L2.46891 13.1404C1.84891 12.5204 1.84891 11.5004 2.46891 10.8804L3.9889 9.36039C4.2489 9.10039 4.4589 8.59038 4.4589 8.23038V6.08036C4.4589 5.20036 5.1789 4.48038 6.0589 4.48038H8.2089C8.5689 4.48038 9.0789 4.27041 9.3389 4.01041L10.8589 2.49039C11.4789 1.87039 12.4989 1.87039 13.1189 2.49039L14.6389 4.01041C14.8989 4.27041 15.4089 4.48038 15.7689 4.48038H17.9189C18.7989 4.48038 19.5189 5.20036 19.5189 6.08036V8.23038C19.5189 8.59038 19.7289 9.10039 19.9889 9.36039L21.5089 10.8804C22.1289 11.5004 22.1289 12.5204 21.5089 13.1404L19.9889 14.6604C19.7289 14.9204 19.5189 15.4304 19.5189 15.7904V17.9403C19.5189 18.8203 18.7989 19.5404 17.9189 19.5404H15.7689C15.4089 19.5404 14.8989 19.7504 14.6389 20.0104L13.1189 21.5304C12.4989 22.1504 11.4789 22.1504 10.8589 21.5304L9.3389 20.0104C9.0789 19.7504 8.5689 19.5404 8.2089 19.5404H6.0589C5.1789 19.5404 4.4589 18.8203 4.4589 17.9403V15.7904C4.4589 15.4204 4.2489 14.9104 3.9889 14.6604Z" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M9 15L15 9" stroke="#ff0000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M14.4945 14.5H14.5035" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                              <path d="M9.49451 9.5H9.50349" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                        </g>
                                                                  </svg>
                                                                  <p class='ms-1 text-danger'>30%</p>
                                                            </div>
                                                      </div>
                                                </div>
                                          </div>
                                    </div>
                                    <div class='col-lg-2 col-12 d-flex'>
                                          <div class="btn-group my-lg-auto mx-lg-0 mx-auto mt-2" role="group">
                                                <input onclick='adjustAmount(false,"${ data.query_result.detail[data.query_result.detail.length - 1].id }")' type="button" class="btn-check" id="decrease_book_ammount_${ data.query_result.detail[data.query_result.detail.length - 1].id }" autocomplete="off">
                                                <label class="btn btn-outline-danger" for="decrease_book_ammount_${ data.query_result.detail[data.query_result.detail.length - 1].id }">-</label>

                                                <input onchange='checkAmmount("${ data.query_result.detail[data.query_result.detail.length - 1].id }")' type="number" class="fw-bold ammount_input ps-2" id="book_ammount_${ data.query_result.detail[data.query_result.detail.length - 1].id }" autocomplete="off" value="${ data.query_result.detail[data.query_result.detail.length - 1].amount }" min="1" max="${ data.query_result.detail[data.query_result.detail.length - 1].inStock }">

                                                <input onclick='adjustAmount(true,"${ data.query_result.detail[data.query_result.detail.length - 1].id }")' type="button" class="btn-check" id="increase_book_ammount_${ data.query_result.detail[data.query_result.detail.length - 1].id }" autocomplete="off">
                                                <label class="btn btn-outline-success" for="increase_book_ammount_${ data.query_result.detail[data.query_result.detail.length - 1].id }">+</label>
                                          </div>
                                    </div>
                                    <div class='col-lg-2 col-12'>
                                          <div class='w-100 h-100 d-flex justify-content-lg-start justify-content-center mt-lg-0 my-2'>
                                                <strong class='my-auto'>In stock:&nbsp;</strong>
                                                <strong class='my-auto' id='in_stock_${ data.query_result.detail[data.query_result.detail.length - 1].id }'>${ data.query_result.detail[data.query_result.detail.length - 1].inStock }</strong>
                                          </div>
                                    </div>
                                    <div class='col-lg-1 col-12 d-flex'>
                                          <i onclick='openDeleteModal("${ data.query_result.detail[data.query_result.detail.length - 1].id }",2)' class="bi bi-trash3-fill my-lg-auto fs-4 pointer text-danger mx-lg-0 mx-auto"></i>
                                    </div>
                              </div>`;

                              $('#physicalList').append(temp);
                        }
                        else
                        {
                              $('#physicalCopyTitle').css('display', 'none');
                              $('#physicalList').css('display', 'none');
                        }
                  }
            },

            error: function (err)
            {
                  console.error(err);
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

function openDeleteModal(id, type)
{
      deleteID = id;
      refreshList = type;
      $('#deleteModal').modal('show');
}

function removeBook()
{
      console.log(deleteID, refreshList);

      $('#deleteModal').modal('hide');
}

function adjustAmount(isIncrease, id)
{
      if (isIncrease)
            $(`#book_ammount_${ id }`).val(parseInt($(`#book_ammount_${ id }`).val()) + 1);
      else
            $(`#book_ammount_${ id }`).val(parseInt($(`#book_ammount_${ id }`).val()) - 1);

      checkAmmount(id);
}

function checkAmmount(id)
{
      clearAllCustomValidity();

      const amount = parseInt($(`#book_ammount_${ id }`).val());
      const inStock = parseInt($(`#in_stock_${ id }`).text());

      if (amount < 0)
      {
            reportCustomValidity($(`#book_ammount_${ id }`).get(0), "Book amount can not be negative!");
      } else if (amount === 0)
      {
            reportCustomValidity($(`#book_ammount_${ id }`).get(0), "Book amount can not be zero!");
      }
      else if (amount > inStock)
      {
            reportCustomValidity($(`#book_ammount_${ id }`).get(0), "Book amount exceeds in stock amount!");
      }

}