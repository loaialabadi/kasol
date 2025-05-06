
@extends('dashboard.layouts.layout')
<!doctype html>
<html lang="en" data-bs-theme="blue-theme">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kasool</title>
  <!--favicon-->
	<link rel="icon" href="{{ asset('assets/images/favkasool.jpeg') }}" type="image/png">
  <!-- loader-->
	<link href="assets/css/pace.min.css" rel="stylesheet">
	<script src="assets/js/pace.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
  <!--plugins-->
  <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/metismenu/metisMenu.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/metismenu/mm-vertical.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}">

  <!-- Bootstrap CSS -->
  <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">

  <!-- Main CSS -->
  <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/main.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/css/horizontal-menu.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/dark-theme.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/blue-theme.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/semi-dark.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/bordered-theme.css') }}" rel="stylesheet">
  <link href="{{ asset('sass/responsive.css') }}" rel="stylesheet">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
      .alert {
          transition: opacity 1s ease-out;
          z-index: 999999999999999999999999;
      }
  </style>





</head>

<body>


    @if(session('success'))
    <div class="alert alert-success" id="success-message">
        {{ session('success') }}

    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" id="error-message">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

  <!--start main wrapper-->
  <main class="main-wrapper">
    <div class="main-content">
      <!--breadcrumb-->
      <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <!--<div class="breadcrumb-title pe-3">eCommerce</div>-->
        <div class="ps-3">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
              <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
              </li>
              <li class="breadcrumb-item active" aria-current="page">الطلبات</li>
            </ol>
          </nav>
        </div>
        <div class="ms-auto">
          {{--  <div class="btn-group">
            <button type="button" class="btn btn-primary">Settings</button>
            <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split"
              data-bs-toggle="dropdown"> <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end"> <a class="dropdown-item"
                href="javascript:;">Action</a>
              <a class="dropdown-item" href="javascript:;">Another action</a>
              <a class="dropdown-item" href="javascript:;">Something else here</a>
              <div class="dropdown-divider"></div> <a class="dropdown-item" href="javascript:;">Separated link</a>
            </div>
          </div>  --}}
        </div>
      </div>
      <!--end breadcrumb-->

      {{--  <div class="product-count d-flex align-items-center gap-3 gap-lg-4 mb-4 fw-medium flex-wrap font-text1">
        <a href="javascript:;"><span class="me-1">All</span><span class="text-secondary">(88754)</span></a>
        <a href="javascript:;"><span class="me-1">Published</span><span class="text-secondary">(56242)</span></a>
        <a href="javascript:;"><span class="me-1">Drafts</span><span class="text-secondary">(17)</span></a>
        <a href="javascript:;"><span class="me-1">On Discount</span><span class="text-secondary">(88754)</span></a>
      </div>  --}}

      <div style="justify-content: space-between;" class="row g-3">
        <div class="col-auto">
          <div class="position-relative">
            <form action="{{ route('search_orders') }}" method="GET" class="d-flex">
                <input class="form-control px-5" type="search" name="search" placeholder="بحث" value="{{ request('search') }}">
                <span class="material-icons-outlined position-absolute ms-3 translate-middle-y start-0 top-50 fs-5">بحث</span>

            </form>
          </div>
        </div>

        <div class="col-auto">
          <div class="d-flex align-items-center gap-2 justify-content-lg-end">
            {{--  <button class="btn btn-filter px-4"><i class="bi bi-box-arrow-right me-2"></i>Export</button>  --}}

            {{--  <button class="btn btn-primary px-4"><i class="bi bi-plus-lg me-2"></i>Add role</button>  --}}
          </div>
        </div>
      </div><!--end row-->

      <div class="card mt-4">
        <div class="card-body">
          <div class="product-table">
            <div class="table-responsive white-space-nowrap">
            <table class="table align-middle">
    <thead class="table-light">
        <tr>
            <th>اسم المستخدم</th>
            <th>اسم الخدمة</th>
            <th>التكلفة</th>
            <th>تكلفة الطلب</th>
            <th>طريقة الدفع</th>
            <th>حالة الطلب</th>
            <th>تغيير الحالة</th>
            <th>تفاصيل الطلب</th>
            <th>حالة التسليم</th>
            <th>الموصل المعين</th>
            <th>تعيين موصل</th>
            <th>التوصيل</th>
            <th>الإجراءات</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
            <tr>
                <td>{{ optional($order->user)->name ?? 'غير متوفر' }}</td>
                <td>{{ optional($order->service)->name ?? 'غير متوفر' }}</td>
                <td>{{ $order->delivery_cost ?? 'غير متوفر' }}</td>
                <td>{{ $order->total_price ?? 'غير متوفر' }}</td>
                <td>{{ $order->payment_method ?? 'غير متوفر' }}</td>
                <td>{{ $order->status ?? 'غير متوفر' }}</td>

                <td>
                    <button class="btn btn-primary"
                        onclick="openStatusUpdateModal('{{ $order->id }}', '{{ $order->status }}')">
                        تحديث الحالة
                    </button>
                </td>

                <td>
                    <a class="btn btn-primary" href="{{ route('order_details', ['id' => $order->id]) }}">
                        التفاصيل الكاملة
                    </a>
                </td>

                <td>
                    @if(optional($order->service)->reciving_method == 'pickup')
                        <p>التسليم من الفرع</p>
                    @else
                        <p>التوصيل من خلال دليفرى</p>
                    @endif
                </td>

                <td>
                    <p>{{ optional($order->delivery)->name ?? 'لم يتم التعيين' }}</p>
                </td>

                <td>
                    <a class="btn btn-primary" href="javascript:void(0);"
                        onclick='openDeliveriesModal(@json($deliveries), "{{ $order->id }}")'>
                        عرض التوصيلات
                    </a>
                </td>

                <td>
                    @if(optional($order->service)->has_delivery)
                        <p>لديه توصيل</p>
                    @else
                        <p>ليس لديه توصيل</p>
                    @endif
                </td>

                <td>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);" 
                                    onclick="openDeleteModal({{ $order->id }})">
                                    حذف
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0);"
                                    onclick="openOrderDetailsModal(
                                        '{{ $order->id }}',
                                        '{{ optional($order->user)->name ?? 'غير متوفر' }}',
                                        '{{ $order->total_price ?? 'غير متوفر' }}',
                                        '{{ $order->payment_method ?? 'غير متوفر' }}',
                                        '{{ $order->receiving_method ?? 'غير متوفر' }}',
                                        '{{ $order->shipping_address ?? 'غير متوفر' }}',
                                        '{{ $order->status ?? 'غير متوفر' }}',
                                        '{{ $order->order_notes ?? 'غير متوفر' }}',
                                    )">
                                    عرض التفاصيل
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


              <div class="pagination">
                {{ $orders->links('pagination::bootstrap-4') }}
            </div>
            </div>
          </div>
        </div>
      </div>



      <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">تفاصيل الطلب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>رقم الطلب:</strong> <span id="order_id"></span></p>
                    <p><strong>المستخدم:</strong> <span id="user_name"></span></p>
                    <p><strong>السعر الكلى:</strong> <span id="total_price"></span></p>
                    <p><strong>وسيله الدفع:</strong> <span id="payment_method"></span></p>
                    <p><strong>وسيله التوصيل:</strong> <span id="receiving_method"></span></p>
                    <p><strong>عنوان التوصيل:</strong> <span id="shipping_address"></span></p>
                    <p><strong>الحاله:</strong> <span id="order_status"></span></p>
                    <p><strong>ملاحظات الطلب:</strong> <span id="order_notes"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">غلق</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">تغيير حاله الطلب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="order_id">
                    <p><strong>Current Status:</strong> <span id="current_status"></span></p>
                    <label for="new_status" class="form-label">Select New Status</label>
                    <select id="new_status" class="form-control">
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="accepted">Accepted</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="updateOrderStatus()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

      <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="confirmDeleteModalLabel">تأكيد المسح</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              هل تريد حقا مسح هذا
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">لا</button>
              <button type="button" class="btn btn-danger" id="confirmDeleteButton">نعم
          </div>
        </div>
      </div>


    </div>
  </main>
  <!--end main wrapper-->





    <!--start overlay-->
    <div class="overlay btn-toggle"></div>
    <!--end overlay-->

     <!--start footer-->
     <footer class="page-footer">
      <p class="mb-0">Copyright © 2024. All right reserved.</p>
    </footer>
    <!--top footer-->

  <!--start cart-->
  <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCart">
    <div class="offcanvas-header border-bottom h-70">
      <h5 class="mb-0" id="offcanvasRightLabel">8 New Orders</h5>
      <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="offcanvas">
        <i class="material-icons-outlined">close</i>
      </a>
    </div>
    <div class="offcanvas-body p-0">
      <div class="order-list">
        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/01.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">White Men Shoes</h5>
            <p class="mb-0 order-price">$289</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>

        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/02.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">Red Airpods</h5>
            <p class="mb-0 order-price">$149</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>

        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/03.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">Men Polo Tshirt</h5>
            <p class="mb-0 order-price">$139</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>

        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/04.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">Blue Jeans Casual</h5>
            <p class="mb-0 order-price">$485</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>

        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/05.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">Fancy Shirts</h5>
            <p class="mb-0 order-price">$758</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>

        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/06.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">Home Sofa Set </h5>
            <p class="mb-0 order-price">$546</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>

        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/07.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">Black iPhone</h5>
            <p class="mb-0 order-price">$1049</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>

        <div class="order-item d-flex align-items-center gap-3 p-3 border-bottom">
          <div class="order-img">
            <img src="assets/images/orders/08.png" class="img-fluid rounded-3" width="75" alt="">
          </div>
          <div class="order-info flex-grow-1">
            <h5 class="mb-1 order-title">Goldan Watch</h5>
            <p class="mb-0 order-price">$689</p>
          </div>
          <div class="d-flex">
            <a class="order-delete"><span class="material-icons-outlined">delete</span></a>
            <a class="order-delete"><span class="material-icons-outlined">visibility</span></a>
          </div>
        </div>
      </div>
    </div>
    <div class="offcanvas-footer h-70 p-3 border-top">
      <div class="d-grid">
        <button type="button" class="btn btn-grd btn-grd-primary" data-bs-dismiss="offcanvas">View orders</button>
      </div>
    </div>
  </div>
  <!--end cart-->



  <!--start switcher-->
  {{--  <button class="btn btn-grd btn-grd-primary position-fixed bottom-0 end-0 m-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#staticBackdrop">
    <i class="material-icons-outlined">tune</i>Customize
  </button>  --}}

  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="staticBackdrop">
    <div class="offcanvas-header border-bottom h-70">
      <div class="">
        <h5 class="mb-0">Theme Customizer</h5>
        <p class="mb-0">Customize your theme</p>
      </div>
      <a href="javascript:;" class="primaery-menu-close" data-bs-dismiss="offcanvas">
        <i class="material-icons-outlined">close</i>
      </a>
    </div>
    <div class="offcanvas-body">
      <div>
        <p>Theme variation</p>

        <div class="row g-3">
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BlueTheme" checked>
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BlueTheme">
              <span class="material-icons-outlined">contactless</span>
              <span>Blue</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="LightTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="LightTheme">
              <span class="material-icons-outlined">light_mode</span>
              <span>Light</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="DarkTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="DarkTheme">
              <span class="material-icons-outlined">dark_mode</span>
              <span>Dark</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="SemiDarkTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="SemiDarkTheme">
              <span class="material-icons-outlined">contrast</span>
              <span>Semi Dark</span>
            </label>
          </div>
          <div class="col-12 col-xl-6">
            <input type="radio" class="btn-check" name="theme-options" id="BoderedTheme">
            <label class="btn btn-outline-secondary d-flex flex-column gap-1 align-items-center justify-content-center p-4" for="BoderedTheme">
              <span class="material-icons-outlined">border_style</span>
              <span>Bordered</span>
            </label>
          </div>
        </div><!--end row-->

      </div>
    </div>
  </div>
  <!--start switcher-->


  <div class="modal fade" id="deliveriesModal" tabindex="-1" aria-labelledby="deliveriesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deliveriesModalLabel">عمال التوصيل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                     <tr>
    <th>رقم التوصيل</th>
    <th>اسم الموصّل</th>
    <th>البريد الإلكتروني للموصّل</th>
    <th>رقم هاتف الموصّل</th>
    <th>تعيين</th>
</tr>

                    </thead>
                    <tbody id="deliveries_table_body">
                        <!-- Dynamic delivery rows will be inserted here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">غلق</button>
            </div>
        </div>
    </div>
</div>


  <!--bootstrap js-->
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

<!-- Plugins -->
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/plugins/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>


<script>
    $(document).ready(function() {
        // Check if there are success or error messages
        var successMessage = $('#success-message');
        var errorMessage = $('#error-message');

        // Hide success message after 3 seconds
        if(successMessage.length) {
            setTimeout(function() {
                successMessage.fadeOut();
            }, 3000);
        }

        // Hide error message after 3 seconds
        if(errorMessage.length) {
            setTimeout(function() {
                errorMessage.fadeOut();
            }, 3000);
        }
    });
</script>

<script>
    let orderIdToDelete = null;

    // Function to open the modal and set the order ID for deletion
    function openDeleteModal(orderId) {
        orderIdToDelete = orderId;
        // Show the modal
        $('#confirmDeleteModal').modal('show');
    }

    // When the user confirms deletion
    $('#confirmDeleteButton').on('click', function () {
        if (orderIdToDelete) {
            // Redirect to delete route with the order ID
            window.location.href = "{{ route('delete_orders', '') }}" + '/' + orderIdToDelete;
        }
    });

    {{--  $('#search').on('input', function () {
        let query = $(this).val();

        if (query.length >= 3) { // Start searching when the input is at least 3 characters
            $.ajax({
                url: '{{ route('search_orders') }}',
                type: 'GET',
                data: { search: query },
                success: function (response) {
                    // Display the results inside the searchResults div
                    $('#searchResults').html(response);
                }
            });
        } else {
            $('#searchResults').html(''); // Clear results when the input is too short
        }
    });
  --}}


  function openOrderDetailsModal(id, userName, totalPrice, paymentMethod, receivingMethod, shippingAddress, status,order_notes) {
    document.getElementById("order_id").textContent = id;
    document.getElementById("user_name").textContent = userName;
    document.getElementById("total_price").textContent = totalPrice;
    document.getElementById("payment_method").textContent = paymentMethod;
    document.getElementById("receiving_method").textContent = receivingMethod;
    document.getElementById("shipping_address").textContent = shippingAddress;
    document.getElementById("order_status").textContent = status;
    document.getElementById("order_notes").textContent = order_notes;

    // Show the modal
    var orderModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    orderModal.show();
}



function openStatusUpdateModal(orderId, currentStatus) {
    document.getElementById("order_id").value = orderId;
    document.getElementById("current_status").textContent = currentStatus;
    document.getElementById("new_status").value = currentStatus; // Preselect current status

    // Show the modal
    var statusModal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
    statusModal.show();
}

function updateOrderStatus() {
    let orderId = document.getElementById("order_id").value;
    let newStatus = document.getElementById("new_status").value;

    // Send AJAX request to update status
    fetch(`/orders/change_order_status?id=${orderId}&status=${newStatus}`, {
        method: "GET", // Use POST if required by your backend
        headers: { "X-Requested-With": "XMLHttpRequest" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Order status updated successfully!");
            location.reload(); // Reload page to see the changes
        } else {
            alert("Failed to update status. Try again.");
        }
    })
    .catch(error => console.error("Error updating status:", error));
}



function openDeliveriesModal(deliveries,orderId) {
    console.log(orderId)
    // Get the table body element where the rows will be inserted
    let tableBody = document.getElementById('deliveries_table_body');

    // Clear any previous content
    tableBody.innerHTML = '';

    // Loop through each delivery and add a row to the table
    deliveries.forEach((delivery, index) => {
        let row = document.createElement('tr');

        row.innerHTML = `
            <td>${delivery.id}</td>
            <td>${delivery.name}</td>
            <td>${delivery.email}</td>
            <td>${delivery.phone}</td>
            <td><button class="btn btn-primary" onclick="assignDelivery(${delivery.id}, ${orderId})">Assign</button></td>        `;

        tableBody.appendChild(row);
    });

    // Show the modal
    let modal = new bootstrap.Modal(document.getElementById('deliveriesModal'));
    modal.show();
}


function assignDelivery(deliveryId, orderId) {
    // Send an AJAX request to the server to assign the delivery
    fetch('/orders/assign-delivery', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Include CSRF token if needed
        },
        body: JSON.stringify({
            delivery_id: deliveryId,
            order_id: orderId
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log(data)
        if (data.status=="success") {
            // Handle successful assignment (e.g., show a success message)
            alert('Delivery assigned successfully!');
        } else {
            // Handle failure (e.g., show an error message)
            alert('Failed to assign delivery.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred.');
    });
}


</script>

</body>

</html>
