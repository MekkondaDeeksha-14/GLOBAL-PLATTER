<?php 
include("db.php"); // DB connection

// Fetch all chefs (only country is really needed now)
$query = "SELECT DISTINCT country FROM chefs";
$result = mysqli_query($conn, $query);

$countries = [];
while ($row = mysqli_fetch_assoc($result)) {
    $countries[] = $row['country'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chefs | Global Platter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        /* ================= GLOBAL ================= */
        html, body {
            height: 100%;
            margin: 0;
            font-family: Georgia, serif;
        }

        .restaurants-page {
            display: flex;
            height: calc(100% - 70px); /* leave space for navbar */
            margin-top: 70px; /* navbar height */
        }

        /* ================= MAP ================= */
        #map {
            flex: 2;
            height: 100%;
        }

        /* ================= SIDEBAR ================= */
        #sidebar {
            flex: 1;
            padding: 20px;
            background: #fff8e7;
            overflow-y: auto;
            border-left: 2px solid #6b0000;
        }

        #sidebar h2 {
            color: #6b0000;
            text-align: center;
            margin-bottom: 20px;
        }

        .country-center {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .country-center a {
            padding: 15px 30px;
            font-size: 26px;
            font-weight: bold;
            text-decoration: none;
            color: white;
            background-color: #800000;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .country-center a:hover {
            background-color: #d4af37;
            color: #000;
            transform: scale(1.05);
        }

        /* ================= NAVBAR ================= */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            height: 70px;
            background-color: #6b0000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            z-index: 999;
        }

        .navbar .logo a {
            color: #d4af37;
            font-weight: bold;
            font-size: 1.8rem;
            text-decoration: none;
        }

        .nav-links a {
            color: #fff8e7;
            margin-right: 40px;
            font-weight: bold;
            text-decoration: none;
        }

        .nav-links a.active {
            color: #d4af37;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<header class="navbar">
    <div class="logo">
        <a href="LandingPage.php"><b>GLOBAL PLATTER</b></a>
    </div>

    <nav class="nav-links">
        <a href="Login.php">Login/Signup</a>
        <a href="profile.php">My Profile</a>
        <a href="meme_generator.php">Make a Meme</a>
        <a href="Chefs.php" class="active">Chefs</a>
        <a href="About.php">About Us</a>
    </nav>
</header>

<!-- Page content -->
<div class="restaurants-page">
    <div id="map"></div>
    <div id="sidebar">
        <h2>Famous Chefs</h2>
        <div id="chef-list">
            <p>Click a country on the map</p>
        </div>
    </div>
</div>

<script>
    // Initialize map
    var map = L.map('map', { center: [20, 0], zoom: 2 });

    // Ocean background
    L.rectangle([[-90, -180], [90, 180]], {
        color: "#87CEEB",
        fillColor: "#87CEEB",
        fillOpacity: 1
    }).addTo(map);

    let selectedLayer = null;

    // Load GeoJSON countries
    fetch("https://raw.githubusercontent.com/johan/world.geo.json/master/countries.geo.json")
        .then(res => res.json())
        .then(data => {
            L.geoJSON(data, {
                style: {
                    color: "#ffffff",
                    weight: 1,
                    fillColor: "#e0c68c",
                    fillOpacity: 1
                },
                onEachFeature: function (feature, layer) {
                    layer.on('click', function () {
                        let country = feature.properties.name;
                        let sidebar = document.getElementById("chef-list");

                        if (selectedLayer) {
                            selectedLayer.setStyle({ fillColor: "#e0c68c" });
                        }

                        layer.setStyle({ fillColor: "#800000" });
                        selectedLayer = layer;

                        map.fitBounds(layer.getBounds());

                        const countryUrl = "CountryChefs.php?country=" + encodeURIComponent(country);

                        sidebar.innerHTML = `
                            <div class="country-center">
                                <a href="${countryUrl}">
                                    ${country}
                                </a>
                            </div>
                        `;
                    });

                    layer.on('mouseover', function () {
                        if (layer !== selectedLayer) {
                            layer.setStyle({ fillColor: "#d4af37" });
                        }
                    });

                    layer.on('mouseout', function () {
                        if (layer !== selectedLayer) {
                            layer.setStyle({ fillColor: "#e0c68c" });
                        }
                    });
                }
            }).addTo(map);
        })
        .catch(err => console.error("Failed to load GeoJSON:", err));
</script>

</body>
</html>
