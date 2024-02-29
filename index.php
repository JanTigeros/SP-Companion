<!-- FILEPATH: /c:/laragon/www/SP-Companion/index.html -->
<!-- BEGIN: abpxx6d04wxr -->
<!DOCTYPE html>
<html lang="sl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <link href="css/index.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container-fluid text-center bg-dark d-flex align-items-center justify-content-center">
        <h1 class="text-white" id="title">SP COMPANION</h1>
    </div>
    <div class="container-fluid d-flex align-items-center justify-content-center" id="cards-conatainer" style="height: 80vh;">
        <div class="row row-cols-1 row-cols-lg-3">
            <div class="col">
                <div class="card" id="link-cards" onclick="document.location='customer.php'">
                    <div class="card-body">
                        <h2 class="card-title">Račun za stranke</h2>
                        <img width="50%" src="resources/person-icon.png" alt="company icon">
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card" id="link-cards" onclick="document.location='company.php'">
                    <div class="card-body">
                        <h2 class="card-title">Račun za podjetja</h2>
                        <img width="50%" src="resources/company-icon.png" alt="company icon">
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card" id="link-cards" onclick="document.location='adminpannel.html'">
                    <div class="card-body">
                        <h2 class="card-title">Nadzorna Plošča</h2>
                        <img width="50%" src="resources/admin-icon.png" alt="company icon">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
<!-- END: abpxx6d04wxr -->
