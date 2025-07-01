<?php

session_start();

if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="assets/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100">
        <div class="card p-4 py-3 shadow w-75 mx-2">
            <h3 class="mb-4 text-center fs-3">Admin Login</h3>
            <form method="post" action="api/auth.php">
                <div class="mb-3">
                    <label for="username" class="form-label fs-5">Username</label>
                    <input type="text" class="form-control form-control-lg" id="username" name="username" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label fs-5">Password</label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                </div>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger py-1"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
            </form>
        </div>
    </div>
</body>

</html>