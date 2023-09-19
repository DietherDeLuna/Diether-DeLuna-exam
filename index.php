<?php
session_start();

include 'sync_youtube_channel.php';

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Youtube</title>
    <!-- CSS STYLE -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">

    <!-- Script -->
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <section id="navigation">
        <nav class="navbar fixed-top navbar-expand-lg navbar-light bg-white border-top border-5 border-danger">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php"><img src="youtube-logo.png" alt="" width="38"><b>YouTube</b></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto mb-2 mb-lg-0 p-1">
                        <li class="nav-item px-4">
                            <a class="nav-link active" aria-current="page" href="index.php"><strong>Home</strong></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="show_youtube_channel.html"><strong>YouTube Channel</strong></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </section>

    <section id="masthead" class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="head-title">GET YOUTUBE CHANNEL INFORMATION</p>
                    <p> YouTube channel is available to everyone who joins YouTube as a member.
                        The channel serves as the home page for the user's account</p>
                    <p> Enter YouTube Channel Name and you it will save it's 100 latest videos. </p>
                    <?php
                    if (!empty($message)) {
                    ?>
                        <?= $message; ?>
                    <?php
                    }
                    ?>
                    <form action="index.php" method="POST">
                        <div class="mb-3">
                            <input type="text" name="y_cname" placeholder="Type here...." class="form-control" size="50" id="y_url" required>
                        </div>
                        <div class="mb-3">
                            <button type="submit" name="save_name" class="btn btn-danger">Get Channel</button>
                        </div>
                    </form>
                </div>

                <div class="col-md-6 text-center">
                    <img src="youtube-logo.png" alt="" class="img-logo">
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark py-5 ">
        <div class="container text-light text-center">
            <p class="display-6 mb-3"><img src="youtube-logo.png" alt="" width="75">YouTube</p>
            <small>&copy; DIETHER DE LUNA. All rights reserved.</small>
        </div>
    </footer>

</body>

</html>