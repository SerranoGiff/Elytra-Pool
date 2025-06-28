// Real-time Crypto Table Script with QR Modal Logic

function showSkeleton() {
  const table = document.getElementById("crypto-table");
  table.innerHTML = "";
  for (let i = 0; i < 10; i++) {
    const row = document.createElement("tr");
    row.className = "animate-pulse";
    row.innerHTML = `
      <td class="px-4 py-3 text-left bg-[#1E2A3A]">---</td>
      <td class="px-4 py-3 text-left bg-[#1E2A3A]">Loading...</td>
      <td class="px-4 py-3 bg-[#1E2A3A]">---</td>
      <td class="px-4 py-3 bg-[#1E2A3A]">---</td>
      <td class="px-4 py-3 bg-[#1E2A3A]">---</td>
      <td class="px-4 py-3 bg-[#1E2A3A]">---</td>
      <td class="px-4 py-3 bg-[#1E2A3A]">---</td>
    `;
    table.appendChild(row);
  }
}

async function loadCryptoData() {
  showSkeleton();
  try {
    const res = await fetch("https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&per_page=100&page=1&price_change_percentage=1h,24h,7d");
    const data = await res.json();
    const table = document.getElementById("crypto-table");
    table.innerHTML = "";
    data.forEach((coin, index) => {
      const row = document.createElement("tr");
      row.className = "hover:bg-[#415A77]";
      row.innerHTML = `
        <td class="px-4 py-3 text-left">${index + 1}</td>
        <td class="px-4 py-3 text-left flex items-center gap-2">
          <img src="${coin.image}" alt="${coin.name}" class="w-5 h-5" />
          ${coin.name} <span class="text-gray-400 text-xs">(${coin.symbol.toUpperCase()})</span>
        </td>
        <td class="px-4 py-3">$${coin.current_price.toLocaleString()}</td>
        <td class="px-4 py-3" style="color:${getColor(coin.price_change_percentage_1h_in_currency)}">
          ${formatChange(coin.price_change_percentage_1h_in_currency)}%
        </td>
        <td class="px-4 py-3" style="color:${getColor(coin.price_change_percentage_24h)}">
          ${formatChange(coin.price_change_percentage_24h)}%
        </td>
        <td class="px-4 py-3" style="color:${getColor(coin.price_change_percentage_7d_in_currency)}">
          ${formatChange(coin.price_change_percentage_7d_in_currency)}%
        </td>
        <td class="px-4 py-3">$${coin.market_cap.toLocaleString()}</td>
      `;
      table.appendChild(row);
    });
  } catch (err) {
    console.error("Failed to load crypto data:", err);
    const table = document.getElementById("crypto-table");
    table.innerHTML = `<tr><td colspan="7" class="py-6 text-red-400">❌ Failed to load data. Please try again.</td></tr>`;
  }
}

function getColor(value) {
  return value >= 0 ? "lightgreen" : "red";
}

function formatChange(value) {
  return value ? value.toFixed(2) : "0.00";
}

function validateDepositForm() {
  const fileInput = document.getElementById("receiptUpload");
  const checkbox = document.getElementById("agreeCheckbox");
  const proceedBtn = document.getElementById("proceedBtn");
  proceedBtn.disabled = !(fileInput.files.length > 0 && checkbox.checked);
}

function openModal() {
  const modal = document.getElementById("qrModal");
  modal.classList.remove("hidden");
  modal.classList.add("flex");

  document.getElementById("receiptUpload").value = "";
  document.getElementById("depositAmount").value = "";
  document.getElementById("qrGenerated").classList.add("hidden");
}

function closeModal() {
  const modal = document.getElementById("qrModal");
  modal.classList.add("hidden");
  modal.classList.remove("flex");
}

function copyToClipboard() {
  const address = document.getElementById("walletAddress").textContent;
  navigator.clipboard.writeText(address)
    .then(() => alert("Wallet address copied!"))
    .catch(() => alert("Failed to copy address."));
}

function confirmDeposit() {
  const amount = document.getElementById("depositAmount").value;
  const file = document.getElementById("receiptUpload").files[0];

  if (!amount || !file) {
    alert("Please enter the amount and upload a receipt.");
    return;
  }

  alert("Deposit confirmed!\nAmount: $" + amount);
  closeModal();
}

function showQR() {
  document.getElementById("qrSection").classList.remove("hidden");
}

function generateQR() {
  const address = document.getElementById("walletAddress").textContent;
  const qrImage = document.getElementById("qrImage");
  const qrSection = document.getElementById("qrGenerated");

  qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(address)}`;
  qrSection.classList.remove("hidden");
}

// QR POPUP Generator
function generateQRPopup() {
  const address = document.getElementById("walletAddress").textContent;
  const qrImage = document.getElementById("qrImage");
  const qrWalletText = document.getElementById("qrWalletText");

  qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(address)}`;
  qrWalletText.textContent = address;

  document.getElementById("qrPopup").classList.remove("hidden");
}

function closeQRPopup() {
  document.getElementById("qrPopup").classList.add("hidden");
}

// Load crypto table on page ready
window.addEventListener("DOMContentLoaded", loadCryptoData);

// Ethereum Staking Update
async function updateEthereumStaking() {
  try {
    const res = await fetch("https://api.lido.fi/v1/protocol/stats");
    const data = await res.json();

    const totalStaked = parseFloat(data.totalPooledEther).toLocaleString(undefined, {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });

    const apy = (data.apr * 100).toFixed(2);

    document.getElementById("eth-total-staked").textContent = `${totalStaked} ETH`;
    document.getElementById("eth-apy").textContent = `${apy}% APY`;
  } catch (error) {
    console.error("Failed to fetch Ethereum staking data:", error);
    document.getElementById("eth-total-staked").textContent = "Unavailable";
    document.getElementById("eth-apy").textContent = "Unavailable";
  }
}

// Run staking update once on load
updateEthereumStaking();
// Then refresh every 15s
setInterval(updateEthereumStaking, 15000);

// Crypto update (make sure updateDashboard is defined elsewhere)
updateDashboard();
setInterval(updateDashboard, 5000);
