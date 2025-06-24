// Crypto update
updateDashboard();
setInterval(updateDashboard, 5000); // update every 5s

async function updateEthereumStaking() {
  try {
    const res = await fetch("https://api.lido.fi/v1/protocol/stats");
    const data = await res.json();

    const totalStaked = parseFloat(data.totalPooledEther).toLocaleString(
      undefined,
      {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }
    );

    const apy = (data.apr * 100).toFixed(2);

    // Update DOM
    document.getElementById(
      "eth-total-staked"
    ).textContent = `${totalStaked} ETH`;
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
