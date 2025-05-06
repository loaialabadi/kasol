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

  <!--plugins-->
  <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/metismenu/metisMenu.min.css') }}">
  <link href="{{ asset('assets/plugins/fancy-file-uploader/fancy_fileupload.css') }}" rel="stylesheet">
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
</head>

<style>
  .alert{
      z-index: 9999999999999;
  }
  .preview-images {
    display: flex;
    gap: 10px;
    margin-top: 20px;
  }
  .preview-images img {
    max-width: 100px;
    max-height: 100px;
    object-fit: cover;
  }
  .old-images {
    display: flex;
    gap: 10px;
    margin-top: 20px;
  }
  .old-images img {
    max-width: 100px;
    max-height: 100px;
    object-fit: cover;
  }
  .delete-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: red;
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 14px;
  }
</style>

<body>

    <!-- Display Success Message -->
    @if(session('success'))
        <div class="alert alert-success" id="successAlert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Display Validation Errors -->
    @if($errors->any())
        <div class="alert alert-danger" id="errorAlert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

  <!--start main wrapper-->
  <main class="main-wrapper">
    <div class="main-content">
      <!--breadcrumb-->
      <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">تحديث</div>
        <div class="ps-3">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
              <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
              <!--<li class="breadcrumb-item active" aria-current="page">Starter Page</li>-->
            </ol>
          </nav>
        </div>
      </div>
      <!--end breadcrumb-->

      <div class="row">
        <div class="col-12 col-lg-12">
          <div class="card">
            <div class="card-body">

                <div class="old-images">
                    @foreach($offer->images as $image)
                      <div style="position: relative;">
                        <img src="{{ asset('storage/' . $image->image) }}" alt="Offer Image" style="width: 100px; height: 100px;">
                        <!-- Delete Button -->
                        <form action="{{ route('delete_offer_image', $image->id) }}" method="POST" style="position: absolute; top: 5px; right: 5px;">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="delete-btn" style="background: red; color: white; border: none; padding: 5px; border-radius: 50%; cursor: pointer;">X</button>
                        </form>
                      </div>
                    @endforeach
                  </div>
                <form method="POST" action="{{ route('store_update_offer') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $id }}">

                    <!-- Offer Image -->
                    <div class="mb-4">
                      <h5 class="mb-3">صور العرض</h5>
                      <input type="file" name="images[]" class="form-control" placeholder="Choose images..." multiple id="imageInput">
                      @error('images')
                        <div class="alert alert-danger" id="imageError">{{ $message }}</div>
                      @enderror

                      <!-- Preview New Images -->
                      <div class="preview-images" id="imagePreview"></div>

                      <!-- Display Old Images -->
                      {{--  <div class="old-images">
                        @foreach($offer->images as $image)
                          <div style="position: relative;">
                            <img src="{{ asset('storage/' . $image->image) }}" alt="Offer Image" style="width: 100px; height: 100px;">
                            <!-- Delete Button -->
                            <form action="{{ route('delete_offer_image', $image->id) }}" method="POST" style="position: absolute; top: 5px; right: 5px;">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="delete-btn" style="background: red; color: white; border: none; padding: 5px; border-radius: 50%; cursor: pointer;">X</button>
                            </form>
                          </div>
                        @endforeach
                      </div>  --}}
                    </div>

                    <!-- Offer Title -->
                    <div class="mb-4">
                      <h5 class="mb-3">العنوان</h5>
                      <input type="text" name="title" value="{{ old('title', $offer->title) }}" class="form-control" >
                      @error('title')
                        <div class="alert alert-danger" id="nameError">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Offer Description -->
                    <div class="mb-4">
                      <h5 class="mb-3">الوصف</h5>
                      <textarea name="description" class="form-control" >{{ old('description', $offer->description) }}</textarea>
                      @error('description')
                        <div class="alert alert-danger" id="descriptionError">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Offer Start Date -->
                    <div class="mb-4">
                      <h5 class="mb-3">تاريخ البدأ</h5>
                      <input type="date" value="{{ old('start_date', $offer->start_date) }}" name="start_date" class="form-control">
                      @error('start_date')
                        <div class="alert alert-danger" id="start_dateError">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Offer End Date -->
                    <div class="mb-4">
                      <h5 class="mb-3">تاريخ الانتهاء</h5>
                      <input type="date" value="{{ old('end_date', $offer->end_date) }}" name="end_date" class="form-control">
                      @error('end_date')
                        <div class="alert alert-danger" id="end_dateError">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Offer Product -->
                    <div class="mb-4">
                      <h5 class="mb-3">منتجات العرض</h5>
                      <select name="product_id" class="form-control">
                        @foreach ($products as $product)
                          <option value="{{ $product->id }}" {{ $offer->product_id == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}({{ $product->price }}$)
                          </option>
                        @endforeach
                      </select>
                      @error('product_id')
                        <div class="alert alert-danger" id="product_idError">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Offer Discounted Price -->
                    <div class="mb-4">
                      <h5 class="mb-3"> نسبه الخصم</h5>
                      <input type="number" name="descounted_price" class="form-control" placeholder="Enter Price After Discount" value="{{ old('descounted_price', $offer->descounted_price) }}">
                      @error('descounted_price')
                        <div class="alert alert-danger" id="descounted_priceError">{{ $message }}</div>
                      @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">تحديث</button>
                    <div>
                    </div>
                  </form>

            </div>
          </div>
        </div>

      </div><!--end row-->
    </div>
  </main>
  <!--end main wrapper-->

    <!--start overlay-->
    <div class="overlay btn-toggle"></div>
    <!--end overlay-->

     <!--start footer-->
     <footer class="page-footer">
      <p class="mb-0">Copyright © 2024. All rights reserved.</p>
    </footer>
    <!--end footer-->

  <!--bootstrap js-->
  <script src="assets/js/bootstrap.bundle.min.js"></script>

  <!--plugins-->
  <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('assets/plugins/metismenu/metisMenu.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.ui.widget.js') }}"></script>
  <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.fileupload.js') }}"></script>
  <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.iframe-transport.js') }}"></script>
  <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.fancy-fileupload.js') }}"></script>
  <script>
    $('#fancy-file-upload').FancyFileUpload({
        params: {
            action: 'fileuploader'
        },
        maxfilesize: 1000000 // File size in bytes
    });
  </script>
  <script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>

  <script>
    // Image Preview Functionality
    document.getElementById('imageInput').addEventListener('change', function(event) {
        const previewContainer = document.getElementById('imagePreview');
        previewContainer.innerHTML = ''; // Clear previous previews

        const files = event.target.files;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                previewContainer.appendChild(img);
            };

            reader.readAsDataURL(file);
        }
    });
  </script>
</body>

</html>
