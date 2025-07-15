<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo isset($title) ? html_escape($title) . ' | Abadia App' : 'Abadia App'; ?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="<?php echo site_url('/'); ?>"><b>Abadia</b>App</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <!-- Main content view will be loaded here -->
      <p class="login-box-msg"><?php echo lang('login_subheading'); ?></p>

      <?php if (isset($message) && !empty($message)): ?>
          <div id="infoMessage" class="alert alert-danger" role="alert">
              <?php echo $message;?>
          </div>
      <?php endif; ?>
      <?php if(validation_errors()): ?>
          <div class="alert alert-danger" role="alert">
              <?php echo validation_errors(); ?>
          </div>
      <?php endif; ?>

      <!-- The form from login.php will be rendered here by the controller -->



































































































































































































































































































































































-
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+
+-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-

-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-..
..
.
.
.
.
.
..
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
...
.
.
.
..
.
.
-
-
-
-..
.
.
.
.
.
..
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-..
..
.
.
.
.
.
.
.
.
.
..
.
.
.
.
.
.
.
.
-
-
-
-
-
-
-
-
-
-
-..
.
.
.
.
.
.
.
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-..
.
.
.
.
.
.
.
.
.
..
..
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
-
-
-
-
-
-
-
-
-
-
-
-
-
-`
-
-`
-
-
-
-
-
-
-
-
-
-
-`
-
-
-
-
-
-
-
-
-
-
-
-
-..
.
..
.
.
.
.
.
.
.
.
.
.
.
.
.
..
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
-
-..
.
.
.
.
.
.
.
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-.
.
.
.
.
.
...
.
.
.
.
.
.
.
.
.
.
.
.
..
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-..
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-...
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
..
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
...
.
.
.
.
.
-
-...
.
.
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-..
.
.
.
.
.
.
.
.
-
-
-
-
-
-
-
-
-
-..
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
...
.
.
-
-
-..
.
.
.
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
-..
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
-
-
--
--
-
-...
.
.
.
-
-
-
-`
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-..
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
-
-..
.
.
.
.
.
.
.
.
.
.
.
.
.
.
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
-
-
-
-
-
--
-
-
-
-
-
-
-
-
--
-
-
-
-
-
-
-
--
-
-
-
-
-
-..
.
..
.
.
.
.
.
.
.
.
.
.
.
.
..
.
..
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
..
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
..
.
.
.
.
.
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
--
-
-
--
-
--
-
-
--
-
-
-
-
-..
.
.
.
.
.
.
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
-
-
-
-
-
-
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-..
.
.
.
.
.
...
.
.
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
--
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
--
-
-
-
-
-
-
--
-
-...
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
-
-
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
--
-
-
--
-
-
-
-
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
--
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-...
.
.
.
.
.
.
.
.
.
.
.
.
.
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
-
-
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-...
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
..
.
.
.
.
.
.
.
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
-
--
-
-
-
--
-
-
-
--
-
-
-
-
-
-
-
-
-
-
-
--
-
-
-
-
-...
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
.
li {
    list-style-type: none;
}
.navbar {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    background-color: #f2f2f2;
}
.navbar ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}
.navbar li {
    display: inline-block;
    margin-right: 10px;
}
.navbar a {
    text-decoration: none;
    color: #333;
    padding: 5px;
}
.navbar a:hover {
    background-color: #ccc;
}
.container {
    padding: 20px;
}
.footer {
    background-color: #f2f2f2;
    padding: 10px;
    text-align: center;
}
</style>
</head>
<body>

<div class="navbar">
    <ul>
        <li><a href="#home">Home</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>
    <ul>
        <li><a href="#login">Login</a></li>
        <li><a href="#register">Register</a></li>
    </ul>
</div>

<div class="container">
    <h1>Welcome to my website!</h1>
    <p>This is a simple example of a website layout with a navbar, container, and footer.</p>
</div>

<div class="footer">
    <p>&copy; 2023 My Website</p>
</div>

</body>
</html>
[end of application/views/templates/main_layout.php]
