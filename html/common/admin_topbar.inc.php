<nav class="navbar navbar-expand-lg bg-secondary text-uppercase fixed-top" style="position: relative;" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="/admin/main">
            <img src="/photo/logo2.png" width="60" height="60" class="d-inline-block align-top" alt="">

        </a> <a class="navbar-brand" href="/admin/main">Admin</a>

        <button class="navbar-toggler text-uppercase font-weight-bold bg-primary text-white rounded" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            Menu
            <i class="fas fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto">
                <a class="waves-effect waves-dark" href="/admin/logout/?access_token=<?php echo admin::getAccessToken(); ?>&continue=/" aria-expanded="false">
                    <button class="nav-item mx-0 mx-lg-1">Logout</button>
                </a>
            </ul>
        </div>
    </div>
</nav>