<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<aside class="col-md-3 col-lg-2 sidebar">
  <div class="list-group small">

    <a class="list-group-item list-group-item-action <?= ($current=='index.php' ? 'active' : '') ?>" href="index.php">Dashboard</a>

    <a class="list-group-item list-group-item-action 
    <?= ($current=='content-list.php' || $current=='content-edit.php'  || $current=='content-delete.php' ? 'active' : '') ?>" 
    href="content-list.php">Flowers</a>

    <a class="list-group-item list-group-item-action <?= ($current=='content-add.php' ? 'active' : '') ?>" href="content-add.php">Add New Flower</a>

    <a class="list-group-item list-group-item-action 
    <?= ($current=='orders.php' || $current=='order-details.php' ? 'active' : '') ?>" 
    href="orders.php">Orders</a>

    <a class="list-group-item list-group-item-action 
    <?= ($current=='customers.php' || $current=='customer.php' ? 'active' : '') ?>" 
    href="customers.php">Customers</a>

    <a class="list-group-item list-group-item-action <?= ($current=='pages.php' ? 'active' : '') ?>" href="pages.php">Pages</a>

    <a class="list-group-item list-group-item-action <?= ($current=='settings.php' ? 'active' : '') ?>" href="settings.php">Settings</a>

    <a class="list-group-item list-group-item-action <?= ($current=='profile.php' ? 'active' : '') ?>" href="profile.php">Profile</a>

  </div>
</aside>



