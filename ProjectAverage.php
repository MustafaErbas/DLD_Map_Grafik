<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dubai Proje Değeri Haritası</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- TASARIM AYNEN KORUNDU --- */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        #map-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 94vw;
            height: 92vh;
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 4px solid white;
            z-index: 1;
            display: flex;
            flex-direction: column;
        }

        #map {
            flex-grow: 1;
            width: 100%;
            background: #f8f9fa;
            border-radius: 16px;
            z-index: 1;
        }

        /* Sol Üst Panel */
        .stats-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.8);
            width: 340px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .panel-title {
            font-size: 14px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
        }

        .stat-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            display: flex;
            flex-direction: column;
        }

        .stat-card.highlight {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-color: #bfdbfe;
        }

        .stat-label {
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-sub {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 500;
        }

        /* Tooltip */
        .custom-tooltip {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #333;
            padding: 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            font-family: 'Inter', sans-serif;
        }

        .tooltip-header {
            background: #0f172a;
            color: white;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 7px 7px 0 0;
        }

        .tooltip-body {
            padding: 10px 12px;
        }

        .tooltip-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 12px;
            gap: 15px;
        }

        .tooltip-val {
            font-weight: 700;
            color: #0f172a;
        }

        .leaflet-tooltip-left:before,
        .leaflet-tooltip-right:before {
            border: none;
        }

        /* Lejant */
        .legend {
            background: rgba(255, 255, 255, 0.95);
            padding: 12px;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            font-size: 12px;
            color: #475569;
            margin-bottom: 25px;
            margin-right: 25px;
            border: 1px solid #e2e8f0;
        }

        .legend-title {
            font-weight: 700;
            margin-bottom: 8px;
            display: block;
            color: #1e293b;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 4px;
        }

        .legend i {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            opacity: 1;
            border-radius: 4px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div id="map-container">
        <div id="map"></div>

        <div class="stats-panel">
            <div class="panel-title">Bölgesel Pazar Analizi</div>

            <div class="stat-card highlight">
                <span class="stat-label">GENEL ORTALAMA PROJE BEDELİ</span>
                <span class="stat-value" id="stat-avg" style="font-size:16px;">Hesaplanıyor...</span>
                <span class="stat-sub">Dubai Geneli</span>
            </div>

            <div style="display:flex; gap:10px;">
                <div class="stat-card" style="flex:1;">
                    <span class="stat-label" style="color:#08306b;">EN YÜKSEK HACİM</span>
                    <span class="stat-value" id="stat-high">...</span>
                    <span class="stat-sub" id="stat-high-name">...</span>
                </div>

                <div class="stat-card" style="flex:1;">
                    <span class="stat-label" style="color:#6baed6;">EN DÜŞÜK HACİM</span>
                    <span class="stat-value" id="stat-low">...</span>
                    <span class="stat-sub" id="stat-low-name">...</span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        var map = L.map('map', {
            zoomControl: false
        }).setView([25.12, 55.22], 11);
        L.control.zoom({
            position: 'bottomleft'
        }).addTo(map);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; CARTO',
            maxZoom: 19
        }).addTo(map);

        var projectData = {};
        var geojsonLayer;
        var geoJsonRawData = null;
        var mapNamesSet = new Set();

        function normalizeName(name) {
            if (!name) return "";
            var clean = String(name).toUpperCase().trim();
            clean = clean.replace(/\./g, ' ').replace(/-/g, ' ').replace(/_/g, ' ');
            clean = clean.replace(/\bIND\b/g, 'INDUSTRIAL').replace(/\bRES\b/g, 'RESIDENTIAL');
            clean = clean.replace(/\bFIRST\b|\b1ST\b/g, '1').replace(/\bSECOND\b|\b2ND\b/g, '2');
            clean = clean.replace(/\bTHIRD\b|\b3RD\b/g, '3').replace(/\bFOURTH\b|\b4TH\b/g, '4');
            clean = clean.replace(/\bFIFTH\b|\b5TH\b/g, '5').replace(/\bSIXTH\b|\b6TH\b/g, '6');
            clean = clean.replace("AL BARSHAA", "AL BARSHA").replace("AL GOZE", "AL QOUZ");
            return clean.replace(/\s+/g, ' ').trim();
        }

        function similarity(s1, s2) {
            var longer = s1,
                shorter = s2;
            if (s1.length < s2.length) {
                longer = s2;
                shorter = s1;
            }
            var longerLength = longer.length;
            if (longerLength == 0) return 1.0;
            return (longerLength - editDistance(longer, shorter)) / parseFloat(longerLength);
        }

        function editDistance(s1, s2) {
            s1 = s1.toLowerCase();
            s2 = s2.toLowerCase();
            var costs = new Array();
            for (var i = 0; i <= s1.length; i++) {
                var lastValue = i;
                for (var j = 0; j <= s2.length; j++) {
                    if (i == 0) costs[j] = j;
                    else {
                        if (j > 0) {
                            var newValue = costs[j - 1];
                            if (s1.charAt(i - 1) != s2.charAt(j - 1)) newValue = Math.min(Math.min(newValue, lastValue), costs[j]) + 1;
                            costs[j - 1] = lastValue;
                            lastValue = newValue;
                        }
                    }
                }
                if (i > 0) costs[s2.length] = lastValue;
            }
            return costs[s2.length];
        }

        function findBestMatch(targetName, candidates) {
            var bestMatch = null,
                bestScore = 0;
            candidates.forEach(candidate => {
                var score = similarity(targetName, candidate);
                if (score > bestScore) {
                    bestScore = score;
                    bestMatch = candidate;
                }
            });
            return {
                name: bestMatch,
                score: bestScore
            };
        }

        var nameMapping = {
            "DUBAI LAND RESIDENCE COMPLEX": "WADI AL SAFA 5",
            "DUBAI SCIENCE PARK": "AL BARSHA SOUTH 2",
            "DUBAI PRODUCTION CITY": "ME'AISEM 1",
            "MAJAN": "WADI AL SAFA 3",
            "ARJAN": "AL BARSHA SOUTH 3",
            "PALM DEIRA": "NAKHLAT DEIRA",
            "BUKADRA": "BU KADRA",
            "AL YELAYISS 1": "AL YALAYIS 1",
            "AL YELAYISS 2": "AL YALAYIS 2",
            "DUBAI MARITIME CITY": "MADINAT DUBAI AL MELAHEYAH",
            "DUBAI HILLS": "HADAEQ SHEIKH MOHAMMED BIN RASHID",
            "DUBAI HILLS ESTATE": "HADAEQ SHEIKH MOHAMMED BIN RASHID",
            "INTERNATIONAL CITY PH 1": "WARSAN 1",
            "INTERNATIONAL CITY PH 2 & 3": "WARSAN 4",
            "AL KHAIRAN 1": "AL KHEERAN 1",
            "DMCC EZ2": "AL THANYAH 5",
            "SOBHA HEARTLAND": "AL MERKADH",
            "DUBAI STUDIO CITY": "AL HEBIAH 2",
            "LIWAN": "WADI AL SAFA 2",
            "DUBAI INDUSTRIAL CITY": "SAIH SHUAIB 2",
            "JADDAF WATERFRONT": "AL JADAF",
            "EMIRATE LIVING": "AL THANYAH 3",
            "FALCON CITY OF WONDERS": "WADI AL SAFA 2",
            "DUBAI HEALTHCARE CITY PHASE 2": "AL JADAF",
            "TECOM SITE A": "AL THANYAH 1",
            "TECOM SITE D": "AL THANYAH 2",
            "BARSHA HEIGHTS": "AL THANYAH 2",
            "ZAABEEL 2": "ZAA'BEEL 2",
            "ARABIAN RANCHES I": "WADI AL SAFA 6",
            "ARABIAN RANCHES II": "WADI AL SAFA 7",
            "ARABIAN RANCHES III": "WADI AL SAFA 5",
            "NAD AL SHIBA 1": "NADD AL SHIBA 1",
            "AL AWEER 1": "AL AWIR 1",
            "EMAAR SOUTH": "HESSYAN 1",
            "DUBAI HARBOUR": "MARSA DUBAI",
            "DOWN TOWN JABAL ALI": "JABAL ALI 1",
            "VILLANOVA": "WADI AL SAFA 5",
            "JUMEIRAH GOLF": "ME'AISEM 1",
            "CITY OF ARABIA": "WADI AL SAFA 4",
            "JABEL ALI HILLS": "SAIH SHUAIB 1",
            "JUMEIRAH HEIGHTS": "AL THANYAH 5",
            "MEYDAN AVENUE": "AL MERKADH",
            "AL KHAIL HEIGHTS": "AL QOUZ 4",
            "DUBAI WATER CANAL": "AL SAFA 2",
            "MBR DISTRICT 1": "AL MERKADH",
            "THE LAKES": "AL THANYAH 3",
            "SUFOUH GARDENS": "AL SAFOUH 1",
            "SERENA": "WADI AL SAFA 7",
            "THE VALLEY": "AL YUFRAH 1",
            "AL KHAWANEEJ 2": "AL KHWANEEJ 2",
            "THE FIELD": "AL MERKADH",
            "TILAL AL GHAF": "AL HEBIAH 4",
            "MBR DISTRICT 7": "AL MERKADH",
            "NAD AL SHEBA GARDENS": "NADD AL SHIBA 1",
            "NAD AL HAMAR": "NADD AL HAMAR",
            "BUSINESS PARK": "HESSYAN 1",
            "ZAABEEL 1": "ZAA'BEEL 1",
            "AL YELAYISS 5": "AL YALAYIS 5",
            "SAMA AL JADAF": "AL JADAF",
            "PALM JABAL ALI": "JABAL ALI 1",
            "NAD AL SHIBA 3": "NADD AL SHIBA 3",
            "AL WARSAN 3": "WARSAN 3",
            "AL KHAWANEEJ 1": "AL KHWANEEJ 1",
            "JUMEIRAH LIVING": "MARSA DUBAI",
            "THE WORLD": "WORLD ISLANDS",
            "WADI AL AMARDI": "WADI ALAMARDI",
            "MUHAISANAH 1": "MUHAISNAH 1",
            "BLUEWATERS": "MARSA DUBAI",
            "BLUEWATERS ISLAND": "MARSA DUBAI",
            "AL WARQA 3": "AL WARQA'A 3",
            "AL WARQA 4": "AL WARQA'A 4",
            "GRAND VIEWS": "AL MERKADH",
            "SUSTAINABLE CITY": "WADI AL SAFA 7",
            "AL BARSHAA SOUTH 1": "AL BARSHA SOUTH 1",
            "AL WAHA": "WADI AL SAFA 7",
            "UM SUQAIM 1": "UMM SUQEIM 1",
            "UM AL SHEIF": "UMM AL SHEIF",
            "AL GOZE 1": "AL QOUZ 1",
            "AL BADA": "AL BADA'",
            "PEARL JUMEIRA": "JUMEIRA BAY",
            "NAD AL SHIBA 4": "NADD AL SHIBA 4",
            "AL WAHEDA": "AL WUHEIDA",
            "CHERRYWOODS": "AL YALAYIS 1",
            "AL MURQABAT": "AL MURAQQABAT",
            "UM SUQAIM 2": "UMM SUQEIM 2",
            "AL SUQ AL KABEER": "AL SOUQ AL KABEER",
            "RUKAN": "WADI AL SAFA 7",
            "AL WARQA 2": "AL WARQA'A 2",
            "DUBAI WATER FRONT": "HESSYAN 1",
            "AL BARSHAA SOUTH 3": "AL BARSHA SOUTH 3",
            "AL BARSHAA SOUTH 2": "AL BARSHA SOUTH 2",
            "JUMEIRAH 3": "JUMEIRA 3",
            "AL MAMZER": "AL MAMZAR",
            "AL AWEER 2": "AL AWIR 2",
            "LIWAN 2": "WADI AL SAFA 2",
            "DUBAI HEALTHCARE CITY PHASE 1": "UMM HURAIR 2",
            "AL JAFLIYA": "AL JAFILIYA",
            "AL WARQA 1": "AL WARQA'A 1",
            "NAD AL SHIBA 2": "NADD AL SHIBA 2",
            "MUSHRIF": "MUSHRAIF",
            "MILLENNIUM": "AL MERKADH",
            "AL DHAGAYA": "AL DAGHAYA",
            "MINA RASHID": "AL MINA",
            "AL WARSAN 2": "WARSAN 2",
            "EYAL NASSER": "AYAL NASIR",
            "AL KHABEESI": "AL KHABAISI",
            "ARABIAN RANCHES POLO CLUB": "AL HEBIAH 1",
            "MEDYAN RACE COURSE VILLAS": "AL MERKADH",
            "AL SAFFA 2": "AL SAFA 2",
            "DUBAI DESIGN DISTRICT": "ZAA'BEEL 2",
            "DUBAI GOLF CITY": "AL HEBIAH 4",
            "AL MARARR": "AL MURAR",
            "AL GOZE 4": "AL QOUZ 4",
            "UM HURAIR 1": "UMM HURAIR 1",
            "ISLAND 2": "JUMEIRA BAY",
            "AL SAFFA 1": "AL SAFA 1",
            "POLO TOWNHOUSES IGO": "AL MERKADH",
            "NAD SHAMMA": "NADD SHAMMA",
            "REGA AL BUTEEN": "RIGGAT AL BUTEEN",
            "UM RAMOOL": "UMM RAMOOL",
            "AL REGA": "AL RIGGA",
            "AL GOZE INDUSTRIAL 1": "AL QOUZ INDUSTRIAL 1",
            "AL GOZE INDUSTRIAL 4": "AL QOUZ INDUSTRIAL 4",
            "DUBAI LIFESTYLE CITY": "WADI AL SAFA 3",
            "PALMAROSA": "WADI AL SAFA 4",
            "AL LUSAILY": "AL LESAILY",
            "AL GOZE INDUSTRIAL 2": "AL QOUZ INDUSTRIAL 2",
            "UMM ADDAMIN": "UMM AL DAMAN",
            "AL GOZE 3": "AL QOUZ 3",
            "THE BEACH": "MARSA DUBAI",
            "AL EYAS": "ALEYAS",
            "UM HURAIR 2": "UMM HURAIR 2",
            "AL GOZE INDUSTRIAL 3": "AL QOUZ INDUSTRIAL 3",
            "DUBAI INTERNATIONAL AIRPORT": "DUBAI INT'L AIRPORT",
            "JUMEIRAH VILLAGE CIRCLE": "AL BARSHA SOUTH 4",
            "JVC": "AL BARSHA SOUTH 4",
            "JUMEIRAH VILLAGE TRIANGLE": "AL BARSHA SOUTH 5",
            "JVT": "AL BARSHA SOUTH 5",
            "DUBAI MARINA": "MARSA DUBAI",
            "JUMEIRAH BEACH RESIDENCE": "MARSA DUBAI",
            "JUMEIRAH LAKES TOWERS": "AL THANYAH 5",
            "JLT": "AL THANYAH 5",
            "BUSINESS BAY": "BUSINESS BAY",
            "DOWNTOWN DUBAI": "BURJ KHALIFA",
            "PALM JUMEIRAH": "NAKHLAT JUMEIRA",
            "DUBAI SPORTS CITY": "AL HEBIAH 4",
            "MOTOR CITY": "AL HEBIAH 1",
            "DAMAC HILLS": "AL HEBIAH 3",
            "DAMAC HILLS 2": "AL YUFRAH 2",
            "AKOYA OXYGEN": "AL YUFRAH 2",
            "DUBAI SOUTH": "HESSYAN 1",
            "DIP": "DUBAI INVESTMENT PARK 1",
            "SILICON OASIS": "NADD HESSA",
            "INTERNATIONAL CITY": "WARSAN 1",
            "CITY WALK": "AL WASL",
            "LA MER": "JUMEIRA 1",
            "DISCOVERY GARDENS": "JABAL ALI 1",
            "AL FURJAN": "JABAL ALI 1",
            "THE GARDENS": "JABAL ALI 1",
            "MADINAT DUBAI ALMELAHEYAH": "MADINAT DUBAI AL MELAHEYAH",
            "HORIZON": "MARSA DUBAI",
            "LIVING LEGENDS": "WADI AL SAFA 2",
            "DUBAI CREEK HARBOUR": "AL KHEERAN 1",
            "JUMEIRAH 2": "JUMEIRA 2",
            "JUMEIRAH 1": "JUMEIRA 1",
            "MEYDAN ONE": "AL MERKADH",
            "TOWN SQUARE": "AL YALAYIS 2"
        };

        // --- SKALA (TOPLAM BÖLGE DEĞERİ) ---
        // 50M ile 4B Aralığı
        function getColor(totalValue) {
            return totalValue > 4000000000 ? '#08306b' : // > 4 Milyar (En Koyu)
                totalValue > 2000000000 ? '#08519c' : // 2B - 4B
                totalValue > 1000000000 ? '#2171b5' : // 1B - 2B
                totalValue > 500000000 ? '#4292c6' : // 500M - 1B
                totalValue > 250000000 ? '#6baed6' : // 250M - 500M
                totalValue > 100000000 ? '#9ecae1' : // 100M - 250M
                totalValue > 50000000 ? '#c6dbef' : // 50M - 100M
                totalValue > 0 ? '#deebf7' : // < 50M (En Açık)
                'transparent';
        }

        // 1. TAM FORMAT (Genel Ortalama İçin) - Kısaltma Yok
        const fullPriceFormatter = new Intl.NumberFormat('en-AE', {
            style: 'currency',
            currency: 'AED',
            maximumFractionDigits: 0
        });

        // 2. KISALTILMIŞ FORMAT (Tooltip ve İstatistikler İçin) - 1.2B, 500M gibi
        const compactPriceFormatter = new Intl.NumberFormat('en-AE', {
            style: 'currency',
            currency: 'AED',
            maximumFractionDigits: 1,
            notation: "compact",
            compactDisplay: "short"
        });

        function style(feature) {
            var geoName = feature.properties.CNAME_E || feature.properties.LABEL_E || "";
            var cleanGeoName = normalizeName(geoName);
            var data = projectData[cleanGeoName];

            var totalVal = 0;
            if (data) {
                totalVal = data.total_value;
            }

            if (totalVal === 0) return {
                fillOpacity: 0,
                weight: 0.8,
                color: '#e2e8f0',
                opacity: 0.6
            };

            return {
                fillColor: getColor(totalVal),
                weight: 1,
                opacity: 1,
                color: 'white',
                fillOpacity: 0.8
            };
        }

        function onEachFeature(feature, layer) {
            var geoName = feature.properties.CNAME_E || feature.properties.LABEL_E || "";
            var cleanGeoName = normalizeName(geoName);
            var data = projectData[cleanGeoName];

            if (data && data.count > 0) {
                // Tooltip Verileri
                var formattedTotal = compactPriceFormatter.format(data.total_value);

                var avgPrice = data.total_value / data.count;
                var formattedAvg = compactPriceFormatter.format(avgPrice);

                var tooltipContent = `
                    <div class="tooltip-header">${geoName}</div>
                    <div class="tooltip-body">
                        <div class="tooltip-row"><span>Toplam Pazar Değeri</span><span class="tooltip-val" style="color:#08519c">${formattedTotal}</span></div>
                        <div class="tooltip-row"><span>Ort. Proje Değeri</span><span class="tooltip-val">${formattedAvg}</span></div>
                        <div class="tooltip-row"><span>Proje Sayısı</span><span class="tooltip-val">${data.count} Adet</span></div>
                    </div>`;

                layer.bindTooltip(tooltipContent, {
                    permanent: false,
                    direction: 'top',
                    className: 'custom-tooltip',
                    sticky: true
                });

                layer.on({
                    mouseover: function(e) {
                        e.target.setStyle({
                            weight: 2,
                            color: '#333',
                            fillOpacity: 1
                        });
                        e.target.bringToFront();
                    },
                    mouseout: function(e) {
                        geojsonLayer.resetStyle(e.target);
                    }
                });
            }
        }

        function calculateStats(dataObj) {
            var globalTotalValue = 0;
            var globalTotalCount = 0;

            // En Yüksek / En Düşük Toplam Değer (Volume) için
            var minTotal = Infinity;
            var minName = "-";

            var maxTotal = 0;
            var maxName = "-";

            for (var key in dataObj) {
                var item = dataObj[key];
                if (item.count > 0) {
                    // Global Hesaplama
                    globalTotalValue += item.total_value;
                    globalTotalCount += item.count;

                    // En Yüksek Hacim (Total Value)
                    if (item.total_value > maxTotal) {
                        maxTotal = item.total_value;
                        maxName = key;
                    }
                    // En Düşük Hacim
                    if (item.total_value < minTotal) {
                        minTotal = item.total_value;
                        minName = key;
                    }
                }
            }

            // Dubai Genel Ortalaması (Bütün projelerin ortalaması)
            var globalAvg = globalTotalCount > 0 ? (globalTotalValue / globalTotalCount) : 0;

            // 1. Genel Ortalama -> TAM RAKAM
            document.getElementById('stat-avg').innerText = fullPriceFormatter.format(globalAvg);

            // 2. En Yüksek Hacimli Bölge -> KISALTMA
            document.getElementById('stat-high').innerText = compactPriceFormatter.format(maxTotal);
            document.getElementById('stat-high-name').innerText = maxName;
            document.getElementById('stat-high-name').title = maxName;

            // 3. En Düşük Hacimli Bölge -> KISALTMA
            document.getElementById('stat-low').innerText = compactPriceFormatter.format(minTotal === Infinity ? 0 : minTotal);
            document.getElementById('stat-low-name').innerText = minName;
            document.getElementById('stat-low-name').title = minName;
        }

        function startApp() {
            fetch('dubai.geojson')
                .then(res => res.json())
                .then(geoData => {
                    geoJsonRawData = geoData;
                    geoData.features.forEach(f => {
                        var name = f.properties.CNAME_E || f.properties.LABEL_E;
                        if (name) mapNamesSet.add(normalizeName(name));
                    });
                    fetchProjects();
                });
        }

        function fetchProjects() {
            fetch('Api/api_projects.php')
                .then(res => res.json())
                .then(data => {
                    projectData = {};

                    for (var csvKey in data) {
                        var cleanCSV = normalizeName(csvKey);
                        var record = data[csvKey];
                        var mapName = nameMapping[csvKey] || nameMapping[cleanCSV];
                        var targetKey = mapName ? normalizeName(mapName) : cleanCSV;

                        if (!mapNamesSet.has(targetKey)) {
                            var fuzzyMatch = findBestMatch(targetKey, Array.from(mapNamesSet));
                            if (fuzzyMatch.score > 0.75) targetKey = fuzzyMatch.name;
                        }

                        if (mapNamesSet.has(targetKey)) {
                            if (!projectData[targetKey]) {
                                projectData[targetKey] = {
                                    count: 0,
                                    total_value: 0
                                };
                            }
                            projectData[targetKey].count += record.count;
                            projectData[targetKey].total_value += record.total_value;
                        }
                    }

                    drawMap();
                    calculateStats(projectData);
                })
                .catch(err => console.error("API Hatası:", err));
        }

        function drawMap() {
            if (geojsonLayer) map.removeLayer(geojsonLayer);
            geojsonLayer = L.geoJson(geoJsonRawData, {
                style: style,
                onEachFeature: onEachFeature
            }).addTo(map);
            try {
                map.fitBounds(geojsonLayer.getBounds());
            } catch (e) {}

            var legend = L.control({
                position: 'bottomright'
            });
            legend.onAdd = function(map) {
                var div = L.DomUtil.create('div', 'info legend');
                div.innerHTML += '<span class="legend-title">Bölge Toplam Değeri</span>';

                // Yeni Aralıklar: 0, 50M, 100M, 250M, 500M, 1B, 2B, 4B
                var grades = [0, 50000000, 100000000, 250000000, 500000000, 1000000000, 2000000000, 4000000000];
                var labels = ["< 50M", "50M - 100M", "100M - 250M", "250M - 500M", "500M - 1B", "1B - 2B", "2B - 4B", "> 4B"];

                for (var i = 0; i < grades.length; i++) {
                    // Rengi doğru yakalamak için aralığın biraz üzerini (+100) gönderiyoruz
                    var color = getColor(grades[i] + 100);
                    div.innerHTML += '<div class="legend-item"><i style="background:' + color + '"></i><span>' + labels[i] + '</span></div>';
                }
                return div;
            };
            legend.addTo(map);
        }

        startApp();
    </script>
</body>

</html>