<?php include "includes/config.php"; ?>
<?php include "includes/header.php"; ?>

<div class="container py-5">
    <div class="row align-items-center">
        <div class="col-lg-6 mb-4">
            <h1 class="display-5 fw-bold">Barber Shop Management System</h1>
            <p class="lead text-muted">Take control of your appointments, streamline bookings, and keep your barbershop running smoothly.</p>
            <div class="mt-4">
                <a href="login.php" class="btn btn-dark btn-lg me-2">Login</a>
                <a href="register.php" class="btn btn-outline-dark btn-lg">Register</a>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm p-4 hero-card">
                <h5 class="mb-3">Why choose this system?</h5>
                <ul class="list-unstyled feature-list mb-0">
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Quick appointment booking</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Easy edit and delete options</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Clear status tracking</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Secure user login</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>