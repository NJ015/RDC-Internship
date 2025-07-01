<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Manager</title>
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/styles.css" />
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- <div class="feedback-note">
        <strong>Nour J.</strong>
        <p>This is an amazing sticky-note-style feedback box!</p>
    </div> -->

    <nav class="nav navbar-nav align-content-center bg-light p-3 fs-4">
        We value your feedback <?php if(isset($_SESSION['admin']) && $_SESSION['admin']) {
            ?>
            <span class="fs-6 bg-dark-subtle text-center">Admin</span>
            <?php
        } ?>
    </nav>

    <!-- Toast container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
        <div id="mainToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="mainToastMsg"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <main class="container-fluid p-4 flex-grow-1">
        <div class="w-100 d-flex justify-content-end gap-2">
            <button id="addbtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFeedback">Add your
                feedback</button>
            <button class="btn btn-primary d-none" href="logout.html">Log out</button>
        </div>
        <div id="feedbackContainer" class="d-flex gap-4 justify-content-center flex-wrap mt-4">
            <!-- <div id="feedback" class="bg-light mb-3 p-3 rounded" role="button" tabindex="0" data-bs-toggle="modal"
                data-bs-target="#viewFeedback">
                <div class="mb-2">
                    <div id="name" class="text-muted fs-5">Name</div>
                    <div class="rating">
                        ★★★★☆
                    </div>
                </div>

                <div id="textf" class="mt-1">Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem
                    Ipsum Lorem
                    Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem
                    Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem
                    Ipsum Lorem Ipsum</div>
            </div> -->
        </div>
    </main>

    <div id="viewFeedback" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <div id="nameModal" class="text-muted fs-5">Name</div>
                        <div class="rating">
                            <span>★★★★☆</span>
                        </div>
                    </div>

                    <div id="feedbackTextModal" class="mt-1">Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum
                        Lorem Ipsum Lorem
                        Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem
                        Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem Ipsum Lorem
                        Ipsum Lorem Ipsum</div>
                </div>
                <div class="modal-footer d-none">
                    <!-- TODO: fix data and attr(s) -->
                    <button type="button" id="deletebtn" class="btn btn-danger" data-bs-dismiss="modal">Delete</button>
                    <button type="submit" id="editbtn" class="btn btn-primary">Edit</button>
                </div>
            </div>
        </div>
    </div>

    <div id="addFeedback" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Feedback</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modalErrorMsg" class="text-danger text-center d-none"></div>
                    <div class="mb-2">
                        <label class="form-label d-block m-0">Rating</label>
                        <div class="rating-input">
                            <input type="radio" name="rating" value="5" id="star5">
                            <label for="star5">★</label>
                            <input type="radio" name="rating" value="4" id="star4">
                            <label for="star4">★</label>
                            <input type="radio" name="rating" value="3" id="star3">
                            <label for="star3">★</label>
                            <input type="radio" name="rating" value="2" id="star2">
                            <label for="star2">★</label>
                            <input type="radio" name="rating" value="1" id="star1">
                            <label for="star1">★</label>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label for="userName" class="form-label">Name</label>
                        <input type="text" id="userName" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label for="feedbackText" class="form-label">Feedback</label>
                        <textarea id="feedbackText" class="form-control" rows="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="savebtn" class="btn btn-primary">Submit Feedback</button>
                </div>
            </div>
        </div>

    </div>


    <footer class="bg-light p-3 text-center">
        An admin? <a href="login.php">Login</a>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let isAdmin = <?php echo isset($_SESSION['admin']) && $_SESSION['admin'] ? 'true' : 'false'; ?>;
    </script>
    <script src="assets/script.js"></script>

</body>