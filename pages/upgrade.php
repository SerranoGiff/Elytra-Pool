<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Upgrade to Premium | Elytra Pool</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    .glow {
      box-shadow: 0 0 25px #8b5cf6, 0 0 50px #7c3aed;
      animation: pulseGlow 2s infinite;
    }

    @keyframes pulseGlow {
      0%, 100% {
        box-shadow: 0 0 25px #8b5cf6, 0 0 50px #7c3aed;
      }
      50% {
        box-shadow: 0 0 35px #a78bfa, 0 0 60px #9333ea;
      }
    }
  </style>
</head>
<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center px-4">

  <div class="max-w-3xl w-full bg-[#1e1b36] border border-purple-600 rounded-2xl p-6 sm:p-10 glow relative">
   
    <button onclick="window.history.back()" 
      class="absolute top-4 right-4 z-50 bg-[#1e1b36] text-purple-300 hover:text-white hover:bg-purple-700 p-2 rounded-full text-xl shadow-md focus:outline-none">
      &times;
    </button>

    <div class="text-center mb-8">
      <h2 class="text-3xl sm:text-4xl font-bold text-purple-400">Upgrade to Premium</h2>
      <p class="text-sm sm:text-base text-gray-400 mt-2">Only <span class="text-green-400 font-bold">499 USDT</span> per month</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
      <div class="space-y-3">
        <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i> 1 TH/s Hashrate</div>
        <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i> Weekly Withdrawals</div>
        <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i> 1.9% Pool Fee</div>
        <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i> 24/7 Live Monitoring</div>
      </div>
      <div class="space-y-3">
        <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i> Priority Support Access</div>
        <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i> Unlimited Staking Cycles</div>
        <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i> Advanced Analytics Dashboard</div>
        <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i> Exclusive Access</div>
      </div>
    </div>

    <div class="text-center">
      <p class="mb-4 text-sm text-gray-400">Current Wallet Balance: <span id="balanceDisplay" class="text-white font-bold">--</span> USDT</p>
      <button onclick="subscribePremium()" class="bg-purple-600 hover:bg-purple-700 px-6 py-3 rounded-lg text-white font-semibold transition duration-300">
        Subscribe Now – 499 USDT
      </button>
      <p id="errorMsg" class="text-red-400 mt-4 hidden">⚠️ Insufficient balance. Please top up your wallet.</p>
    </div>
  </div>

  <script>
    async function fetchWalletBalance() {
  try {
    const res = await fetch("../config/fetch_wallet_balance.php");
    const data = await res.json();
    if (data.status === 'success') {
      const balance = parseFloat(data.balance);
      document.getElementById("balanceDisplay").innerText = balance.toFixed(2);
      window.userBalance = balance;
    } else {
      document.getElementById("balanceDisplay").innerText = 'Error';
    }
  } catch (e) {
    document.getElementById("balanceDisplay").innerText = 'Error';
  }
}

const subscriptionCost = 499;

function subscribePremium() {
  const error = document.getElementById("errorMsg");
  if (window.userBalance >= subscriptionCost) {
    fetch("../config/upgrade_premium.php", {
      method: "POST"
    }).then(res => res.json()).then(data => {
      if (data.status === 'success') {
        window.location.href = "premium-dashboard.html";
      } else {
        error.innerText = data.message;
        error.classList.remove("hidden");
      }
    });
  } else {
    error.classList.remove("hidden");
  }
}

fetchWalletBalance();

  </script>
</body>
</html>