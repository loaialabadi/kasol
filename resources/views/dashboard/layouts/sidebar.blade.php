<aside class="sidebar-wrapper" id="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
      <div class="logo-icon">
        <img src="assets/images/logo-icon.png" class="logo-img" alt="">
      </div>
      <div class="logo-name flex-grow-1">
        <img style="width: 100px" src="{{ asset('assets/images/logo_kasool.png') }}" alt="">

      </div>
      <div id="sidebar-close" style="display:none;" class="sidebar-close">
        <span class="material-icons-outlined">close</span>
      </div>
    </div>
    <div class="sidebar-nav">
        <!--navigation-->
        <ul class="metismenu" id="sidenav">
          <li>
            {{--  <a href="javascript:;" class="has-arrow">
              <div class="parent-icon"><i class="material-icons-outlined">home</i>
              </div>
              <div class="menu-title">Dashboard</div>
            </a>  --}}
            <ul>
              <!--<li><a href="{{ route('roles_page') }}"><i class="material-icons-outlined">arrow_right</i>الأدوار</a>-->
              <!--</li>-->
              {{--  <li><a href="{{ route('permissions_page') }}"><i class="material-icons-outlined">arrow_right</i>Permissions</a>  --}}
              </li>
              <li><a href="{{ route('admins_page') }}"><i class="material-icons-outlined">arrow_right</i>الادمنز</a>
              </li>
            </ul>
          </li>
          <li>
            {{--  <a href="javascript:;" class="has-arrow">
              <div class="parent-icon"><i class="material-icons-outlined">widgets</i>
              </div>
              <div class="menu-title">Widgets</div>
            </a>  --}}
            <ul>
              <li>
                <a href="{{ route('categories_page') }}"><i class="material-icons-outlined">arrow_right</i>الفئات</a>
              </li>
              <li>
                <a href="{{ route('services_page') }}"><i class="material-icons-outlined">arrow_right</i>الخدمات</a>
              </li>
              <li>
                <a href="{{ route('asks_page') }}"><i class="material-icons-outlined">arrow_right</i>الاسئله الشائعه</a>
              </li>
              <li>
                <a href="{{ route('settings_page') }}"><i class="material-icons-outlined">arrow_right</i>الاعدادات العامه</a>
              </li>
              <li>
                <a href="{{ route('reports_page') }}"><i class="material-icons-outlined">arrow_right</i>الشكوى</a>
              </li>
              <li>
                <a href="{{ route('orders') }}"><i class="material-icons-outlined">arrow_right</i>الطلبات</a>
              </li>
              <!--<li>-->
              <!--  <a href="{{ route('gifts_page') }}"><i class="material-icons-outlined">arrow_right</i>الهدايا</a>-->
              <!--</li>-->
              <li>
                <a href="{{ route('deliveries_page') }}"><i class="material-icons-outlined">arrow_right</i>عمال التوصيل</a>
              </li>
              <li>
                <a href="{{ route('get_notifications') }}"><i class="material-icons-outlined">arrow_right</i>الرسايل</a>
              </li>
              <li>
                <a href="{{ route('coupons') }}"><i class="material-icons-outlined">arrow_right</i>كوبونات الخصم</a>
              </li>
              <li>
                  <a href="{{route('admin_log')}}" class="btn btn-primary">تسجيل خروخ</a>
              </li>
            </ul>
          </li>

         </ul>
        <!--end navigation-->
    </div>
  </aside>


<script>
var menu_side = document.getElementById('menu_side');
var toggleBtn = document.getElementById('toggleBtn');
var sidebar_wrapper = document.getElementById('sidebar-wrapper');

// Add a click event listener to the button
menu_side.addEventListener('click', function() {
    if (sidebar_wrapper.style.display === 'none') {
        sidebar_wrapper.style.display = 'block';  // Show the sidebar
    } else {
        sidebar_wrapper.style.display = 'none';   // Hide the sidebar
    }
});
    // var sidebar = document.getElementById('sidebar-close');
    
    // sidebar.style.display = 'none';
</script>