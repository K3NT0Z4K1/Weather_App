<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>IoT Weather Station</title>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3"></script>
  <style>
    :root {
      --bg:        #070b14;
      --surface:   #0d1526;
      --card:      #111d35;
      --border:    rgba(99,179,255,0.12);
      --accent1:   #38b6ff;
      --accent2:   #ff6b6b;
      --accent3:   #43e97b;
      --text:      #e8f0fe;
      --muted:     #6b7fa3;
      --glow1:     rgba(56,182,255,0.18);
      --glow2:     rgba(255,107,107,0.15);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: 'Outfit', sans-serif;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* Animated background mesh */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background:
        radial-gradient(ellipse 80% 50% at 20% 10%, rgba(56,182,255,0.07) 0%, transparent 60%),
        radial-gradient(ellipse 60% 40% at 80% 80%, rgba(255,107,107,0.06) 0%, transparent 60%),
        radial-gradient(ellipse 50% 60% at 50% 50%, rgba(67,233,123,0.04) 0%, transparent 70%);
      pointer-events: none;
      z-index: 0;
    }

    .wrapper {
      position: relative;
      z-index: 1;
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem 1.5rem 4rem;
    }

    /* ── HEADER ── */
    header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 2.5rem;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .logo-group { display: flex; align-items: center; gap: 1rem; }

    .logo-icon {
      width: 52px; height: 52px;
      background: linear-gradient(135deg, var(--accent1), #0057a8);
      border-radius: 14px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.6rem;
      box-shadow: 0 0 24px var(--glow1);
    }

    .logo-text h1 {
      font-size: 1.5rem;
      font-weight: 700;
      letter-spacing: -0.5px;
      background: linear-gradient(90deg, var(--accent1), #a8d8ff);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .logo-text p {
      font-size: 0.78rem;
      color: var(--muted);
      font-family: 'JetBrains Mono', monospace;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .status-pill {
      display: flex; align-items: center; gap: 0.5rem;
      background: rgba(67,233,123,0.1);
      border: 1px solid rgba(67,233,123,0.25);
      border-radius: 999px;
      padding: 0.45rem 1rem;
      font-size: 0.82rem;
      color: var(--accent3);
      font-family: 'JetBrains Mono', monospace;
    }

    .status-dot {
      width: 8px; height: 8px;
      border-radius: 50%;
      background: var(--accent3);
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; transform: scale(1); }
      50%       { opacity: 0.4; transform: scale(0.8); }
    }

    /* ── STAT CARDS ── */
    .cards-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 1.2rem;
      margin-bottom: 2rem;
    }

    .card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 1.6rem 1.8rem;
      position: relative;
      overflow: hidden;
      transition: transform 0.25s, box-shadow 0.25s;
      animation: fadeUp 0.5s ease both;
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 40px rgba(0,0,0,0.4);
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 3px;
      border-radius: 20px 20px 0 0;
    }

    .card.blue::before  { background: linear-gradient(90deg, var(--accent1), #0057a8); }
    .card.red::before   { background: linear-gradient(90deg, var(--accent2), #c0392b); }
    .card.green::before { background: linear-gradient(90deg, var(--accent3), #11998e); }

    .card-label {
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: var(--muted);
      margin-bottom: 0.6rem;
    }

    .card-value {
      font-size: 2.6rem;
      font-weight: 900;
      line-height: 1;
      letter-spacing: -1px;
    }

    .card.blue  .card-value { color: var(--accent1); }
    .card.red   .card-value { color: var(--accent2); }
    .card.green .card-value { color: var(--accent3); }

    .card-unit {
      font-size: 1rem;
      font-weight: 400;
      color: var(--muted);
      margin-left: 4px;
    }

    .card-sub {
      margin-top: 0.6rem;
      font-size: 0.82rem;
      color: var(--muted);
      font-family: 'JetBrains Mono', monospace;
    }

    .card-icon {
      position: absolute;
      right: 1.4rem; bottom: 1.2rem;
      font-size: 2.8rem;
      opacity: 0.08;
    }

    .card-comment {
      margin-top: 0.5rem;
      font-size: 0.88rem;
      font-weight: 600;
    }

    .card.blue  .card-comment { color: var(--accent1); }
    .card.red   .card-comment { color: var(--accent2); }
    .card.green .card-comment { color: var(--accent3); }

    /* ── CHARTS ── */
    .charts-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.2rem;
      margin-bottom: 2rem;
    }

    @media (max-width: 700px) { .charts-grid { grid-template-columns: 1fr; } }

    .chart-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 1.6rem;
      animation: fadeUp 0.5s ease 0.2s both;
    }

    .chart-title {
      font-size: 0.82rem;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      color: var(--muted);
      margin-bottom: 1rem;
    }

    .chart-wrap {
      position: relative;
      height: 220px;
    }

    /* ── TABLE ── */
    .table-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 1.6rem;
      animation: fadeUp 0.5s ease 0.3s both;
      overflow: hidden;
    }

    .table-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.2rem;
    }

    .table-header h2 {
      font-size: 1rem;
      font-weight: 600;
      color: var(--text);
    }

    .table-header span {
      font-size: 0.75rem;
      color: var(--muted);
      font-family: 'JetBrains Mono', monospace;
    }

    .table-wrap { overflow-x: auto; }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.88rem;
    }

    thead th {
      text-align: left;
      padding: 0.6rem 1rem;
      color: var(--muted);
      font-weight: 600;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      border-bottom: 1px solid var(--border);
    }

    tbody tr {
      border-bottom: 1px solid rgba(99,179,255,0.05);
      transition: background 0.15s;
    }

    tbody tr:hover { background: rgba(56,182,255,0.04); }
    tbody tr:last-child { border-bottom: none; }

    tbody td {
      padding: 0.75rem 1rem;
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.82rem;
    }

    .td-temp  { color: var(--accent1); }
    .td-tempf { color: #80caff; }
    .td-hum   { color: var(--accent3); }
    .td-time  { color: var(--muted); }

    /* ── LOADING ── */
    #loading {
      display: flex; align-items: center; justify-content: center;
      height: 60vh; flex-direction: column; gap: 1rem;
    }

    .spinner {
      width: 40px; height: 40px;
      border: 3px solid var(--border);
      border-top-color: var(--accent1);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    #main { display: none; }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* ── FOOTER ── */
    footer {
      margin-top: 2rem;
      text-align: center;
      font-size: 0.75rem;
      color: var(--muted);
      font-family: 'JetBrains Mono', monospace;
    }

    footer span { color: var(--accent1); }
  </style>
</head>
<body>

<div class="wrapper">

  <!-- Header -->
  <header>
    <div class="logo-group">
      <div class="logo-icon">🌦️</div>
      <div class="logo-text">
        <h1>IoT Weather Station</h1>
        <p>ESP32 + DHT11 · Live Monitor</p>
      </div>
    </div>
    <div class="status-pill">
      <div class="status-dot"></div>
      <span id="status-text">Connecting...</span>
    </div>
  </header>

  <!-- Loading Spinner -->
  <div id="loading">
    <div class="spinner"></div>
    <p style="color:var(--muted); font-size:0.88rem;">Fetching sensor data...</p>
  </div>

  <!-- Main content (shown after data loads) -->
  <div id="main">

    <!-- Stat Cards -->
    <div class="cards-row">
      <div class="card blue" style="animation-delay:0s">
        <div class="card-label">Temperature</div>
        <div class="card-value"><span id="val-tempc">--</span><span class="card-unit">°C</span></div>
        <div class="card-sub" id="val-tempf">-- °F</div>
        <div class="card-comment" id="val-comment">--</div>
        <div class="card-icon">🌡️</div>
      </div>
      <div class="card green" style="animation-delay:0.1s">
        <div class="card-label">Humidity</div>
        <div class="card-value"><span id="val-hum">--</span><span class="card-unit">%</span></div>
        <div class="card-sub" id="val-hum-desc">--</div>
        <div class="card-icon">💧</div>
      </div>
      <div class="card red" style="animation-delay:0.2s">
        <div class="card-label">Heat Index</div>
        <div class="card-value"><span id="val-hi">--</span><span class="card-unit">°C</span></div>
        <div class="card-sub">Feels like</div>
        <div class="card-icon">☀️</div>
      </div>
      <div class="card blue" style="animation-delay:0.3s">
        <div class="card-label">Total Readings</div>
        <div class="card-value"><span id="val-count">--</span></div>
        <div class="card-sub">Last 50 shown</div>
        <div class="card-icon">📊</div>
      </div>
    </div>

    <!-- Charts -->
    <div class="charts-grid">
      <div class="chart-card">
        <div class="chart-title">🌡️ Temperature History (°C)</div>
        <div class="chart-wrap">
          <canvas id="tempChart"></canvas>
        </div>
      </div>
      <div class="chart-card">
        <div class="chart-title">💧 Humidity History (%)</div>
        <div class="chart-wrap">
          <canvas id="humChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="table-card">
      <div class="table-header">
        <h2>Recent Readings</h2>
        <span id="last-update">Last updated: --</span>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Temp (°C)</th>
              <th>Temp (°F)</th>
              <th>Humidity (%)</th>
              <th>Recorded At</th>
            </tr>
          </thead>
          <tbody id="readings-table"></tbody>
        </table>
      </div>
    </div>

  </div><!-- /main -->

  <footer>
    Auto-refreshes every <span>5 seconds</span> · Built with ESP32 + DHT11 + Chart.js
  </footer>

</div><!-- /wrapper -->

<script>
  // =============================================
  // CHANGE THIS to your fetch.php URL
  const API_URL = "http://192.168.1.100/weather/fetch.php";
  // =============================================

  let tempChart, humChart;
  let firstLoad = true;

  const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: '#111d35',
        borderColor: 'rgba(99,179,255,0.2)',
        borderWidth: 1,
        titleColor: '#6b7fa3',
        bodyColor: '#e8f0fe',
        padding: 10,
      }
    },
    scales: {
      x: {
        ticks: { color: '#6b7fa3', font: { family: 'JetBrains Mono', size: 10 }, maxTicksLimit: 8 },
        grid: { color: 'rgba(99,179,255,0.05)' }
      },
      y: {
        ticks: { color: '#6b7fa3', font: { family: 'JetBrains Mono', size: 10 } },
        grid: { color: 'rgba(99,179,255,0.05)' }
      }
    }
  };

  function initCharts(labels, tempData, humData) {
    const tempCtx = document.getElementById('tempChart').getContext('2d');
    const humCtx  = document.getElementById('humChart').getContext('2d');

    const tempGrad = tempCtx.createLinearGradient(0, 0, 0, 220);
    tempGrad.addColorStop(0, 'rgba(56,182,255,0.35)');
    tempGrad.addColorStop(1, 'rgba(56,182,255,0)');

    const humGrad = humCtx.createLinearGradient(0, 0, 0, 220);
    humGrad.addColorStop(0, 'rgba(67,233,123,0.35)');
    humGrad.addColorStop(1, 'rgba(67,233,123,0)');

    tempChart = new Chart(tempCtx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          data: tempData,
          borderColor: '#38b6ff',
          borderWidth: 2.5,
          backgroundColor: tempGrad,
          fill: true,
          tension: 0.4,
          pointRadius: 3,
          pointBackgroundColor: '#38b6ff',
          pointHoverRadius: 6,
        }]
      },
      options: { ...chartDefaults }
    });

    humChart = new Chart(humCtx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          data: humData,
          borderColor: '#43e97b',
          borderWidth: 2.5,
          backgroundColor: humGrad,
          fill: true,
          tension: 0.4,
          pointRadius: 3,
          pointBackgroundColor: '#43e97b',
          pointHoverRadius: 6,
        }]
      },
      options: { ...chartDefaults }
    });
  }

  function updateCharts(labels, tempData, humData) {
    tempChart.data.labels = labels;
    tempChart.data.datasets[0].data = tempData;
    tempChart.update('none');

    humChart.data.labels = labels;
    humChart.data.datasets[0].data = humData;
    humChart.update('none');
  }

  function heatIndex(T, H) {
    // Simplified Steadman heat index
    let hi = -8.78469475556 + 1.61139411*T + 2.33854883889*H
            - 0.14611605*T*H - 0.012308094*T*T
            - 0.0164248277778*H*H + 0.002211732*T*T*H
            + 0.00072546*T*H*H - 0.000003582*T*T*H*H;
    return hi.toFixed(1);
  }

  function tempComment(t) {
    if (t >= 35) return "🔥 Extreme heat! Stay indoors.";
    if (t >= 30) return "☀️ Hot day! Stay hydrated.";
    if (t >= 25) return "🌤️ Warm and pleasant.";
    if (t >= 20) return "😌 Nice and cool today.";
    if (t >= 15) return "🍃 A bit cool, light jacket.";
    return "🧥 Chilly! Bundle up.";
  }

  function humComment(h) {
    if (h >= 80) return "Very humid — feels heavy";
    if (h >= 60) return "Moderately humid";
    if (h >= 40) return "Comfortable humidity";
    return "Dry air today";
  }

  function formatTime(ts) {
    const d = new Date(ts);
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
  }

  async function fetchData() {
    try {
      const res  = await fetch(API_URL + "?t=" + Date.now());
      const data = await res.json();

      if (!data.length) return;

      const labels   = data.map(r => formatTime(r.recorded_at));
      const tempData = data.map(r => parseFloat(r.tempC));
      const humData  = data.map(r => parseFloat(r.humidity));

      const latest = data[data.length - 1];
      const tc = parseFloat(latest.tempC);
      const tf = parseFloat(latest.tempF);
      const h  = parseFloat(latest.humidity);

      // Update stat cards
      document.getElementById('val-tempc').textContent   = tc.toFixed(1);
      document.getElementById('val-tempf').textContent   = tf.toFixed(1) + " °F";
      document.getElementById('val-hum').textContent     = h.toFixed(1);
      document.getElementById('val-hum-desc').textContent = humComment(h);
      document.getElementById('val-comment').textContent = tempComment(tc);
      document.getElementById('val-hi').textContent      = heatIndex(tc, h);
      document.getElementById('val-count').textContent   = data.length;
      document.getElementById('last-update').textContent = "Last updated: " + new Date().toLocaleTimeString();
      document.getElementById('status-text').textContent = "Live · " + new Date().toLocaleTimeString();

      // Update charts
      if (firstLoad) {
        initCharts(labels, tempData, humData);
      } else {
        updateCharts(labels, tempData, humData);
      }

      // Update table (show last 10, newest first)
      const recent = [...data].reverse().slice(0, 10);
      const tbody  = document.getElementById('readings-table');
      tbody.innerHTML = recent.map((r, i) => `
        <tr>
          <td class="td-time">${i + 1}</td>
          <td class="td-temp">${parseFloat(r.tempC).toFixed(1)}</td>
          <td class="td-tempf">${parseFloat(r.tempF).toFixed(1)}</td>
          <td class="td-hum">${parseFloat(r.humidity).toFixed(1)}</td>
          <td class="td-time">${new Date(r.recorded_at).toLocaleString()}</td>
        </tr>
      `).join('');

      // Show main content on first load
      if (firstLoad) {
        document.getElementById('loading').style.display = 'none';
        document.getElementById('main').style.display    = 'block';
        firstLoad = false;
      }

    } catch (err) {
      console.error("Fetch error:", err);
      document.getElementById('status-text').textContent = "⚠ Connection error";
    }
  }

  fetchData();
  setInterval(fetchData, 5000);
</script>
</body>
</html>