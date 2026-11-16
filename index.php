
<?php
require_once 'core/models.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$categories = getAllCategories($pdo);

function renderMenuPanel($pdo, $cat) {
    ?>
    <div class="panel" data-category="<?= htmlspecialchars($cat['name']) ?>">
      <h3>Add Item to <?= htmlspecialchars($cat['name']) ?></h3>

      <form class="add-form" action="core/handleForms.php" method="POST">
        <input type="hidden" name="category_id" value="<?= $cat['category_id'] ?>">
        <input type="hidden" name="from_index" value="1">
        <input type="text" name="name" placeholder="Item name" required>
        <input type="number" step="0.01" name="price" placeholder="Price" required>
        <input type="text" name="details" placeholder="Details">
        <input type="submit" name="insertItemBtn" value="Add Item">
      </form>

      <h3><?= htmlspecialchars($cat['name']) ?> Items</h3>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Price</th>
              <th>Details</th>
              <th>Added by</th>
              <th>Last updated by</th>
              <th>Last updated</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            <?php $items = getItemsByCategory($pdo, $cat['category_id']); ?>

            <?php if (count($items) === 0) { ?>
              <tr>
                <td class="empty" colspan="7">No items in this category yet.</td>
              </tr>
            <?php } ?>

            <?php foreach ($items as $row) { ?>
            <tr>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= number_format($row['price'], 2) ?></td>
              <td><?= htmlspecialchars($row['details'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['added_by_name'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['updated_by_name'] ?? '') ?></td>
              <td><?= htmlspecialchars($row['last_updated']) ?></td>

              <td class="actions">
                <a class="btn-edit"
                   href="edit_item.php?id=<?= $row['item_id'] ?>&from=index"
                   data-edit="item"
                   data-id="<?= (int)$row['item_id'] ?>"
                   data-category-id="<?= (int)$cat['category_id'] ?>"
                   data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?>"
                   data-price="<?= htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8') ?>"
                   data-details="<?= htmlspecialchars($row['details'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   data-from-index="1">Edit</a>

                <a class="btn-del"
                   href="core/handleForms.php?deleteItemId=<?= $row['item_id'] ?>&category_id=<?= $cat['category_id'] ?>&from=index"
                   data-confirm="Delete this item?">Delete</a>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Coffee Shop</title>

  <link rel="stylesheet" href="design_style.css">

  <style>
    .search-section {
      display: block;
      width: 100%;
      margin: 28px auto 8px;
      text-align: center;
    }

    .search-box {
      display: flex;
      justify-content: center;
      gap: 12px;
      width: 100%;
      max-width: 760px;
      margin: 0 auto;
    }

    .search-box input[type="search"] {
      flex: 1;
      padding: 16px 20px;
      font-size: 17px;
      border: 1px solid #d9cfc4;
      border-radius: 14px;
      background: #fff;
      outline: none;
      box-shadow: 0 4px 14px rgba(74, 44, 29, .08);
    }

    .search-box input[type="search"]:focus {
      border-color: #6b4429;
      box-shadow: 0 0 0 4px rgba(107, 68, 41, .15);
    }

    .search-box select {
      padding: 0 14px;
      font-size: 16px;
      border: 1px solid #d9cfc4;
      border-radius: 14px;
      background: #fff;
      color: #4a2c1d;
      font-weight: 600;
      cursor: pointer;
      outline: none;
    }

    .search-box button {
      padding: 0 26px;
      font-size: 16px;
      font-weight: 600;
      border: none;
      border-radius: 14px;
      background: #6b4429;
      color: #fff;
      cursor: pointer;
    }

    .search-box button:hover {
      background: #4a2c1d;
    }

    #noResults {
      display: none;
      margin-top: 14px;
      color: #7a6a5c;
    }
  </style>
</head>

<body>

<div class="container">

  <div class="topbar">
    <h1>
      &#9749; Coffee Shop &mdash; Hello STAFF,
      <?= htmlspecialchars($_SESSION['username']) ?>!
    </h1>

    <a class="logout" href="core/handleForms.php?logoutAUser=1">
      Logout
    </a>
  </div>

  <!-- SEARCH SECTION -->
  <div class="search-section">

    <div class="search-box">

      <select id="searchScope" aria-label="Search in">
        <option value="name">Name</option>
        <option value="items">Items</option>
        <option value="categories">Categories</option>
        <option value="all">All</option>
      </select>

      <input
        type="search"
        id="searchInput"
        placeholder="&#128269; Search records (name, price, details, user)..."
        autocomplete="off"
      >

      <button type="button" id="clearSearch">
        Clear
      </button>

    </div>

    <div id="noResults">
      No records found.
    </div>

  </div>

  <!-- ADD CATEGORY -->
  <h3>Add Category</h3>

  <form class="add-form" action="core/handleForms.php" method="POST">
    <input
      type="text"
      name="name"
      placeholder="Category name"
      required
    >

    <input
      type="submit"
      name="insertCategoryBtn"
      value="Add Category"
    >
  </form>

  <!-- MENU CATEGORIES -->
  <h3>Menu Categories</h3>

  <div class="table-wrap">

    <table id="categoryTable">

      <thead>
        <tr>
          <th>Category</th>
          <th>Added by</th>
          <th>Last updated by</th>
          <th>Last updated</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>

        <?php foreach ($categories as $row) { ?>

        <tr>
          <td><?= htmlspecialchars($row['name']) ?></td>

          <td><?= htmlspecialchars($row['added_by_name'] ?? '') ?></td>

          <td><?= htmlspecialchars($row['updated_by_name'] ?? '') ?></td>

          <td><?= htmlspecialchars($row['last_updated']) ?></td>

          <td class="actions">

            <a class="btn-edit"
               href="edit_category.php?id=<?= $row['category_id'] ?>"
               data-edit="category"
               data-id="<?= (int)$row['category_id'] ?>"
               data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?>">
              Edit
            </a>

            <a class="btn-del"
               href="core/handleForms.php?deleteCategoryId=<?= $row['category_id'] ?>"
               data-confirm="Delete this category and its items?">
              Delete
            </a>

          </td>
        </tr>

        <?php } ?>

        <?php if (count($categories) === 0) { ?>

        <tr>
          <td class="empty" colspan="5">
            No categories yet. Add one above.
          </td>
        </tr>

        <?php } ?>

      </tbody>

    </table>

  </div>

  <!-- CATEGORY ITEM PANELS -->
  <?php
    foreach ($categories as $category) {
        renderMenuPanel($pdo, $category);
    }
  ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="logout.js"></script>
<script src="search.js"></script>

</body>
</html>
