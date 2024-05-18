<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
<link rel="stylesheet" href="./styles/css/navbar.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="icon" type="image/png" sizes="32x32" href="./styles/images/favicon.png">

<?php
require (__DIR__ . '/functions.php');
?>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" /> -->

<nav class="navbar" style="user-select: none;">

  <?php
  $current_page = basename($_SERVER['PHP_SELF']);
  if ($current_page == "index.php" || $current_page == "" || $current_page == "face_login.php" || $current_page == "register.php") {
    ?>
    <a class="btn disabled">&#8801;</a>
    <?php
  } else {
    ?>
    <a href="#sidenav" class="btn open">&#8801;</a>
    <?php
  }
  ?>
  <div class="sidenav" id="sidenav">
    <ul>
      <li class="center user">
        <img draggable="false" src="./face/labels/noprofile.jpg" alt="User" />
        <?php if (isset($_SESSION['fullname'])) {

          echo $_SESSION['fullname'];
        }
        ?>
        <!-- <p>{{session['fullname']}}</p> -->
      </li>

      <!-- {% if session['is_admin'] == 'yes' %} -->
      <li class="divider"></li>
      <li class="title">Administration</li>
      <li class="item {% if request.path == '/admin/dashboard' %}active{% endif %}"><a href="/admin/dashboard"
          draggable="false"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
      <li class="item {% if request.path == '/admin/members_info' %}active{% endif %}"><a href="/admin/members_info"
          draggable="false"><i class="fa-solid fa-address-book"></i> Members Info</a></li>
      <li class="title">Payment Management</li>
      <li class="item {% if request.path == '/admin/payment_arrangement' %}active{% endif %}"><a
          href="/admin/payment_arrangement" draggable="false"><i class="fa-solid fa-note-sticky"></i> Payment
          Arrangement</a></li>

      <li class="item {% if request.path == '/members/payment_history' %}active{% endif %}"><a
          href="/admin/payment_verification" draggable="false"><i class="fa-solid fa-money-check-dollar"></i> Payment
          Verification</a></li>
      <li class="item {% if request.path == '/admin/payment_history' %}active{% endif %}"><a
          href="/admin/payment_history" draggable="false"><i class="fa-solid fa-cash-register"></i> Payment History</a>
      </li>

      <li class="title">Face Recognition</li>
      <li class="item {% if request.path == '/faceregister' %}active{% endif %}"><a href="/faceregister"
          draggable="false"><i class="fa-solid fa-face-smile"></i> Face Register</a></li>
      <!-- {% endif %} -->

      <!-- {% if session['is_admin'] == 'no' %} -->
      <li class="divider"></li>
      <li class="title">User Management</li>
      <li class="item {% if request.path == '/members/home' %}active{% endif %}"><a href="/members/home"
          draggable="false"><i class="fa-solid fa-circle-info"></i> Members Info</a></li>

      <li class="title">Payment Management</li>
      <li class="item {% if request.path == '/members/payment' %}active{% endif %}"><a href="/members/payment"
          draggable="false"><i class="fa-solid fa-money-bill-wave"></i> Members Payment</a></li>


      <li class="item {% if request.path == '/members/payment_history' %}active{% endif %}"><a
          href="/members/payment_history" draggable="false"><i class="fa-solid fa-money-check-dollar"></i> Payment
          History</a></li>


      <li class="title">Face Recognition</li>
      <li class="item {% if request.path == '/faceregister' %}active{% endif %}"><a href="/faceregister"
          draggable="false"><i class="fa-solid fa-face-smile"></i> Face Register</a></li>
      <!-- {% endif %} -->

      <div class="flex">
        <div class="flex-half">
          <li><a href="/account" draggable="false"><i class="fa-solid fa-user-tie"></i> Profile</a></li>
        </div>
        <div class="flex-half">
          <li><a href="/logout" draggable="false"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
        </div>
      </div>
    </ul>
  </div>
  <a href="#!" class="close sidenav-overlay" draggable="false"></a>

  <a class="navbar-brand" href="/hofin" draggable="false">
    <img draggable="false" src="./styles/images/logo-s.png" alt="logo-s" class="navbar-img" />
    HOA Finance
  </a>
  <div class="username">
    <!-- <h3>
      {% if session['is_admin'] == "yes" %}
      <i class="fa-solid fa-user-tie"></i>
      {% elif session['is_admin'] == "no" %}
      <i class="fa-solid fa-user"></i>
      {% endif %}
      {{session['user_type']}} -->
    </h3>

  </div>
</nav>
<!-- Prevent the user from being able to navigate back to a specific page using the browser's back button -->
<script>
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }
</script>