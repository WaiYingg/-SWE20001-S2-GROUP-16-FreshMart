<div class="col-auto col-md-1" style="background-color:#FF7F50;">
                <div class="mt-5" style="background-color:#FF7F50;">
                    <ul class="nav nav-pills flex-column" style="font-family:Playfair Display;">
                        <li class="nav-item pt-5">
                            <a href="admin_page.php" class="nav-link text-white">
                                <i class="fa-solid fa-house" style="margin-right:1rem;"></i><span
                                    class="fs-2 d-none d-lg-inline">Home</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin-product-page.php" class="nav-link text-white">
                                <i class="fa-brands fa-product-hunt" style="margin-right:.8rem;"></i><span
                                    class="fs-2 d-none d-lg-inline">Product</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="admin_order.php" class="nav-link text-white" aria-current="page">
                                <i class="fa-solid fa-receipt" style="margin-right:1.5rem;"></i><span
                                    class="fs-2 d-none d-lg-inline">Order</span>
                            </a>
                        </li>
                        <li class="nav-item mb-2">
                            <a href="admin_contacts.php" class="nav-link text-white" aria-current="page">
                                <i class="fa-solid fa-phone" style="margin-right:1rem;"></i><span
                                    class="fs-2 d-none d-lg-inline">Contact</span>
                            </a>
                        </li>
                        <hr>
                        <li class="nav-item dropdown"> <!-- Add the 'dropdown' class here -->
                            <a href="login.php" class="dropdown-toggle-no-caret text-white " id="dropdownMenuButton" data-mdb-toggle="dropdown"
                                aria-expanded="false" style="font-weight:700;background-color:#222;border-radius:50px;">
                                <i class="fa-solid fa-user" style="margin-right:1rem;"></i><span
                                    class="fs-2 d-none d-lg-inline">Profile</span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <?php
                                if (isset($_SESSION['email'])) {
                                    // User is logged in, show logout button
                                    echo '
                            <li><a class="dropdown-item" href="account.php" title="Account">Account</a></li>
                            <li><a class="dropdown-item" href="logout.php" title="Logout">Logout</a></li>
                        ';
                                } else {
                                    // User is not logged in, show login and register links
                                    echo '
                            <li><a class="dropdown-item" href="login.php" title="Login">Login</a></li>
                            <li><a class="dropdown-item" href="user-register.php" title="Register">Register</a></li>
                        ';
                                }
                                ?>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>