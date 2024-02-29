<?php
include 'db/database.php';
$fullPrice = 0;

  // Funkcija za shranjevanje podatkov o računu v bazo
  function shraniRacunVBazo($kraj_izdaje, $datum_storitve, $datum_izdaje, $datum_zapada, $productList, $conn, $fullPrice) {

    // Priprava SQL poizvedbe za vstavljanje podatkov
    $stmt = $conn->prepare("INSERT INTO racun (id_racun, kraj_izdaje, datum_storitve, datum_izdaje, datum_zapada, vsota) VALUES (null, :kraj_izdaje, :datum_storitve, :datum_izdaje, :datum_zapada, :vsota)");
    // Izvedba poizvedbe
    $stmt->execute(array(':kraj_izdaje' => $kraj_izdaje, ':datum_storitve' => $datum_storitve, ':datum_izdaje' => $datum_izdaje, ':datum_zapada' => $datum_zapada, ':vsota' => $fullPrice));
  
    try {
        foreach ($productList as $product) {
            $sql2 = $conn->prepare("SELECT id_storitev, cena_storitve FROM storitev WHERE naziv_storitve = :ime_storitve");
            $sql2->execute(array(':ime_storitve' => $product));
            $result = $sql2->fetch(PDO::FETCH_ASSOC);
            $productId = $result['id_storitev'];
            $productPrice = $result['cena_storitve'];
            $fullPrice += $productPrice;

            $sql3 = $conn->prepare("SELECT id_racun FROM racun ORDER BY id_racun DESC LIMIT 1");
            $sql3->execute();
            $result = $sql3->fetch(PDO::FETCH_ASSOC);
            $racunId = $result['id_racun'];

            $sql = $conn->prepare("INSERT INTO racun_storitev (racun_id, storitev_id) VALUES (:racun_id, :storitev_id)");
            $sql->execute(array(':racun_id' => $racunId, ':storitev_id' => $productId));
        }
    } catch(PDOException $e) {
        die("Napaka pri vstavljanju podatkov o računu: " . $e->getMessage());
    }

    $sql = $conn->prepare("UPDATE racun SET vsota = :vsota WHERE id_racun = :id_racun");
    $sql->execute(array(':vsota' => $fullPrice, ':id_racun' => $racunId));
    return $fullPrice;
  }
  
  // Preverjanje, ali je bil obrazec poslan
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Preverjanje, ali so podatki o računu prisotni
    if (isset($_POST["invoice_name"]) && isset($_POST["invoice-date-service"]) && isset($_POST["invoice-date"]) && isset($_POST["invoice-expire"]) && isset($_POST["productList"])) {
        $kraj_izdaje = $_POST["invoice_name"];
        $datum_storitve = $_POST["invoice-date-service"];
        $datum_izdaje = $_POST["invoice-date"];
        $datum_zapada = $_POST["invoice-expire"];
        $productListJSON = $_POST["productList"];
        // Decode the JSON string to convert it back to an array
        $productList = json_decode($productListJSON);
  
        // Klic funkcije za shranjevanje podatkov o računu v bazo
        $fullPrice = shraniRacunVBazo($kraj_izdaje, $datum_storitve, $datum_izdaje, $datum_zapada, $productList, $conn, $fullPrice);
    } else {
        echo "Manjkajo podatki o računu.";
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="css/customer-invoice.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.js"></script>
</head>
<body>
    <div class="container-fluid text-center bg-dark d-flex align-items-center justify-content-center">
        <div class="container text-center">
            <div class="row">
              <div class="col" id="back-col">
                <a href="customer.php"><img id="head-img" src="resources/back-icon.png" alt="company icon" width="48px"></a>
              </div>
              <div class="col-6">
                <h1 class="text-white" id="title">RAČUN</h1>
              </div>
              <div class="col">
              </div>
            </div>
        </div>
    </div>
    <?php
    //print_r($_POST);
    ?>
    <div id="content">
        <div class="invoice">
            <p>MASAŽE Sabina Sajtl s.p.</p>
            <p>Ljubljanska cesta 35</p>
            <p>2310 Slovenska Bistrica</p>
            <p>TRR: SI 56 04000028 0776723</p>
            <p>ID za DDV: 66110769</p>
            <p>E-pošta: Masaze.sabina@gmail.com</p>
            <hr>
            <?php
                // Select racun_id from the last inserted racun
                $sql = "SELECT id_racun, datum_izdaje FROM racun ORDER BY id_racun DESC LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $lastRacunId = $result['id_racun'];
                $datum_izdaje = $result['datum_izdaje'];
                //echo $lastRacunId . "\n";
                $year = date('Y', strtotime($datum_izdaje));
                if ($lastRacunId > 10 && $lastRacunId < 100) {
                    echo "<h2>Račun št.: 0000" . $lastRacunId . "/" . $year . "</h2>";
                }
                else if ($lastRacunId > 100 && $lastRacunId < 100) {
                    echo "<h2>Račun št.: 000" . $lastRacunId . "/" . $year . "</h2>";
                }
                else if ($lastRacunId > 1000 && $lastRacunId < 10000) {
                    echo "<h2>Račun št.: 00" . $lastRacunId . "/" . $year . "</h2>";
                }
                else if ($lastRacunId > 10000 && $lastRacunId < 100000) {
                    echo "<h2>Račun št.: 0" . $lastRacunId . "/" . $year . "</h2>";
                }
                else {
                    echo "<h2>Račun št.: 00000" . $lastRacunId . "/" . $year . "</h2>";
                }
            ?>
            <div class="invoice-info">
                <p>Kraj izdaje: <?php echo $kraj_izdaje ?></p>
                <p>Datum opravljene storitve: <?php echo $datum_storitve ?></p>
                <p>Datum izdaje: <?php echo $datum_izdaje ?></p>
                <p>Zapade v plačilo: <?php echo $datum_zapada ?></p>
            </div>
            <hr>
            <div id="selected-products">
                <ul id="product-list-display">
                    <li>
                        <div class="product">
                            <div class="opis">
                                <b>Opis</b>
                            </div>
                            <div class="kolicina">
                                <b>Količina</b>
                            </div>
                            <div class="cena">
                                <b>Znesek</b>
                            </div>
                        </div>
                    </li>
                    <?php

                    // Select storitve from Recun_Storitev table where racun id is last inserted using a prepared statement
                    $sql = "SELECT storitev_id, COUNT(*) as quantity FROM racun_storitev WHERE racun_id = :lastRacunId GROUP BY storitev_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':lastRacunId', $lastRacunId, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($result as $row) {
                        $id = $row['storitev_id'];
                        $quantity = $row['quantity'];

                        // Select data from storitev table using a prepared statement
                        $sql1 = "SELECT * FROM storitev WHERE id_storitev = :id";
                        $stmt1 = $conn->prepare($sql1);
                        $stmt1->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt1->execute();
                        $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <li>
                            <div class="product">
                                <div class="opis">
                                    <p><?php echo $result1['naziv_storitve']?></p>
                                </div>
                                <div class="kolicina">
                                <p><?php echo $quantity . "x" ?></p>
                                </div>
                                <div class="cena">
                                    <p><?php echo $result1['cena_storitve'] * $quantity ?>€</p>
                                </div>
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <hr>
            <div class="skupaj">
                <div class="skupajContent">
                    <b id="skupajBold">SKUPAJ: <?php echo $fullPrice ?>€</b>
                    <p>Cena vsebuje 22% DDV</p>
                </div>
            </div>
            <div class="end-text">
                <p>Navedeni znesek nakažite na <b>poslovni račun</b> odprt pri NovaKBM: SI56 04000028 0776723</p> 
                <p>Za <b>sklic</b> uporabite številko računa.</p>
            </div>
        </div>
    </div>
    <div>
        <button id="downloadButton">Download PDF</button>
    </div>
    <?php include 'footer.php'; ?>

    <script>
        document.getElementById('downloadButton').addEventListener('click', function () {
            var element = document.getElementById('content');
            if (window.confirm('Do you want to download the PDF?')) {
                html2pdf(element, {
                    filename: "Račun_<?php echo $lastRacunId . '/' . $year; ?>", // Concatenate PHP variables within the string
                    html2canvas: { scale: 3 }, // Adjust the scale factor for better quality
                    jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' } // Optional: Adjust PDF settings
                });
            }
        });
    </script>
</body>
</html>