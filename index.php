<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container-xl">
  <div class="jumbotron">
  <h1>PHP + MySQL CRUD Demo</h1>
  <p class="bg-info">Create, read, update, and delete records below:</p>
  <table class="table table-bordered">
    <tbody>
      <?php include 'read.php'; ?>
    </tbody>
  </table>

  <form class="form-inline m-2" action="create.php" method="POST">
    <label for="name">Name:</label>
    <input type="text" class="form-control m-2" id="name" name="name">
    <label for="score">Score:</label>
    <input type="number" class="form-control m-2" id="score" name="score">
    <button type="submit" class="btn btn-primary">Add</button>
  </form>

  <blockquote>
    <p class="lead">!! Please 33don't  !! I am not a professional front end designer and I have created this page to showcase my learning in devops. I have tried to dockerize this app and automatate to run it on kubernetes cluster all by learning on the various education platforms.</p>
    <footer class="lead">Mohit Verma</footer>
  </blockquote>
  

</div>

</body>
</html>
