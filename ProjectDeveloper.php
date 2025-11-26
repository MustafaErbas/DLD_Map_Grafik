<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Proje Dağılımı</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
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

        /* Sol Panel */
        .developer-panel {
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
            display: flex;
            flex-direction: column;
            max-height: calc(85vh - 40px);
        }

        .panel-header {
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 16px 16px 0 0;
        }

        .panel-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 10px;
            display: block;
        }

        .search-box {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            outline: none;
            box-sizing: border-box;
            transition: all 0.2s;
        }

        .search-box:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .list-content {
            overflow-y: auto;
            flex-grow: 1;
            padding: 5px 0;
        }

        .list-content::-webkit-scrollbar {
            width: 6px;
        }

        .list-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .dev-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.2s;
        }

        .dev-item:hover {
            background-color: #f1f5f9;
        }

        .dev-item.active {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
        }

        .dev-name {
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            max-width: 75%;
        }

        .dev-badge {
            background: #e2e8f0;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
        }

        .dev-item.active .dev-badge {
            background: #bfdbfe;
            color: #1e40af;
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
            background: #1e293b;
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
    </style>
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div id="map-container">
        <div id="map"></div>

        <div class="developer-panel">
            <div class="panel-header">
                <span class="panel-title">Geliştirici Seçimi</span>
                <input type="text" id="searchInput" class="search-box" placeholder="Geliştirici ara... (Örn: EMAAR)">
            </div>
            <div class="list-content" id="devList">
                <div style="padding:20px; text-align:center; color:#999; font-size:12px;">Yükleniyor...</div>
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

        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
            zIndex: 1000
        }).addTo(map);

        var rawDeveloperData = {};
        var geojsonLayer;
        var geoJsonRawData = null;
        var selectedDeveloper = null;
        var mapNamesList = []; // Sadece isimleri tutan liste (Hızlı arama için)
        var activeMapping = {}; // O anki eşleştirme haritası

        // --- YARDIMCI FONKSİYONLAR ---
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

        // En iyi eşleşmeyi bul (Fuzzy Search)
        function findBestMatchInMap(targetName) {
            var bestMatch = null,
                bestScore = 0;
            mapNamesList.forEach(mapName => {
                var score = similarity(targetName, mapName);
                if (score > bestScore) {
                    bestScore = score;
                    bestMatch = mapName;
                }
            });
            return {
                name: bestMatch,
                score: bestScore
            };
        }

        // Eksik veya farklı isimler için Manuel Eşleştirme Listesi
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

        const currencyFormatter = new Intl.NumberFormat('en-AE', {
            style: 'currency',
            currency: 'AED',
            maximumFractionDigits: 1,
            notation: "compact"
        });

        // --- AKILLI EŞLEŞTİRME (YENİ MANTIK) ---
        // Bu fonksiyon seçilen developer'ın bölgelerini alır ve haritadaki TEK BİR bölgeyle eşleştirir.
        function mapDeveloperDataToGeoJson(devName) {
            activeMapping = {}; // Sıfırla: { "HARİTA_ADI": {count: 5, value: 100M} }

            var devData = rawDeveloperData[devName];
            if (!devData) return;

            // Developer'ın CSV'deki her bölgesi için döngü
            for (var csvAreaName in devData.areas) {
                var cleanCSV = normalizeName(csvAreaName);
                var targetMapName = null;

                // 1. Adım: Manuel Mapping Kontrolü
                if (nameMapping[csvAreaName] || nameMapping[cleanCSV]) {
                    var mappedName = nameMapping[csvAreaName] || nameMapping[cleanCSV];
                    targetMapName = normalizeName(mappedName);
                }
                // 2. Adım: Harita isimleri içinde Tam Eşleşme
                else if (mapNamesList.includes(cleanCSV)) {
                    targetMapName = cleanCSV;
                }
                // 3. Adım: Fuzzy (Benzerlik) Arama
                else {
                    var fuzzy = findBestMatchInMap(cleanCSV);
                    // Benzerlik oranı %70'in üzerindeyse kabul et
                    if (fuzzy.score > 0.70) {
                        targetMapName = fuzzy.name;
                    } else {
                        // Eşleşmeyenleri konsola yaz (Hata ayıklamak için)
                        console.warn("Eşleşmeyen Bölge:", csvAreaName, "-> En yakın:", fuzzy.name, fuzzy.score);
                    }
                }

                // Eğer bir eşleşme bulunduysa veriyi ata
                if (targetMapName) {
                    // Eğer bu harita bölgesi daha önce başkasıyla eşleştiyse topla (Örn: Business Bay ve Business Bay Phase 2 -> Business Bay)
                    if (!activeMapping[targetMapName]) {
                        activeMapping[targetMapName] = {
                            count: 0,
                            value: 0
                        };
                    }
                    activeMapping[targetMapName].count += devData.areas[csvAreaName].count;
                    activeMapping[targetMapName].value += devData.areas[csvAreaName].value;
                }
            }
        }

        // --- STİL VE TOOLTIP ---
        function style(feature) {
            // Varsayılan Stil
            var defaultStyle = {
                fillOpacity: 0,
                weight: 0.5,
                color: '#ccc',
                opacity: 0.5
            };

            if (!selectedDeveloper) return defaultStyle;

            var geoName = feature.properties.CNAME_E || feature.properties.LABEL_E || "";
            var cleanGeoName = normalizeName(geoName);

            // Eşleşmiş veriyi al (Artık doğrudan harita adıyla arıyoruz)
            var stats = activeMapping[cleanGeoName];

            if (stats && stats.count > 0) {
                var opacity = stats.count > 10 ? 0.9 : stats.count > 5 ? 0.75 : 0.6;
                return {
                    fillColor: '#2563eb',
                    weight: 1.5,
                    opacity: 1,
                    color: 'white',
                    fillOpacity: opacity
                };
            }
            return defaultStyle;
        }

        function onEachFeature(feature, layer) {
            if (!selectedDeveloper) return;

            // Tooltip verisini hazırla
            var geoName = feature.properties.CNAME_E || feature.properties.LABEL_E || "";
            var cleanGeoName = normalizeName(geoName);
            var stats = activeMapping[cleanGeoName];

            if (stats && stats.count > 0) {
                var valFmt = currencyFormatter.format(stats.value);
                var content = `
                    <div class="tooltip-header">${geoName}</div>
                    <div class="tooltip-body">
                        <div class="tooltip-row"><span>Geliştirici</span><span class="tooltip-val">${selectedDeveloper}</span></div>
                        <div class="tooltip-row"><span>Proje Sayısı</span><span class="tooltip-val">${stats.count}</span></div>
                        <div class="tooltip-row"><span>Toplam Değer</span><span class="tooltip-val" style="color:#2563eb">${valFmt}</span></div>
                    </div>
                `;
                layer.bindTooltip(content, {
                    className: 'custom-tooltip',
                    sticky: true,
                    direction: 'top'
                });

                layer.on({
                    mouseover: function(e) {
                        e.target.setStyle({
                            weight: 3,
                            color: '#1e40af',
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

        // --- SEÇİM VE LİSTELEME ---
        window.selectDeveloper = function(devName) {
            selectedDeveloper = devName;
            renderDeveloperList(document.getElementById('searchInput').value);

            // Önce eşleştirmeyi hesapla (Kritik Adım)
            mapDeveloperDataToGeoJson(devName);

            if (geojsonLayer) map.removeLayer(geojsonLayer);

            geojsonLayer = L.geoJson(geoJsonRawData, {
                style: style,
                onEachFeature: onEachFeature
            }).addTo(map);

            // Zoom Fit
            var bounds = L.latLngBounds([]);
            var hasLayer = false;
            geojsonLayer.eachLayer(function(layer) {
                var name = normalizeName(layer.feature.properties.CNAME_E || layer.feature.properties.LABEL_E);
                if (activeMapping[name]) {
                    bounds.extend(layer.getBounds());
                    hasLayer = true;
                }
            });
            if (hasLayer && bounds.isValid()) map.fitBounds(bounds, {
                padding: [50, 50]
            });
        };

        function renderDeveloperList(filterText = "") {
            var container = document.getElementById('devList');
            container.innerHTML = "";

            var devArray = Object.keys(rawDeveloperData).map(key => {
                return {
                    name: key,
                    count: rawDeveloperData[key].total_global_count
                };
            });

            devArray.sort((a, b) => b.count - a.count);

            var html = "";
            var countShown = 0;
            devArray.forEach(dev => {
                if (filterText === "" || dev.name.includes(filterText.toUpperCase())) {
                    // Performans için sadece ilk 100 sonucu göster (filtre yoksa)
                    if (filterText === "" && countShown > 100) return;

                    var activeClass = (selectedDeveloper === dev.name) ? "active" : "";
                    html += `
                        <div class="dev-item ${activeClass}" onclick="selectDeveloper('${dev.name}')">
                            <span class="dev-name">${dev.name}</span>
                            <span class="dev-badge">${dev.count} Proje</span>
                        </div>
                    `;
                    countShown++;
                }
            });

            if (html === "") html = '<div style="padding:20px; text-align:center; color:#999;">Sonuç bulunamadı.</div>';
            container.innerHTML = html;
        }

        // --- BAŞLANGIÇ ---
        function startApp() {
            document.getElementById('searchInput').addEventListener('keyup', function(e) {
                renderDeveloperList(e.target.value);
            });

            fetch('dubai.geojson')
                .then(res => res.json())
                .then(geoData => {
                    geoJsonRawData = geoData;

                    // Harita isim listesini bir kere oluştur (Hız için)
                    mapNamesList = [];
                    geoData.features.forEach(f => {
                        var name = f.properties.CNAME_E || f.properties.LABEL_E;
                        if (name) mapNamesList.push(normalizeName(name));
                    });

                    fetchData();
                });
        }

        function fetchData() {
            fetch('Api/api_developer_breakdown.php')
                .then(res => res.json())
                .then(data => {
                    rawDeveloperData = data;
                    renderDeveloperList();

                    // İlk açılış (Boş harita)
                    geojsonLayer = L.geoJson(geoJsonRawData, {
                        style: style
                    }).addTo(map);
                })
                .catch(err => {
                    console.error("API Hatası:", err);
                    document.getElementById('devList').innerHTML = "Veri yüklenemedi.";
                });
        }

        startApp();
    </script>
</body>

</html>