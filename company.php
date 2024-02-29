<?php
  include 'db/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer</title>
    <link href="css/customer.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container-fluid text-center bg-dark d-flex align-items-center justify-content-center">
        <div class="container text-center">
            <div class="row">
              <div class="col" id="back-col">
                <a href="index.php"><img id="head-img" src="resources/back-icon.png" alt="company icon" width="48px"></a>
              </div>
              <div class="col-6">
                <h1 class="text-white" id="title">RAČUN</h1>
              </div>
              <div class="col">
              </div>
            </div>
        </div>
    </div>
    <form action="company-invoice.php" method="post">
      <div class="container-fluid d-flex align-items-center justify-content-center" id="add-conatainer">
        <div class="container">
          <div class="row">
            <div class="col">
              <div id="invoice-info">
                <div id="invoice-info-input">
                  <label for="invoice-name">Podjetje:</label>
                  <section>
                    <select id="company" name="company">
                      <?php
                        // Prepare and execute the query
                        $query = "SELECT * FROM podjetje";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();
                        // Fetch the results
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                      ?>
                      <?php foreach ($results as $row): ?>
                        <option value="<?php echo $row['id_podjetje']; ?>"><?php echo $row['naziv_podjetja']; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </section>
                </div>
                <div id="invoice-info-input">
                  <label for="invoice-name">Kraj izdaje:</label>
                  <input type="text" id="invoice_name" name="invoice_name" value="Slovenska Bistrica">
                </div>
                <div id="invoice-info-input">
                  <label for="invoice-date-service-label">Datum opravljene storitve:</label>
                  <input type="date" id="invoice-date-service" name="invoice-date-service" value="">
                </div>
                <div id="invoice-info-input">
                  <label for="invoice-date-label">Datum izdaje:</label>
                  <input type="date" id="invoice-date" name="invoice-date">
                </div>
                <div id="invoice-info-input">
                  <label for="invoice-expire-label">Zapade v plačilo:</label>
                  <input type="date" id="invoice-expire" name="invoice-expire">
                </div>
              </div>
              <input type="hidden" name="productList" id="productListInput">
            </div>
          </div>
        </div>
      </div>
      <div class="container-fluid d-flex align-items-center justify-content-center" id="products-container">
        <div class="container text-center" id="products">
          <div class="row row-cols-1 row-cols-sm-2">
            <div class="col-sm-4" id="first-col">
              <div id="product-list">
                <h2>Product List</h2>
                <div id="product-list-select">
                  <label for="product">Storitev:</label>
                    <?php
                      // Prepare and execute the query
                      $query = "SELECT * FROM storitev";
                      $stmt = $conn->prepare($query);
                      $stmt->execute();
                      // Fetch the results
                      $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <select id="product">
                      <?php foreach ($results as $row): ?>
                        <option value="<?php echo $row['id_storitev']; ?>"><?php echo $row['naziv_storitve']; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </select>
                </div>
                <div id="product-list-input">
                  <label for="quantity">Quantity:</label>
                  <input type="number" id="quantity" min="1" value="1">
                </div>
                <div id="product-list-button">
                  <button type="button" onclick="addProduct()">
                    <img src="resources/add-icon.png" alt="add icon" width="32">
                  </button>
                </div>
              </div>
            </div>
            <div class="col-sm-8">
              <div id="selected-products">
                <h2>Selected Products</h2>
                <ul id="product-list-display"></ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="container-fluid d-flex align-items-center justify-content-center">
        <div class="container button-container">
          <button type="submit" id="submit-button">IZPIŠI RAČUN</button>
        </div>
      </div>
    </form>
    <script>
      var listOfProducts = [];
      var currentDate = new Date().toISOString().slice(0, 10);
      document.getElementById("invoice-date-service").value = currentDate;
      document.getElementById("invoice-date").value = currentDate;
      var futureDate = new Date();
      futureDate.setDate(futureDate.getDate() + 15);
      futureDate = futureDate.toISOString().slice(0, 10);
      document.getElementById("invoice-expire").value = futureDate;


      function addProduct() {
        var productSelect = document.getElementById("product");
        var quantityInput = document.getElementById("quantity");
        var productListDisplay = document.getElementById("product-list-display");

        var productName = productSelect.options[productSelect.selectedIndex].text;
        var quantity = quantityInput.value;

        // Check if the product is already in the list
        var existingItem = document.querySelector("#product-list-display li[data-product='" + productName + "']");

        if (existingItem) {
            existingItem.setAttribute("data-quantity", parseInt(existingItem.getAttribute("data-quantity")) + 1);
            existingItem.querySelector("#product-info-text").textContent = productName + "  -  " + existingItem.getAttribute("data-quantity") + "x";
            listOfProducts.push(productName);
            updateHiddenInput();
        } else {
            // Add the product to the list if it's not there
            var listItem = document.createElement("li");
            listItem.setAttribute("data-product", productName);
            listItem.setAttribute("data-quantity", quantity);
            listItem.id = productName;

            // Display the product name and quantity
            var productInfo = document.createElement("div");
            productInfo.id = "product-info";
            productInfo.classList.add("quantity");

            var productInfoText = document.createElement("p");
            productInfoText.textContent = productName + "  -  " + quantity + "x";
            productInfoText.id = "product-info-text";
            productInfoText.style.display = "inline-block";
            productInfo.appendChild(productInfoText);

            listOfProducts.push(productName);

            // Create a delete button
            var deleteButton = document.createElement("button");
            deleteButton.id = "delete-button";
            deleteButton.style.float = "right"; // Add this line to align the button to the right
            deleteButton.onclick = function() {
                listItem.remove();
                listOfProducts = listOfProducts.filter(item => item !== listItem.id);
                updateHiddenInput();
            };

            var deleteIcon = document.createElement("img");
            deleteIcon.src = "resources/delete-icon.png";
            deleteIcon.alt = "delete icon";
            deleteIcon.width = "24";
            deleteButton.appendChild(deleteIcon);
            productInfo.appendChild(deleteButton);

            // Append delete button and product info to the list item
            listItem.appendChild(productInfo);
            productListDisplay.appendChild(listItem);

            console.log(listOfProducts);
            updateHiddenInput();
        }
      }

      // Function to update the hidden input field with the current listOfProducts array
      function updateHiddenInput() {
          var productListJSON = JSON.stringify(listOfProducts);
          document.getElementById("productListInput").value = productListJSON;
      }
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>