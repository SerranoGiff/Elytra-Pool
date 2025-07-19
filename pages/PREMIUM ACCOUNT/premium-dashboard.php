<?php
session_start();
include '../../config/dbcon.php';

// NO CACHE HEADERS
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// VALIDATE SESSION
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'premium') {
  header("Location: ../../index.php?error=Unauthorized access.");
  exit;
}

$userId = $_SESSION['user_id'] ?? null;

// Fetch username (used in referral code)
if ($userId) {
  $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  $username = $user ? $user['username'] : 'User';
} else {
  $username = 'Guest';
}

// Get full user details
$query = "SELECT first_name, last_name, birthday, username, about_me, email, wallet_address, profile_photo 
          FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Set default values
$firstName = $user['first_name'] ?? 'N/A';
$lastName = $user['last_name'] ?? 'N/A';
$birthday = $user['birthday'] ?? '';
$username = $user['username'] ?? 'N/A';
$aboutMe = $user['about_me'] ?? 'N/A';
$email = $user['email'] ?? 'N/A';
$walletAddress = $user['wallet_address'] ?? '';
$photoPath = $user['profile_photo'] ?? '';

// Check if file exists and construct proper image URL (relative to public access)
if (!empty($photoPath) && file_exists("../../" . $photoPath)) {
  $profileImg = "../../" . $photoPath;
} else {
  $profileImg = '../../assets/default-avatar.png';
}

// Append cache buster to avoid cached image
$profileImg .= '?v=' . time();

// Generate referral code
$referralSuffix = sprintf('%04d', $userId % 10000); // consistent & unique per user
$referralCode = $username . $referralSuffix;
$referralLink = "https://elytra.io/referral/" . $referralCode;
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Elytra Pool | Asset Wallet</title>
  <link rel="stylesheet" href="../../assets/css/style.css" />
  <link rel="stylesheet" href="../../assets/css/user.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="shortcut icon" href="../../assets/img/ELYTRA.jpg" type="image/x-icon" />
  <!-- Alertify CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
  <!-- Optional themes -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen">
  <!-- Navigation -->
  <nav
    class="fixed top-0 left-0 w-full z-50 backdrop-blur-lg bg-white/10 border-b border-white/10 shadow-md transition-all duration-500 ease-in-out">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
      <!-- Logo -->
      <div class="flex items-center space-x-2">
        <a href="premium-dashboard.php" class="flex items-center">
          <div
            class="w-10 h-10 rounded-full flex items-center justify-center pulse hover:scale-105 transition-transform duration-200">
            <img src="../../assets/img/Elytra Logo.png" alt="Elytra Logo"
              class="w-full h-full rounded-full object-cover" />
          </div>
          <span
            class="text-xl font-bold text-white hover:text-blue-200 transition-colors duration-200 flex items-center gap-1">
            Elytra Pool
            <span class=" text-s font-semibold px-2 py-0.5 rounded-full animate-pulse flex items-center gap-1">
              <i class="fas fa-crown text-yellow-400"></i>
            </span>
          </span>
        </a>
      </div>

      <!-- Desktop Nav Links -->
      <div class="hidden md:flex nav-links space-x-6 items-center" id="nav-links">
        <a href="premium-dashboard.php"
          class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Home</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="premium-staking.php"
          class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Staking</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="premium-leaderboard.php"
          class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Leaderboard</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="premium-deposit.php" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Deposit</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="premium-withdraw.php"
          class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Withdraw</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="premium-convert.php" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Convert</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
      </div>


      <!-- Desktop Profile -->
      <div class="relative hidden md:block">
        <button id="profileBtn" class="focus:outline-none">
          <img src="<?= htmlspecialchars($profileImg) ?>" alt="Profile"
            class="w-10 h-10 rounded-full border-2 border-purple-400 object-cover" />
        </button>
        <div id="profileMenu"
          class="absolute right-0 mt-2 w-40 bg-purple-100 rounded-lg shadow-lg text-sm text-black hidden z-50">
          <a href="premium-settings.php" class="block px-4 py-2 hover:bg-gray-100">Settings</a>
          <a href="../../config/logout.php" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
        </div>
      </div>

      <!-- Mobile Menu & Profile -->
      <div class="md:hidden flex items-center gap-4">
        <button id="menu-button" aria-label="Toggle navigation">
          <i class="fas fa-bars text-xl text-white"></i>
        </button>
        <div class="relative">
          <button id="mobileProfileBtn" class="focus:outline-none">
            <img src="<?= htmlspecialchars($profileImg) ?>" alt="Profile"
              class="w-10 h-10 rounded-full border-2 border-purple-400 object-cover" />
          </button>
          <div id="mobileProfileMenu"
            class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg text-sm text-black hidden z-50">
            <a href="premium-settings.php" class="block px-4 py-2 hover:bg-gray-100">Settings</a>
            <a href="../../config/logout.php" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Navigation Links -->
    <div id="mobile-menu" class="nav-links">
      <a href="premium-dashboard.php" class="nav-link">Home</a>
      <a href="premium-staking.php" class="nav-link">Staking</a>
      <a href="premium-leaderboard.php" class="nav-link">Leaderboard</a>
      <a href="premium-deposit.php" class="nav-link">Deposit</a>
      <a href="premium-withdraw.php" class="nav-link">Withdraw</a>
      <a href="premium-convert.php" class="nav-link">Convert</a>
    </div>
  </nav>

  <!-- Dashboard Main Content -->
  <main id="dashboard" class="max-w-7xl mx-auto px-4 pt-24 pb-10">
    <?php if (isset($_GET['success']) || isset($_GET['error'])): ?>
      <script>
        document.addEventListener("DOMContentLoaded", () => {
          const message = <?= json_encode($_GET['error'] ?? $_GET['success']) ?>;
          const type = <?= isset($_GET['error']) ? json_encode('error') : json_encode('success') ?>;
          showToast(message, type);
          history.replaceState(null, "", window.location.pathname);
        });
      </script>
    <?php endif; ?>

    <!-- Section: Dashboard Header -->
    <section aria-label="Dashboard Heading" class="mb-8">
      <div class="flex justify-between items-center">
        <h1 class="text-4xl font-bold text-purple-400">
          <?php echo htmlspecialchars($username); ?> Wallet Dashboard
        </h1>
      </div>
    </section>

    <!-- Wallet Card -->
    <div class="relative bg-[#1a1f36] p-6 rounded-xl shadow-lg border border-purple-500">

      <!-- Desktop-only Last Activity + badges -->
      <div class="absolute top-2 right-6 hidden md:flex flex-col items-end gap-1">
        <p id="lastActivity" class="text-sm text-gray-400 font-semibold">
          Last Activity: Loading...
        </p>
        <div class="flex gap-2">
          <div class="flex items-center gap-1 text-xs bg-blue-900/60 text-blue-300 px-2 py-0.5 rounded-full border border-blue-500">
            <i class="fas fa-bolt text-blue-400 text-xs"></i>
            1 TH/s
          </div>
          <div class="flex items-center gap-1 text-xs bg-green-900/60 text-green-300 px-2 py-0.5 rounded-full border border-green-500">
            <i class="fas fa-percentage text-green-400 text-xs"></i>
            1.9% Fee
          </div>
        </div>
      </div>

      <!-- Mobile-only Last Activity -->
      <p id="lastActivityMobile" class="text-sm text-gray-400 font-semibold mb-1 md:hidden text-end">
        Last Activity: Loading...
      </p>

      <!-- Wallet Balance -->
      <p class="text-white font-semibold text-lg mb-1">Wallet Balance (Total)</p>
      <p id="totalBalance" class="text-4xl font-bold text-[#c084fc] mb-2">$0.00</p>

      <!-- Mobile-only badges -->
      <div class="flex gap-2 mb-3 md:hidden">
        <div class="flex items-center gap-1 text-xs bg-blue-900/60 text-blue-300 px-2 py-0.5 rounded-full border border-blue-500">
          <i class="fas fa-bolt text-blue-400 text-xs"></i>
          1 TH/s
        </div>
        <div class="flex items-center gap-1 text-xs bg-green-900/60 text-green-300 px-2 py-0.5 rounded-full border border-green-500">
          <i class="fas fa-percentage text-green-400 text-xs"></i>
          1.9% Fee
        </div>
      </div>

      <div class="text-sm text-slate-400 mb-4">Secure and ready to use</div>

      <!-- Balances -->
      <div class="grid grid-cols-2 gap-4 text-sm mb-4">
        <div>
          <p class="text-slate-400">Bitcoin</p>
          <p id="btcBalance" class="text-yellow-300 font-bold">Loading...</p>
        </div>
        <div>
          <p class="text-slate-400">Ethereum</p>
          <p id="ethBalance" class="text-purple-300 font-bold">Loading...</p>
        </div>
        <div>
          <p class="text-slate-400">Tether</p>
          <p id="usdtBalance" class="text-green-300 font-bold">Loading...</p>
        </div>
        <div>
          <p class="text-slate-400">Elytrs</p>
          <p id="elytrsBalance" class="text-blue-300 font-bold">Loading...</p>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-2 mt-2">
        <a href="premium-deposit.php" class="flex-1 flex items-center justify-center gap-1 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white text-xs py-2 rounded transition duration-300 shadow-md shadow-purple-800/30">
          <i class="fas fa-arrow-down"></i> Deposit
        </a>
        <a href="premium-withdraw.php" class="flex-1 flex items-center justify-center gap-1 bg-gradient-to-r from-pink-500 to-red-500 hover:from-pink-400 hover:to-red-400 text-white text-xs py-2 rounded transition duration-300 shadow-md shadow-red-800/30">
          <i class="fas fa-arrow-up"></i> Withdraw
        </a>
        <a href="premium-convert.php" class="flex-1 flex items-center justify-center gap-1 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white text-xs py-2 rounded transition duration-300 shadow-md shadow-blue-800/30">
          <i class="fas fa-sync-alt"></i> Convert
        </a>
      </div>
    </div>

    <!-- Earnings Chart -->
    <div class="mt-8 bg-[#1a1f36] border border-purple-600 p-6 rounded-xl shadow-md">
      <h2 class="text-purple-300 text-lg font-bold mb-4">Earnings Overview</h2>
      <canvas id="earningsChart" height="100"></canvas>
    </div>

    <!-- Referral Card -->
    <div class="relative bg-[#1a1f36] p-6 rounded-xl shadow-lg border border-purple-500 mt-10 mb-8">
      <h2 class="text-xl font-bold text-purple-300 mb-2">Referral Program</h2>
      <p class="text-sm text-slate-400 mb-4">
        Get <span class="text-purple-400 font-semibold">200 ELTR</span> for every referral that deposits
        <span class="text-green-400 font-semibold">1,000 USDT</span>.
      </p>

      <!-- Referral Link Input Box -->
      <div class="flex items-center gap-2">
        <input id="referralLink" type="text" readonly value="<?= htmlspecialchars($referralLink) ?>"
          class="flex-1 px-4 py-2 bg-[#111827] border border-purple-600 text-white rounded-lg text-sm select-all" />
        <button onclick="copyReferralLink()"
          class="px-4 py-2 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white rounded-lg text-sm shadow-md shadow-purple-800/30">
          Copy
        </button>
      </div>
    </div>


    <!-- Transactions -->
    <div class="bg-[#1a1f36] p-6 rounded-xl shadow-lg border border-purple-500">
      <p class="text-white font-semibold text-lg mb-4">Transaction History</p>
      <div id="transactionList" class="space-y-4 max-h-64 overflow-y-auto text-sm">
        <p class="text-slate-400 text-center">Loading...</p>
      </div>
    </div>

    <section class="mt-12">
      <div
        class="bg-[#1e1b36] border-2 border-purple-600 rounded-2xl shadow-2xl p-6 sm:p-10 w-full max-w-4xl mx-auto neon-glow">
        <div class="flex flex-col lg:flex-row justify-between gap-6">

          <!-- FREE PLAN -->
          <div
            class="w-full lg:w-1/2 bg-[#1a1f36] rounded-xl p-6 flex flex-col justify-between border border-emerald-500">
            <div>
              <h2 class="text-xl font-bold text-emerald-400 mb-4">Free Plan</h2>
              <ul class="space-y-3 text-sm text-white">
                <li class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i>100 GH/s Hashrate</li>
                <li class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i>2x a Month Withdrawal</li>
                <li class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i>5% Pool Fee</li>
                <li class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i>Basic Analytics</li>
                <li class="flex items-center"><i class="fas fa-times text-red-400 mr-2"></i>Priority Support</li>
                <li class="flex items-center"><i class="fas fa-times text-red-400 mr-2"></i>Unlimited Staking</li>
              </ul>
            </div>
            <p class="text-center text-xs text-gray-400 mt-6">Thank you for purchasing premium</p>
          </div>

          <!-- PREMIUM PLAN -->
          <div
            class="w-full lg:w-1/2 bg-gradient-to-br from-[#301934] via-[#3b1c5f] to-[#281c36] rounded-xl p-6 relative border border-purple-500 shadow-lg flex flex-col justify-between">
            <div
              class="absolute top-4 right-4 bg-yellow-400 text-black text-xs font-bold px-3 py-1 rounded-full animate-pulse shadow-md">
              PREMIUM
            </div>
            <div>
              <h2 class="text-xl font-bold text-purple-300 mb-4">Premium Plan</h2>
              <ul class="space-y-3 text-sm text-white">
                <li class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i>1 TH/s Hashrate</li>
                <li class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i>Weekly Withdrawals</li>
                <li class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i>1.9% Pool Fee</li>
                <li class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i>Unlimited Staking</li>
                <li class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i>Advanced Analytics
                  Dashboard</li>
                <li class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i>24/7 Live Monitoring</li>
                <li class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i>Priority Support</li>
                <li class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i>Exclusive Beta Access</li>
              </ul>
            </div>
            <p class="mt-8 text-center bg-green-500 text-white font-semibold py-2 rounded-lg shadow-md animate-pulse">
              You're on Premium
            </p>
            </a>
          </div>

        </div>
      </div>
    </section>

    <!-- Section: My Assets Table -->
    <section id="my-assets" class="mt-12 bg-[#1a1f36] rounded-lg shadow-lg border border-purple-500 overflow-hidden">
      <div class="p-4 border-b border-purple-800">
        <p class="text-[#c084fc] font-semibold text-lg">Cryptocurrencies Market</p>
        <p class="text-sm text-slate-400">
          Overview of your cryptocurrency holdings and market prices.
        </p>
      </div>
      <div class="overflow-x-auto">
        <div class="h-96 overflow-y-auto">
          <table class="min-w-full text-sm text-white">
            <thead class="bg-[#1a1330] text-purple-300 uppercase sticky top-0 z-10">
              <tr>
                <th class="px-4 py-3 text-left">#</th>
                <th class="px-4 py-3 text-left">Asset</th>
                <th class="px-4 py-3">Price</th>
                <th class="px-4 py-3">1h %</th>
                <th class="px-4 py-3">24h %</th>
                <th class="px-4 py-3">7d %</th>
                <th class="px-4 py-3">Market Cap</th>
              </tr>
            </thead>
            <tbody id="crypto-table" class="text-center divide-y divide-purple-900">
              <!-- Dynamic rows go here -->
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- Floating Support Button + Chat -->
    <div class="fixed bottom-6 right-6 z-50 group">
      <button id="supportButton"
        class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-full shadow-lg hover:shadow-purple-500/50 transition duration-300 ease-in-out animate-bounce hover:scale-110">
        <i class="fas fa-headset text-white text-xl"></i>
      </button>

      <!-- Tooltip -->
      <div
        class="absolute bottom-16 right-0 bg-gray-800 text-white text-xs px-3 py-1 rounded-md opacity-0 group-hover:opacity-100 transition-opacity duration-300">
        Customer Support
      </div>
    </div>

    <!-- Chatbox -->
    <div id="chatBox"
      class="fixed bottom-24 right-6 w-80 bg-white border border-gray-300 rounded-lg shadow-lg p-4 hidden flex-col">
      <div class="flex justify-between items-center mb-2">
        <h3 class="text-lg font-semibold text-purple-700">Support Chat</h3>
        <button id="closeChat" class="text-gray-500 hover:text-red-500">&times;</button>
      </div>
      <div id="chatMessages" class="h-64 overflow-y-auto text-sm mb-2 space-y-2">
        <!-- AI messages appear here -->
      </div>
      <input type="text" placeholder="Type your message..."
        class="w-full px-3 py-2 border rounded-md focus:outline-none text-sm text-black" />
    </div>
  </main>

  <!-- Footer -->
  <footer class="py-12 px-6 border-t border-gray-800">
    <div class="max-w-7xl mx-auto">
      <div class="grid md:grid-cols-4 gap-8 mb-8">
        <div class="flex items-center space-x-2">
          <a href="index.php" class="flex items-center">
            <div
              class="w-10 h-10 rounded-full flex items-center justify-center pulse hover:scale-105 transition-transform duration-200">
              <img src="../assets/img/Elytra Logo.png" alt="Elytra Logo"
                class="w-full h-full rounded-full object-cover" />
            </div>
            <span class="text-xl font-bold text-white hover:text-blue-200 transition-colors duration-200">Elytra
              Pool</span>
          </a>
        </div>‹

        <div>
          <h4 class="font-semibold mb-4">Products</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="premium-staking.php" class="hover:text-white">Staking</a></li>
            <li><a href="premium-dashboard.php" class="hover:text-white">Assets</a></li>
            <li>
              <a href="premium-leaderboard.php" class="hover:text-white">Leaderboard</a>
            </li>
            <li>
              <a href="pages/about.php" class="hover:text-white">FAQ</a>
            </li>
          </ul>
        </div>

        <div>
          <h4 class="font-semibold mb-4">Support</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="#" class="hover:text-white">Help Center</a></li>
            <li><a href="#" class="hover:text-white">Contact Us</a></li>
            <li><a href="#" class="hover:text-white">Status</a></li>
            <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
          </ul>
        </div>

        <div>
          <h4 class="font-semibold mb-4">Follow Us</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="#" class="hover:text-white">Twitter</a></li>
            <li><a href="#" class="hover:text-white">Telegram</a></li>
            <li><a href="#" class="hover:text-white">Discord</a></li>
          </ul>
        </div>
      </div>

      <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center">
        <div class="text-sm text-gray-400 mb-4 md:mb-0">
          © 2023 - 2025 Elytra Pool. All rights reserved.
        </div>
        <div class="flex space-x-6">
          <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-telegram"></i></a>
          <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-discord"></i></a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scripts -->

  <script>
    if (!<?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>) {
      window.location.href = '../../index.php';
    }
  </script>

  <script>
    function showToast(message, type = 'success') {
      const toast = document.createElement('div');
      toast.className = `
      px-4 py-3 rounded-md shadow-md text-white text-sm transition-opacity duration-500 animate-slide-in-right
      ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}
    `;
      toast.textContent = message;

      document.getElementById('toastContainer').appendChild(toast);

      setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 500);
      }, 3000);
    }
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", async () => {
      try {
        // Load Wallet Balances
        const resWallet = await fetch("../../config/wallet_data.php");
        const dataWallet = await resWallet.json();

        if (dataWallet.status === 'success') {
          const formattedDate = new Date(dataWallet.last_activity).toLocaleString();
          document.getElementById("btcBalance").textContent = `${parseFloat(dataWallet.btc).toFixed(8)} BTC`;
          document.getElementById("ethBalance").textContent = `${parseFloat(dataWallet.eth).toFixed(8)} ETH`;
          document.getElementById("usdtBalance").textContent = `${parseFloat(dataWallet.usdt).toLocaleString()} USDT`;
          document.getElementById("elytrsBalance").textContent = `${parseFloat(dataWallet.eltr).toLocaleString()} ELTR`;
          document.getElementById("totalBalance").textContent = `$${parseFloat(dataWallet.total).toLocaleString()}`;
          document.getElementById("lastActivity").textContent = `Last Activity: ${formattedDate}`;
          document.getElementById("lastActivityMobile").textContent = `Last Activity: ${formattedDate}`;
        }

        // Load Transactions
        const resTx = await fetch("../../config/transaction_history.php");
        const dataTx = await resTx.json();
        const container = document.getElementById("transactionList");
        if (container) container.innerHTML = "";

        if (dataTx.status === 'success' && dataTx.data.length > 0) {
          dataTx.data.forEach(tx => {
            const colorMap = {
              deposit: "text-emerald-400",
              withdraw: "text-red-400",
              stake: "text-purple-300",
              convert: "text-blue-300",
              transfer: "text-yellow-300"
            };
            const color = colorMap[tx.type.toLowerCase()] || "text-white";
            const sign = tx.direction === 'in' ? "+" : "-";

            const div = document.createElement("div");
            div.className = "flex justify-between border-b border-purple-800 pb-2";
            div.innerHTML = `
            <span class="text-slate-300 capitalize">${tx.type}</span>
            <span class="${color} font-bold">${sign}${parseFloat(tx.amount).toLocaleString()} ${tx.currency}</span>
          `;
            if (container) container.appendChild(div);
          });
        } else {
          if (container) container.innerHTML = `<p class="text-slate-400 text-center">No transactions found.</p>`;
        }
      } catch (error) {
        console.error("Error loading data:", error);
        const container = document.getElementById("transactionList");
        if (container) container.innerHTML = `<p class="text-red-400 text-center">Error loading transactions.</p>`;
      }
    });
  </script>

  <script>
    function copyReferralLink() {
      const linkInput = document.getElementById("referralLink");
      linkInput.select();
      linkInput.setSelectionRange(0, 99999); // For mobile
      document.execCommand("copy");

      // Show Alertify alert with OK button
      alertify.alert("Copied", "Referral link copied to clipboard!");
    }
  </script>

  <!-- Navbar Toggle Script -->
  <script>
    const menuBtn = document.getElementById("menu-button");
    const mobileMenu = document.getElementById("mobile-menu");
    const profileBtn = document.getElementById("profileBtn");
    const profileMenu = document.getElementById("profileMenu");
    const mobileProfileBtn = document.getElementById("mobileProfileBtn");
    const mobileProfileMenu = document.getElementById("mobileProfileMenu");

    menuBtn.addEventListener("click", () => {
      mobileMenu.classList.toggle("active");
    });

    if (profileBtn) {
      profileBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        profileMenu.classList.toggle("hidden");
      });

      window.addEventListener("click", (e) => {
        if (!profileBtn.contains(e.target)) {
          profileMenu.classList.add("hidden");
        }
      });
    }

    if (mobileProfileBtn) {
      mobileProfileBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        mobileProfileMenu.classList.toggle("hidden");
      });

      window.addEventListener("click", (e) => {
        if (!mobileProfileBtn.contains(e.target)) {
          mobileProfileMenu.classList.add("hidden");
        }
      });
    }
  </script>
  <script>
    const ctx = document.getElementById('earningsChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
          label: 'Earnings (ELTR)',
          data: [120, 150, 180, 130, 200, 220, 250],
          fill: true,
          backgroundColor: 'rgba(192, 132, 252, 0.1)',
          borderColor: '#c084fc',
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            labels: {
              color: '#c084fc'
            }
          }
        },
        scales: {
          x: {
            ticks: {
              color: '#a78bfa'
            },
            grid: {
              color: '#2e2b4a'
            }
          },
          y: {
            ticks: {
              color: '#a78bfa'
            },
            grid: {
              color: '#2e2b4a'
            }
          }
        }
      }
    });
  </script>

  <script>
    const supportButton = document.getElementById("supportButton");
    const chatBox = document.getElementById("chatBox");
    const closeChat = document.getElementById("closeChat");
    const chatMessages = document.getElementById("chatMessages");
    const userInput = chatBox.querySelector('input[type="text"]');

    supportButton.addEventListener("click", () => {
      chatBox.classList.remove("hidden");
      showAIIntro();
      userInput.focus();
    });

    closeChat.addEventListener("click", () => {
      chatBox.classList.add("hidden");
      chatMessages.innerHTML = "";
      userInput.value = "";
    });

    function showAIIntro() {
      if (chatMessages.children.length === 0) {
        setTimeout(() => addBotMessage("👋 Hi there! I'm Elytra Support Bot."), 500);
        setTimeout(() => addBotMessage("How can I help you today?"), 1500);
        setTimeout(() => addBotMessage(`
        • 💬 How to start staking?<br>
        • 🔒 Wallet connection issue<br>
        • 📊 View staking rewards<br>
        • 📌 Report a bug
      `), 2500);
      }
    }

    function addBotMessage(message) {
      const msg = document.createElement("div");
      msg.className = "bg-gray-100 text-black p-2 rounded text-sm";
      msg.innerHTML = message;
      chatMessages.appendChild(msg);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function addUserMessage(message) {
      const msg = document.createElement("div");
      msg.className = "bg-purple-200 text-black p-2 rounded text-sm text-right";
      msg.textContent = message;
      chatMessages.appendChild(msg);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Handle Enter key to send message
    userInput.addEventListener("keypress", function(e) {
      if (e.key === "Enter" && userInput.value.trim() !== "") {
        const message = userInput.value.trim();
        addUserMessage(message);
        userInput.value = "";

        // Simulated bot reply
        setTimeout(() => {
          addBotMessage("🤖 Got it! One of our team will get back to you soon.");
        }, 1000);
      }
    });
  </script>

  <script src="../../assets/js/script.js"></script>
  <script src="../../assets/js/randomizer.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <!-- Alertify JS -->
  <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
</body>

</html>