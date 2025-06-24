// mining.js
const ctx = document.getElementById("miningChart").getContext("2d");

// Initialize line chart
const miningChart = new Chart(ctx, {
  type: 'line',
  data: { labels: [], datasets: [{ label: 'BTC Earned', data: [], borderColor: '#4F46E5', backgroundColor: 'rgba(79,70,229,0.2)' }] },
  options: { scales: { x: { display: false }, y: { beginAtZero: true } } }
});

async function fetchMiningStats() {
  try {
    // TODO: Replace this simulated data with real API calls
    const now = new Date();
    const dataPoint = {
      time: now.toLocaleTimeString(),
      earnings: +(Math.random() * 0.0001 + 0.0005).toFixed(6),
      hashrate: +(Math.random() * 0.05 + 1).toFixed(2),
      progress: +(Math.random() * 5 + 70).toFixed(1)
    };

    return {
      status: 'Active',
      timestamp: now,
      ...dataPoint
    };
  } catch(err) {
    console.error(err);
    return null;
  }
}

async function updateDashboard() {
  const stats = await fetchMiningStats();
  if (!stats) return;

  document.getElementById('mining-status').textContent = stats.status;
  document.getElementById('hashrate').textContent = `${stats.hashrate} TH/s`;
  document.getElementById('daily-earnings').textContent = `${(stats.earnings * 24).toFixed(6)} BTC`; // extrapolate daily
  document.getElementById('progress-percent').textContent = `${stats.progress}%`;
  document.getElementById('progress-bar').style.width = `${stats.progress}%`;

  miningChart.data.labels.push(stats.timestamp.toLocaleTimeString());
  miningChart.data.datasets[0].data.push(stats.earnings);
  if (miningChart.data.labels.length > 20) {
    miningChart.data.labels.shift();
    miningChart.data.datasets[0].data.shift();
  }
  miningChart.update();
}

updateDashboard();
setInterval(updateDashboard, 5000); // update every 5s

async function updateEthereumStaking() {
    try {
        const res = await fetch('https://api.lido.fi/v1/protocol/stats');
        const data = await res.json();

        const totalStaked = parseFloat(data.totalPooledEther).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        const apy = (data.apr * 100).toFixed(2);

        // Update DOM
        document.getElementById("eth-total-staked").textContent = `${totalStaked} ETH`;
        document.getElementById("eth-apy").textContent = `${apy}% APY`;
    } catch (error) {
        console.error("Failed to fetch Ethereum staking data:", error);
        document.getElementById("eth-total-staked").textContent = "Unavailable";
        document.getElementById("eth-apy").textContent = "Unavailable";
    }
}

// Call on load
updateEthereumStaking();

// Refresh every 15 seconds
setInterval(updateEthereumStaking, 15000);