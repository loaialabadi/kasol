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
  <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet">
  <script src="{{ asset('assets/js/pace.min.js') }}"></script>

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

@if(session('error'))
    <div class="alert alert-danger" id="error-message">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success" id="success-message">
        {{ session('success') }}
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
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0">
            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
            <li class="breadcrumb-item active" aria-current="page">الخدمات</li>
          </ol>
        </nav>
      </div>
      <div class="ms-auto">
        {{-- Additional actions can go here --}}
      </div>
    </div>
    <!--end breadcrumb-->

    <div class="row g-3">
      
      <div class="col-auto">
        <div class="d-flex align-items-center gap-2 justify-content-lg-end">
          <a class="btn btn-primary px-4" href="{{ route('assign_size', ['id' => $id]) }}">
              <i class="bi bi-plus-lg me-2"></i>اضافه
          </a>
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
                  <th>الحجم</th>
                  <th>السعر</th>
                  <th>الاوامر</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($sizes as $size)
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-3">
                      <div class="product-info">
                        {{ $size->name }}
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-3">
                      <div class="product-info">
                        {{ $size->price }}
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-sm btn-filter dropdown-toggle dropdown-toggle-nocaret" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li>
                          <a class="dropdown-item" href="javascript:void(0);" onclick="openDeleteModal({{ $size->id }}, {{ $id }})">حذف</a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="{{ route('update_size',['size_id'=>$size->id,'product_id'=>$id]) }}">تحديث</a>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmDeleteModalLabel">تأكيد الحذف</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            هل انت متأكد من الحذف؟
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">لا</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteButton">نعم</button>
          </div>
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
<!--end footer-->

<!-- Bootstrap and Plugins -->
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/plugins/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

<script>
  $(document).ready(function() {
      // Hide success message after 3 seconds
      var successMessage = $('#success-message');
      if(successMessage.length) {
          setTimeout(function() {
              successMessage.fadeOut();
          }, 3000);
      }

      // Hide error message after 3 seconds
      var errorMessage = $('#error-message');
      if(errorMessage.length) {
          setTimeout(function() {
              errorMessage.fadeOut();
          }, 3000);
      }
  });
</script>

<script>
  let serviceIdToDelete = null;
  let prodIdToDelete = null;

  // Function to open the delete modal and set the IDs for deletion
  function openDeleteModal(serviceId, prodId) {
      serviceIdToDelete = serviceId;
      prodIdToDelete = prodId;
      $('#confirmDeleteModal').modal('show');
  }

  // When the user confirms deletion
  $('#confirmDeleteButton').on('click', function () {
      if (serviceIdToDelete && prodIdToDelete) {
          $.ajax({
              url: "{{ route('unassigned_size', ['id' => '__ID__', 'product_id' => '__PRODUCT_ID__']) }}"
                  .replace('__ID__', serviceIdToDelete)
                  .replace('__PRODUCT_ID__', prodIdToDelete),
              type: 'POST',
              headers: {
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              success: function (response) {
                  alert('Deleted successfully');
                  location.reload();
              },
              error: function () {
                  alert('Error deleting the item.');
              }
          });
      }
  });
</script>

</body>

</html>
