<?php
session_start();
include '../../config/dbcon.php';

// PREMIUM EXPIRATION ENFORCEMENT
if (isset($_SESSION['user_id']) && isset($_SESSION['type']) && $_SESSION['type'] === 'premium') {
    $userId = (int) $_SESSION['user_id'];
    // Fetch current expiration
    $stmtExp = $conn->prepare("SELECT premium_expiration FROM users WHERE id = ?");
    $stmtExp->bind_param("i", $userId);
    $stmtExp->execute();
    $expResult = $stmtExp->get_result()->fetch_assoc();
    $stmtExp->close();

    $now = new DateTime('now', new DateTimeZone('Asia/Manila'));
    $exp  = !empty($expResult['premium_expiration'])
         ? DateTime::createFromFormat('Y-m-d H:i:s', $expResult['premium_expiration'], new DateTimeZone('Asia/Manila'))
         : null;

    if (is_null($exp) || $exp < $now) {
        // expired → downgrade
        $downgrade = $conn->prepare("UPDATE users SET type = 'free' WHERE id = ?");
        $downgrade->bind_param("i", $userId);
        $downgrade->execute();
        $downgrade->close();

        $_SESSION['type'] = 'free';
        unset($_SESSION['expires']);
        header("Location: ../../index.php?error=Subscription expired.");
        exit;
    }
}

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

$userId = $_SESSION['user_id'];

// Fetch user data
$query = "SELECT first_name, last_name, birthday, username, about_me, email, wallet_address, profile_photo
          FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Set defaults
$firstName     = $user['first_name']    ?? 'N/A';
$lastName      = $user['last_name']     ?? 'N/A';
$birthday      = $user['birthday']      ?? '';
$username      = $user['username']      ?? 'N/A';
$aboutMe       = $user['about_me']      ?? 'N/A';
$email         = $user['email']         ?? 'N/A';
$walletAddress = $user['wallet_address']?? '';
$profileImg    = !empty($user['profile_photo'])
                 ? "../../" . $user['profile_photo']
                 : '../../assets/default-avatar.png';
$profileImg   .= '?v=' . time();

// Generate referral code
$referralSuffix = sprintf('%04d', $userId % 10000);
$referralCode   = $username . $referralSuffix;
$referralLink   = "https://elytra.io/referral/" . $referralCode;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Elytra Pool | Staking</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
  <link rel="shortcut icon" href="../../assets/img/ELYTRA.jpg" type="image/x-icon" />
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="../../assets/css/user.css">
  <style>
    .shimmer {
      position: relative;
      overflow: hidden;
    }

    .shimmer::after {
      content: "";
      position: absolute;
      top: 0;
      left: -150%;
      width: 100%;
      height: 100%;
      background: linear-gradient(120deg,
          rgba(255, 255, 255, 0) 0%,
          rgba(255, 255, 255, 0.1) 50%,
          rgba(255, 255, 255, 0) 100%);
      animation: shimmerMove 2.5s infinite;
    }

    @keyframes shimmerMove {
      0% {
        left: -150%;
      }

      100% {
        left: 150%;
      }
    }

    .floating-premium-badge {
      position: fixed;
      top: 100px;
      right: 20px;
      z-index: 50;
      background: linear-gradient(to right, #facc15, #fcd34d);
      color: #1e1b36;
      padding: 0.5rem 1rem;
      font-weight: bold;
      border-radius: 9999px;
      box-shadow: 0 0 15px rgba(250, 204, 21, 0.5);
      animation: bounceIn 1s ease;
    }

    @keyframes bounceIn {
      0% {
        transform: translateY(-30%);
        opacity: 0;
      }

      100% {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .exclusive-tag {
      background: #facc15;
      color: #1a1f36;
      font-size: 0.75rem;
      padding: 0.15rem 0.5rem;
      border-radius: 9999px;
      font-weight: 700;
      margin-left: 0.5rem;
      animation: pulse 2s infinite;
    }

    @keyframes pulse {

      0%,
      100% {
        box-shadow: 0 0 0 0 rgba(250, 204, 21, 0.5);
      }

      50% {
        box-shadow: 0 0 10px 6px rgba(250, 204, 21, 0.3);
      }
    }

    @keyframes slideIn {
      0% {
        opacity: 0;
        transform: translateX(100%);
      }

      100% {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes fadeOut {
      0% {
        opacity: 1;
        transform: translateX(0);
      }

      100% {
        opacity: 0;
        transform: translateX(100%);
      }
    }

    .slide-in {
      animation: slideIn 0.4s ease-out forwards;
    }

    .fade-out {
      animation: fadeOut 0.5s ease-in forwards;
    }
  </style>


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
        <a href="premium-dashboard.php" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Home</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="premium-staking.php" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
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
          class="absolute right-0 mt-2 w-40  bg-white rounded-lg shadow-lg text-sm text-black hidden z-50">
          <a href="settings.php" class="block px-4 py-2 hover:bg-gray-100">Settings</a>
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
            <a href="settings.php" class="block px-4 py-2 hover:bg-gray-100">Settings</a>
            <a href="../../config/logout.php" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Navigation Links -->
    <div id="mobile-menu" class="md:hidden hidden text-white text-center animate-fade-in backdrop-blur-xl bg-white/10 rounded-b-xl p-4 space-y-2 shadow-xl border-t border-white/10">
      <a href="premium-dashboard.php" class="block px-6 py-3 rounded-md transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:text-purple-300 relative group">
        Home
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
      <a href="premium-staking.php" class="block px-6 py-3 rounded-md transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:text-purple-300 relative group">
        Staking
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
      <a href="premium-leaderboard.php" class="block px-6 py-3 rounded-md transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:text-purple-300 relative group">
        Leaderboard
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
      <a href="premium-deposit.php" class="block px-6 py-3 rounded-md transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:text-purple-300 relative group">
        Deposit
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
      <a href="premium-withdraw.php" class="block px-6 py-3 rounded-md transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:text-purple-300 relative group">
        Withdraw
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
      <a href="premium-convert.php" class="block px-6 py-3 rounded-md transition-all duration-300 hover:bg-white/20 hover:scale-105 hover:text-purple-300 relative group">
        Convert
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
    </div>
  </nav>

  <!-- Staking Section -->
  <section id="staking" class="pt-32 pb-20 px-4 md:px-10">
    <div class="max-w-[90rem] mx-auto">
      <div class="text-center mb-4">
        <h2
          class="flex flex-col sm:flex-row justify-center items-center
           gap-y-2 sm:gap-x-3
           text-4xl font-bold text-purple-400">
          <i class="fas fa-crown text-yellow-400 animate-pulse"></i>
          Earn Rewards On Staking
        </h2>
      </div>
      <p class="text-gray-400 mb-12 text-center max-w-3xl mx-auto text-lg">
        Premium users enjoy higher Hashrate, weekly payouts, and exclusive access.
      </p>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <!-- Yesterday's Profit -->
        <div class="bg-gray-800 border border-purple-500 p-6 rounded-xl shadow-lg text-purple-300 w-full neon-glow">
          <div class="text-sm text-gray-400 mb-1">Yesterday's Profit</div>
          <div class="text-3xl font-bold" id="yesterday-profit">0.00 <span class="text-purple-400">ELTR</span></div>
          <div class="text-xs text-gray-500 mt-1">Platform-wide earnings</div>
        </div>

        <!-- 30 Days Cumulative Income -->
        <div class="bg-gray-800 border border-purple-500 p-6 rounded-xl shadow-lg text-purple-300 w-full neon-glow">
          <div class="text-sm text-gray-400 mb-1">30 Days Cumulative Income</div>
          <div class="text-3xl font-bold" id="thirty-day-profit">0.00 <span class="text-purple-400">ELTR</span></div>
          <div class="text-xs text-gray-500 mt-1">Platform-wide earnings</div>
        </div>

        <!-- Premium Profit Tracker -->
        <div class="bg-gray-800 border border-yellow-400 p-6 rounded-xl shadow-lg shimmer w-full text-yellow-300 col-span-1 md:col-span-2">
          <div class="text-sm text-gray-400 mb-1">Premium Profit Tracker</div>
          <div class="text-3xl font-bold">
            <span id="premium-profit">0.00</span> <span class="text-yellow-400">ELTR</span>
          </div>
          <div class="text-xs text-gray-500 mt-1">Lifetime exclusive earnings</div>
        </div>

      </div>

      <!-- Tabs -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10 w-full">
        <button class="tab-btn w-full py-3 rounded-lg bg-gradient-to-r from-purple-600 via-indigo-600 to-purple-700 text-white font-semibold hover:brightness-110 transition">Staking Cycles</button>
        <button class="tab-btn w-full py-3 rounded-lg bg-gray-700 text-white font-semibold hover:bg-purple-700 transition">Active Stakes</button>
        <button class="tab-btn w-full py-3 rounded-lg bg-gray-700 text-white font-semibold hover:bg-purple-700 transition">Staking Archive</button>
      </div>

      <!-- Sections -->
      <div id="stakingCyclesSection">
        <!-- This will just toggle card visibility -->
      </div>

      <div class="hidden" id="activeStakesSection">
        <div class="bg-gray-800 p-6 rounded-lg text-white text-center shadow-lg">
          <p class="text-lg">You have no active stakes yet.</p>
        </div>
      </div>

      <!-- Currency Filter Dropdown (Initially Hidden) -->
      <div id="currencyFilterWrapper" class="hidden mb-4">
        <label for="currencyFilter" class="block text-sm font-medium text-white mb-1">Filter by Currency</label>
        <select id="currencyFilter" class="bg-gray-700 text-white p-2 rounded-lg w-full max-w-xs">
          <option value="">All Currencies</option>
        </select>
      </div>

      <!-- Staking History Section -->
      <div class="hidden" id="stakingHistorySection">
        <div class="bg-gray-800 p-6 rounded-lg text-white text-center shadow-lg">
          <p class="text-lg">No staking history found.</p>
        </div>
      </div>

      <!-- STAKING CARDS -->
      <div id="stakingCards" class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Elytra Staking -->
        <div class="relative staking-card p-6 rounded-2xl cursor-pointer fade-in bg-[#1a1f36] border border-purple-500 shadow-lg hover:shadow-purple-500/30 transition duration-300" data-default="true">
          <!-- 🏆 Hanging Badge -->
          <div class="absolute -top-3 -right-3 z-10">
            <div class="bg-gradient-to-r from-purple-600 via-indigo-500 to-blue-200 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg animate-pulse ring-2 ring-purple-600">
              🔥 Exclusive
            </div>
          </div>

          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center">
              <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                <i class="fab fa-book text-white"></i>
              </div>
              <div>
                <div class="font-semibold text-white">Elytra</div>
                <div class="text-sm text-gray-400">ELTR Staking</div>
              </div>
            </div>
            <div id="btc-apy" class="bg-blue-500/10 text-blue-400 px-3 py-1 rounded-full text-sm">5 TH/s Hashrate</div>
          </div>

          <div class="mb-6">
            <div class="flex justify-between text-sm mb-1 text-white">
              <span class="text-gray-400">Total Staked</span>
              <span id="btc-staked">120,450 ELTR</span>
            </div>
            <div class="w-full bg-gray-800 rounded-full h-2">
              <div class="progress-bar bg-blue-400 h-2 rounded-full" style="width: 25%"></div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 mb-6 text-white">
            <div>
              <div class="text-sm text-gray-400">Min. Stake</div>
              <div id="btc-min" class="font-semibold">200 ELTR</div>
            </div>
            <div>
              <div class="text-sm text-gray-400">Lock Period</div>
              <div id="btc-lock" class="font-semibold">3-90 days</div>
            </div>
          </div>

          <a href="#"
            class="stake-btn w-full py-2 rounded-lg text-center block text-white font-semibold bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:bg-purple-800 transition">
            Stake Now
          </a>
        </div>
        <!-- Bitcoin -->
        <div id="card-btc" class="staking-card p-6 rounded-2xl cursor-pointer fade-in relative" data-default="true">
          <div class="absolute -top-3 -right-3 z-10">
            <div class="bg-gradient-to-r from-purple-600 via-indigo-500 to-blue-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg animate-pulse ring-2 ring-purple-400">🏆 Best Option</div>
          </div>
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center">
              <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center mr-3">
                <i class="fab fa-btc text-white"></i>
              </div>
              <div>
                <div class="font-semibold">Bitcoin</div>
                <div class="text-sm text-gray-400">BTC Staking</div>
              </div>
            </div>
            <div class="bg-blue-500/10 text-blue-400 px-3 py-1 rounded-full text-sm">100 GH/s Hashrate</div>
          </div>
          <div class="mb-6">
            <div class="flex justify-between text-sm mb-1"><span class="text-gray-400">Total Staked</span><span>12,450 BTC</span></div>
            <div class="w-full bg-gray-800 rounded-full h-2">
              <div class="progress-bar bg-blue-400 h-2 rounded-full" style="width: 65%"></div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
              <div class="text-sm text-gray-400">Min. Stake</div>
              <div class="font-semibold">200 ELTR</div>
            </div>
            <div>
              <div class="text-sm text-gray-400">Lock Period</div>
              <div class="font-semibold">3-90 days</div>
            </div>
          </div>
          <a href="#" class="stake-btn btn-primary w-full py-2 rounded-lg text-center block bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:bg-blue-700" data-title="Bitcoin" data-min="0.1 ETH" data-lock="14 Days" data-currency="BTC">Stake Now</a>
        </div>

        <!-- Ethereum Staking -->
        <div class="staking-card p-6 rounded-2xl cursor-pointer fade-in" style="animation-delay: 0.2s" data-default="true">
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center">
              <div class="w-10 h-10 bg-gray-400 rounded-full flex items-center justify-center mr-3">
                <i class="fab fa-ethereum text-white"></i>
              </div>
              <div>
                <div class="font-semibold">Ethereum</div>
                <div class="text-sm text-gray-400">ETH Staking</div>
              </div>
            </div>
            <div id="eth-apy" class="bg-blue-500/10 text-blue-400 px-3 py-1 rounded-full text-sm">87 GH/s Hashrate</div>
          </div>
          <div class="mb-6">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-400">Total Staked</span>
              <span id="eth-staked">245,000 ETH</span>
            </div>
            <div class="w-full bg-gray-800 rounded-full h-2">
              <div class="progress-bar" style="width: 78%"></div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
              <div class="text-sm text-gray-400">Min. Stake</div>
              <div id="eth-min" class="font-semibold">200 ELTR</div>
            </div>
            <div>
              <div class="text-sm text-gray-400">Lock Period</div>
              <div id="eth-lock" class="font-semibold">3-90 days</div>
            </div>
          </div>
          <a href="#"
            class="stake-btn btn-primary w-full py-2 rounded-lg text-center block bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:bg-blue-700"
            data-title="Ethereum" data-min="0.1 ETH" data-lock="14 Days" data-currency="ETH">Stake Now</a>
        </div>

        <!-- Solana Staking -->
        <div class="staking-card p-6 rounded-2xl cursor-pointer fade-in" style="animation-delay: 0.3s" data-default="true">
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center">
              <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-bolt text-white"></i>
              </div>
              <div>
                <div class="font-semibold">Solana</div>
                <div class="text-sm text-gray-400">SOL Staking</div>
              </div>
            </div>
            <div id="sol-apy" class="bg-blue-500/10 text-blue-400 px-3 py-1 rounded-full text-sm">78 GH/s Hashrate</div>
          </div>
          <div class="mb-6">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-400">Total Staked</span>
              <span>4,560,000 SOL</span>
            </div>
            <div class="w-full bg-gray-800 rounded-full h-2">
              <div class="progress-bar" style="width: 45%"></div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
              <div class="text-sm text-gray-400">Min. Stake</div>
              <div id="sol-min" class="font-semibold">200 ELTR</div>
            </div>
            <div>
              <div class="text-sm text-gray-400">Lock Period</div>
              <div id="sol-lock" class="font-semibold">3-90 days</div>
            </div>
          </div>
          <a href="#"
            class="stake-btn btn-primary w-full py-2 rounded-lg text-center block bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:bg-blue-700"
            data-title="Solana" data-min="1 SOL" data-lock="7 Days" data-currency="SOL">Stake Now</a>

        </div>

        <!-- Cardano -->
        <div class="staking-card p-8 rounded-2xl fade-in shadow-lg bg-gray-900 hover:scale-[1.02] transition-transform" data-default="true">
          <div class="flex justify-between items-start mb-6">
            <div class="flex items-center">
              <div
                class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-4 text-white text-xl font-bold">
                ₳</div>
              <div>
                <div class="text-lg font-semibold text-white">Cardano</div>
                <div class="text-sm text-gray-400">ADA Staking</div>
              </div>
            </div>
            <div class="bg-blue-500/10 text-blue-400 px-4 py-1 rounded-full text-sm">66 GH/s Hashrate</div>
          </div>
          <div class="mb-6">
            <div class="flex justify-between text-sm mb-1 text-white">
              <span class="text-gray-400">Total Staked</span>
              <span>100,000 ADA</span>
            </div>
            <div class="w-full bg-gray-800 rounded-full h-2">
              <div class="progress-bar bg-blue-500 h-2 rounded-full" style="width: 50%"></div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-6 text-white">
            <div>
              <div class="text-sm text-gray-400">Min. Stake</div>
              <div class="font-semibold">200 ELTR</div>
            </div>
            <div>
              <div class="text-sm text-gray-400">Lock Period</div>
              <div class="font-semibold">3-90 days</div>
            </div>
          </div>
          <a href="#"
            class="stake-btn w-full py-2 rounded-lg text-center block text-white bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:brightness-110"
            data-title="Cardano" data-min="10 ADA" data-lock="14 Days" data-currency="ADA">Stake Now</a>

        </div>

        <!-- Polkadot -->
        <div class="staking-card p-8 rounded-2xl fade-in shadow-lg bg-gray-900 hover:scale-[1.02] transition-transform" data-default="true">
          <div class="flex justify-between items-start mb-6">
            <div class="flex items-center">
              <div
                class="w-12 h-12 bg-pink-500 rounded-full flex items-center justify-center mr-4 text-white text-xl font-bold">
                ●</div>
              <div>
                <div class="text-lg font-semibold text-white">Polkadot</div>
                <div class="text-sm text-gray-400">DOT Staking</div>
              </div>
            </div>
            <div class="bg-blue-500/10 text-blue-400 px-4 py-1 rounded-full text-sm">92 GH/s Hashrate</div>
          </div>
          <div class="mb-6">
            <div class="flex justify-between text-sm mb-1 text-white">
              <span class="text-gray-400">Total Staked</span>
              <span>500,000 DOT</span>
            </div>
            <div class="w-full bg-gray-800 rounded-full h-2">
              <div class="progress-bar bg-pink-500 h-2 rounded-full" style="width: 60%"></div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-6 text-white">
            <div>
              <div class="text-sm text-gray-400">Min. Stake</div>
              <div class="font-semibold">200 ELTR</div>
            </div>
            <div>
              <div class="text-sm text-gray-400">Lock Period</div>
              <div class="font-semibold">3-90 days</div>
            </div>
          </div>
          <a href="#"
            class="stake-btn w-full py-2 rounded-lg text-center block text-white bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:brightness-110"
            data-title="Polkadot" data-min="5 DOT" data-lock="28 Days" data-currency="DOT">Stake Now</a>

        </div>
      </div>
    </div>

    <!-- STAKING MODAL -->
    <div id="stakingModal"
      class="fixed inset-0 bg-black bg-opacity-60 modal-overlay flex items-center justify-center z-50 hidden">
      <div class="bg-gray-900 rounded-2xl w-full max-w-md p-6 relative text-white shadow-lg fade-in-up">
        <button id="closeModal"
          class="absolute top-3 right-3 text-gray-400 hover:text-white text-2xl leading-none">&times;</button>

        <h2 id="modalTitle" class="text-3xl font-bold mb-4 text-purple-400">Stake Now</h2>
        <p id="modalDescription" class="mb-4 text-sm text-gray-300">
          Select a period and stake your crypto securely.
        </p>

        <div class="mb-4">
          <label class="block text-sm text-gray-400 mb-1">Select Lock Period</label>
          <select id="lockPeriodSelect"
            class="w-full p-3 rounded-lg bg-gray-800 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-600">
            <option value="3">3 Days</option>
            <option value="7">7 Days</option>
            <option value="15">15 Days</option>
            <option value="30">30 Days</option>
            <option value="60">60 Days</option>
            <option value="90">90 Days</option>
          </select>
        </div>

        <div class="mb-4">
          <label class="block text-sm text-gray-400 mb-1">Allowed Elytra Range</label>
          <div id="modalElytra" class="font-semibold text-white">Loading...</div>
        </div>

        <div class="mb-4">
          <label class="block text-sm text-gray-400 mb-1">Amount in USDT (approx)</label>
          <div id="modalUSDT" class="font-semibold text-white">Loading...</div>
        </div>

        <div class="mb-4">
          <label class="block text-sm text-gray-400 mb-1">DAE Range</label>
          <div id="modalAPY" class="font-semibold text-white">Loading...</div>
        </div>

        <div class="mb-6">
          <label for="stakeAmount" class="block text-sm text-gray-400 mb-1">Amount to Stake</label>
          <input id="stakeAmount" type="number"
            class="w-full p-3 rounded-lg bg-gray-800 text-white placeholder-gray-500 border border-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-600"
            placeholder="Enter amount">
        </div>

        <button type="button"
          class="staking-modal-btn bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 w-full py-3 rounded-lg font-semibold hover:brightness-110 transition-all flex items-center justify-center gap-2">
          <i class="fas fa-lock text-sm"></i> Confirm Stake
        </button>
      </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 hidden">
      <div class="bg-gray-900 text-white p-6 rounded-2xl max-w-sm w-full shadow-2xl relative border border-red-500">
        <div class="mb-4">
          <h2 class="text-2xl font-bold text-red-400 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            Cancel Stake
          </h2>
          <p class="text-sm text-gray-300 mt-2">
            Are you sure you want to cancel your stake of:
          </p>
          <p class="text-lg font-bold mt-3">
            <span id="cancelAmount"></span> <span id="cancelCurrency"></span>
          </p>

          <div class="bg-yellow-900 text-yellow-300 text-sm p-3 rounded-lg mt-4 border border-yellow-600 flex gap-2 items-start">
            <i class="fas fa-info-circle text-yellow-400 mt-0.5"></i>
            <span>
              <strong>Note:</strong> Cancelling a stake early will result in a
              <span class="font-bold text-yellow-200">penalty</span> and loss of potential rewards.
            </span>
          </div>
        </div>

        <div class="flex justify-end gap-2 mt-6">
          <button onclick="document.getElementById('cancelModal').classList.add('hidden')"
            class="px-4 py-2 rounded-lg bg-gray-600 hover:bg-gray-700 transition">
            <i class="fas fa-times-circle mr-1"></i> Close
          </button>
          <button id="confirmCancelBtn"
            class="px-4 py-2 rounded-lg bg-gradient-to-r from-red-500 via-red-600 to-red-700 hover:brightness-110 transition font-medium">
            <i class="fas fa-ban mr-1"></i> Yes, Cancel Anyway
          </button>
        </div>
      </div>
    </div>

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
  </section>

  <!-- Footer -->
  <footer class="py-12 px-6 border-t border-gray-800">
    <div class="max-w-7xl mx-auto">
      <div class="grid md:grid-cols-4 gap-8 mb-8">
        <div class="flex items-center space-x-2">
          <a href="user.php" class="flex items-center">
            <div
              class="w-10 h-10 rounded-full flex items-center justify-center pulse hover:scale-105 transition-transform duration-200">
              <img src="../../assets/img/Elytra Logo.png" alt="Elytra Logo"
                class="w-full h-full rounded-full object-cover" />
            </div>
            <span class="text-xl font-bold text-white hover:text-blue-200 transition-colors duration-200">Elytra
              Pool</span>
          </a>
        </div>

        <div>
          <h4 class="font-semibold mb-4">Products</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="premium-staking.php" class="hover:text-white">Staking</a></li>
            <li>
              <a href="premium-leaderboard.php" class="hover:text-white">Leaderboard</a>
            </li>
            <li>
              <a href="faq.php" class="hover:text-white">FAQ</a>
            </li>
          </ul>
        </div>

        <div>
          <h4 class="font-semibold mb-4">Support</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="../../help center.html" class="hover:text-white">Help Center</a></li>
            <li><a href="#" class="hover:text-white">Contact Us</a></li>
            <li><a href="../../terms and condition.html" class="hover:text-white">Terms & Conditions</a></li>
            <li><a href="../../privacy policy.html" class="hover:text-white">Privacy Policy</a></li>
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
    document.addEventListener("DOMContentLoaded", function() {
      const stakingModal = document.getElementById("stakingModal");
      const modalTitle = document.getElementById("modalTitle");
      const modalDescription = document.getElementById("modalDescription");
      const stakeButtons = document.querySelectorAll(".stake-btn");
      const confirmBtn = document.querySelector(".staking-modal-btn");
      const stakeInput = document.getElementById("stakeAmount");
      const lockPeriodSelect = document.getElementById("lockPeriodSelect");
      const modalElytra = document.getElementById("modalElytra");
      const modalUSDT = document.getElementById("modalUSDT");
      const modalAPY = document.getElementById("modalAPY");
      const closeModal = document.getElementById("closeModal");

      if (!stakingModal || !confirmBtn || !stakeInput) return;

      alertify.set("notifier", "position", "top-right");

      let selectedDisplayCurrency = "Elytra";

      const stakingConfig = {
        periods: {
          3: {
            min: 200,
            max: 2999.99,
            dailyRange: [4.2, 4.5]
          },
          7: {
            min: 3000,
            max: 6999.99,
            dailyRange: [4.5, 4.8]
          },
          15: {
            min: 7000,
            max: 19999.99,
            dailyRange: [7.0, 8.0]
          },
          30: {
            min: 20000,
            max: 39999.99,
            dailyRange: [8.5, 9.3]
          },
          60: {
            min: 40000,
            max: 99999.99,
            dailyRange: [10.0, 14.0]
          },
          90: {
            min: 120000,
            max: Infinity,
            dailyRange: [16.0, 25.0]
          },
        },
        elytraToUSDT: (ely) => ely / 2,
        updateModalInfo() {
          const period = parseInt(lockPeriodSelect.value);
          const config = this.periods[period];
          if (!config) return;
          const {
            min,
            max,
            dailyRange
          } = config;
          modalElytra.textContent = `${min} ELTR – ${max === Infinity ? "∞" : max} ELTR`;
          modalUSDT.textContent = `${this.elytraToUSDT(min).toFixed(2)} USDT – ${
          max === Infinity ? "∞" : this.elytraToUSDT(max).toFixed(2)
        } USDT`;
          modalAPY.textContent = `${dailyRange[0]}% – ${dailyRange[1]}%`;
        },
      };

      function initModal() {
        stakeButtons.forEach((btn) => {
          btn.addEventListener("click", (e) => {
            e.preventDefault();
            selectedDisplayCurrency = btn.getAttribute("data-currency") || "Elytra";

            modalTitle.textContent = `Stake ${selectedDisplayCurrency}`;
            modalDescription.textContent = `Stake your ${selectedDisplayCurrency} securely and earn passive income.`;
            stakingModal.classList.remove("hidden");

            stakingConfig.updateModalInfo();
          });
        });

        lockPeriodSelect.addEventListener("change", () => stakingConfig.updateModalInfo());
        closeModal.addEventListener("click", () => stakingModal.classList.add("hidden"));

        window.addEventListener("click", (e) => {
          if (e.target === stakingModal) stakingModal.classList.add("hidden");
        });
      }

      async function handleStakeConfirmation() {
        try {
          const lockDays = parseInt(lockPeriodSelect.value);
          const amount = parseFloat(stakeInput.value);
          const config = stakingConfig.periods[lockDays];

          if (!config || isNaN(amount) || amount < config.min || (config.max !== Infinity && amount > config.max)) {
            alertify.error(`Enter amount between ${config.min} and ${config.max === Infinity ? "∞" : config.max} ELTR`);
            return;
          }

          confirmBtn.disabled = true;
          confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
          const processingAlert = alertify.message("Processing your stake...", 0);

          // Run expired stake updater silently
          await fetch("../../config/check_stake_limits.php");

          const checkResponse = await fetch("../../config/check_stake_limits.php");
          const checkData = await checkResponse.json();

          if (checkData.status !== "ok") {
            alertify.dismissAll();
            alertify.error(checkData.message || "Unable to verify stake limits.");
            return;
          }

          const response = await fetch("../../config/stake.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
              amount,
              currency: selectedDisplayCurrency === "Elytra" ? "ELTR" : selectedDisplayCurrency,
              lock_days: lockDays,
              daily_percent: config.dailyRange[0],
            }),
          });

          const rawText = await response.text();
          let data;

          try {
            data = JSON.parse(rawText);
          } catch {
            throw new Error("Invalid response: " + rawText);
          }

          if (!response.ok || data.status !== "success") {
            throw new Error(data.message || "Staking failed");
          }

          alertify.dismissAll();
          alertify.success(`Success! ${amount} ${selectedDisplayCurrency} staked for ${lockDays} days.`);
          stakingModal.classList.add("hidden");
          stakeInput.value = "";
        } catch (error) {
          console.error(error);
          alertify.dismissAll();
          alertify.error(error.message || "Error during staking.");
        } finally {
          confirmBtn.disabled = false;
          confirmBtn.innerHTML = '<i class="fas fa-lock text-sm"></i> Confirm Stake';
        }
      }

      initModal();
      confirmBtn.addEventListener("click", handleStakeConfirmation);

      fetch("../../config/fetch_earnings_summary.php")
        .then((res) => res.json())
        .then((data) => {
          document.getElementById("yesterday-profit").innerHTML = `${data.yesterday_earnings.toFixed(2)} <span class="text-purple-400">ELTR</span>`;
          document.getElementById("thirty-day-profit").innerHTML = `${data.thirty_day_earnings.toFixed(2)} <span class="text-purple-400">ELTR</span>`;
          document.getElementById("premium-profit").textContent = data.lifetime_earnings.toFixed(2);
        })
        .catch((err) => {
          console.error("Error fetching earnings summary:", err);
        });
    });
  </script>

  <script>
    const hashIntervals = {}; // Track update intervals per card

    // Helper to format GH/s into GH/s or TH/s
    function formatHashrate(gh) {
      if (gh >= 1000) {
        return (gh / 1000).toFixed(2) + ' TH/s';
      } else {
        return gh.toFixed(2) + ' GH/s';
      }
    }

    function renderActiveStakes(data) {
      const container = document.getElementById('stakingCards');

      // Hide default cards
      document.querySelectorAll('.staking-card[data-default="true"]').forEach(card => {
        card.classList.add('hidden');
      });

      // Remove any previously‐rendered dynamic cards
      document.querySelectorAll('.staking-card[data-default="false"]').forEach(card => card.remove());

      data.forEach(stake => {
        const cardId = `stake-${stake.id}`;

        // Compute daily earnings label
        const dailyEarnings = (stake.amount * (stake.daily_percent / 100)).toFixed(6);

        // Progress calculation
        const now = new Date();
        const start = new Date(stake.created_at);
        const end = new Date(stake.end_date);
        const totalDuration = end - start;
        const elapsed = now - start;
        let progress = Math.min(100, Math.floor((elapsed / totalDuration) * 100));
        if (progress < 0) progress = 0;

        // Pick a random initial hashrate between 66 and 10,000 GH/s
        const initialGH = Math.random() * (10000 - 66) + 66;
        const initialHashrate = formatHashrate(initialGH);

        // Choose icon & color
        const iconData = {
          'BTC': {
            icon: '<i class="fab fa-btc text-white"></i>',
            color: 'bg-orange-500'
          },
          'ETH': {
            icon: '<i class="fab fa-ethereum text-white"></i>',
            color: 'bg-gray-400'
          },
          'ELTR': {
            icon: '<i class="fas fa-rocket text-white"></i>',
            color: 'bg-purple-500'
          },
          'USDT': {
            icon: '<i class="fas fa-dollar-sign text-white"></i>',
            color: 'bg-green-500'
          },
          'SOL': {
            icon: '<i class="fas fa-bolt text-white"></i>',
            color: 'bg-purple-500'
          },
          'ADA': {
            icon: '₳',
            color: 'bg-blue-500'
          },
          'DOT': {
            icon: '●',
            color: 'bg-pink-500'
          }
        };
        const sel = iconData[stake.currency] || {
          icon: '<i class="fas fa-coins text-white"></i>',
          color: 'bg-gray-500'
        };
        const currencyLabel = stake.currency === 'ELTR' ? 'Elytra' : stake.currency;

        // Build card
        const card = document.createElement('div');
        card.className = 'staking-card p-4 bg-gray-800 rounded-lg shadow text-white';
        card.dataset.default = "false";
        card.id = cardId;
        card.innerHTML = `
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 ${sel.color} rounded-full flex items-center justify-center">
            ${sel.icon}
          </div>
          <div class="text-lg font-semibold">${currencyLabel} Stake</div>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-4 text-sm">
          <div>
            <div class="text-xs text-gray-400">Hashrate</div>
            <div id="${cardId}-hashrate" class="text-base font-semibold">${initialHashrate}</div>
          </div>
          <div>
            <div class="text-xs text-gray-400">Daily Earnings</div>
            <div class="text-base font-semibold">${dailyEarnings} ELTR</div>
          </div>
        </div>
        <div class="mb-2 text-xs text-gray-400">
          Staked Amount: <span class="text-white font-semibold">${stake.amount} ELTR</span>
        </div>
        <div class="flex justify-between text-xs text-white mb-1">
          <span>Progress</span><span>${progress}%</span>
        </div>
        <div class="w-full bg-gray-700 rounded-full h-1.5 mb-4">
          <div class="bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 h-1.5 rounded-full" style="width: ${progress}%"></div>
        </div>
        <a href="#"
           onclick="openCancelModal(${stake.id}, ${stake.amount}, '${stake.currency}')"
           class="w-full py-2 rounded-lg text-center block text-white bg-gradient-to-r from-red-500 via-red-600 to-red-700 hover:brightness-110 text-sm font-medium">
          Cancel Stake
        </a>
      `;
        container.appendChild(card);

        // Schedule random hashrate updates every 1–5 minutes
        const updateHashrate = () => {
          const gh = Math.random() * (10000 - 66) + 66;
          const label = formatHashrate(gh);
          const el = document.getElementById(`${cardId}-hashrate`);
          if (el) el.textContent = label;
          const next = (Math.floor(Math.random() * 5) + 1) * 60000;
          hashIntervals[cardId] = setTimeout(updateHashrate, next);
        };
        const firstDelay = (Math.floor(Math.random() * 5) + 1) * 60000;
        hashIntervals[cardId] = setTimeout(updateHashrate, firstDelay);
      });
    }

    function openCancelModal(id, amount, currency) {
      const modal = document.getElementById("cancelModal");
      const modalAmount = document.getElementById("cancelAmount");
      const modalCurrency = document.getElementById("cancelCurrency");
      const confirmBtn = document.getElementById("confirmCancelBtn");

      modalAmount.textContent = amount;
      modalCurrency.textContent = currency;

      confirmBtn.onclick = () => {
        fetch(`../../config/cancel_stake.php`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              stake_id: id
            })
          })
          .then(res => res.json())
          .then(resp => {
            if (resp.success) {
              alertify.success(resp.message || 'Stake successfully canceled.');
              location.reload();
            } else {
              alertify.error(resp.message || 'Failed to cancel stake.');
            }
          })
          .catch(err => {
            console.error('Cancel error:', err);
            alertify.error('Something went wrong.');
          });
        modal.classList.add("hidden");
      };

      modal.classList.remove("hidden");
    }
  </script>

  <script>
    const tabs = document.querySelectorAll('.tab-btn');
    const stakingCycles = document.getElementById('stakingCyclesSection');
    const activeStakes = document.getElementById('activeStakesSection');
    const stakingHistory = document.getElementById('stakingHistorySection');
    const stakingCards = document.getElementById('stakingCards');
    const currencyFilterWrapper = document.getElementById('currencyFilterWrapper');
    const currencyFilter = document.getElementById('currencyFilter');

    tabs.forEach((btn, index) => {
      btn.addEventListener('click', () => {
        // Reset tab styles
        tabs.forEach(tab => {
          tab.classList.remove('bg-gradient-to-r', 'from-purple-600', 'via-indigo-600', 'to-purple-700', 'bg-purple-700');
          tab.classList.add('bg-gray-700');
        });

        btn.classList.remove('bg-gray-700');
        btn.classList.add('bg-gradient-to-r', 'from-purple-600', 'via-indigo-600', 'to-purple-700');

        // Hide all sections by default
        stakingCycles.classList.add('hidden');
        activeStakes.classList.add('hidden');
        stakingHistory.classList.add('hidden');
        stakingCards.classList.add('hidden');
        currencyFilterWrapper.classList.add('hidden');

        if (index === 0) {
          // Staking Cycles
          stakingCycles.classList.remove('hidden');
          stakingCards.classList.remove('hidden');

          document.querySelectorAll('.staking-card').forEach(card => {
            if (card.dataset.default === "true") {
              card.classList.remove('hidden');
            } else {
              card.classList.add('hidden');
            }
          });

        } else if (index === 1) {
          // Active Stakes
          fetch('../../config/fetch_active_stakes.php')
            .then(res => res.json())
            .then(data => {
              document.querySelectorAll('.staking-card').forEach(card => {
                if (card.dataset.default === "true") card.classList.add('hidden');
              });

              if (data.length > 0) {
                stakingCards.classList.remove('hidden');
                activeStakes.classList.add('hidden');
                renderActiveStakes(data);
              } else {
                stakingCards.classList.add('hidden');
                activeStakes.classList.remove('hidden');
              }
            })
            .catch(err => {
              console.error('Failed to load active stakes:', err);
              stakingCards.classList.add('hidden');
              activeStakes.classList.remove('hidden');
            });

        } else if (index === 2) {
          // Staking Archive
          stakingHistory.classList.remove('hidden');
          stakingCards.classList.add('hidden');
          activeStakes.classList.add('hidden');
          stakingCycles.classList.add('hidden');
          currencyFilterWrapper.classList.remove('hidden');

          stakingHistory.innerHTML = `
          <div class="bg-gray-800 p-6 rounded-lg text-white text-center shadow-lg">
            <p class="text-lg">Loading staking history...</p>
          </div>`;

          fetch('../../config/fetch_staking_archive.php')
            .then(res => res.json())
            .then(data => {
              stakingHistory.innerHTML = '';

              if (!Array.isArray(data) || data.length === 0) {
                stakingHistory.innerHTML = `
                <div class="bg-gray-800 p-6 rounded-lg text-white text-center shadow-lg">
                  <p class="text-lg">No staking history found.</p>
                </div>`;
                return;
              }

              // Get unique currencies
              const uniqueCurrencies = [...new Set(data.map(s => s.currency ?? 'N/A'))];
              currencyFilter.innerHTML = '<option value="">All Currencies</option>';
              uniqueCurrencies.forEach(curr => {
                const opt = document.createElement('option');
                opt.value = curr;
                opt.textContent = curr;
                currencyFilter.appendChild(opt);
              });

              // Render each stake card
              data.forEach(stake => {
                const currency = stake.currency ?? 'N/A';
                const amount = parseFloat(stake.amount ?? 0);
                const dailyPercent = parseFloat(stake.daily_percent ?? 0);
                const createdAt = new Date(stake.created_at);
                const endedAt = new Date(stake.end_date);

                const isValidDates = !isNaN(createdAt) && !isNaN(endedAt);
                const duration = isValidDates ?
                  Math.ceil((endedAt - createdAt) / (1000 * 60 * 60 * 24)) :
                  'N/A';
                const dailyEarnings = (amount * (dailyPercent / 100)).toFixed(6);

                const card = document.createElement('div');
                card.className = 'staking-card p-4 bg-gray-800 rounded-lg shadow text-white mb-4 fade-in';
                card.dataset.default = "false";
                card.dataset.currency = currency;
                card.innerHTML = `
                <div class="flex justify-between items-start mb-3">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-600 rounded-full flex items-center justify-center">
                      <i class="fas fa-history text-white"></i>
                    </div>
                    <div>
                      <div class="text-lg font-semibold flex items-center gap-2">
                        ${currency} 
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium ${
                          stake.status === 'canceled' 
                          ? 'bg-red-600 text-white' 
                          : 'bg-green-600 text-white'
                        }">${stake.status === 'canceled' ? 'Canceled' : 'Completed'}</span>
                      </div>
                      <div class="text-sm text-gray-400">${createdAt.toLocaleDateString()} → ${endedAt.toLocaleDateString()}</div>
                    </div>
                  </div>
                  <button class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1 rounded-md delete-btn">
                    <i class="fas fa-trash-alt mr-1"></i> Delete
                  </button>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                  <div>
                    <div class="text-xs text-gray-400">Stake Duration</div>
                    <div class="font-semibold">${duration} days</div>
                  </div>
                  <div class="text-right">
                    <div class="text-xs text-gray-400">Daily %</div>
                    <div class="font-semibold">${dailyPercent}%</div>
                  </div>
                </div>

                <div class="text-sm text-gray-400 mb-1">Staked Amount: <span class="text-white font-semibold">${amount} ELTR</span></div>
                <div class="text-sm text-gray-400 mb-4">
                Total Est. Earnings: 
                <span class="${stake.status === 'canceled' ? 'text-red-400' : 'text-green-400'} font-semibold">
                  ${(dailyEarnings * duration).toFixed(2)} ELTR
                </span>
              </div>
              `;
                stakingHistory.appendChild(card);
              });
            })
            .catch(err => {
              console.error("Archive fetch failed:", err);
              stakingHistory.innerHTML = `
              <div class="bg-red-600 p-4 text-white rounded-lg shadow">
                Error loading staking history.
              </div>`;
            });
        }
      });
    });

    // Currency Filter Listener
    currencyFilter.addEventListener('change', function() {
      const selected = this.value;
      const cards = stakingHistory.querySelectorAll('.staking-card');

      cards.forEach(card => {
        const currency = card.dataset.currency;
        if (selected === '' || currency === selected) {
          card.classList.remove('hidden');
        } else {
          card.classList.add('hidden');
        }
      });
    });

    function showAllCards() {
      document.querySelectorAll('.staking-card[data-default="true"]').forEach(card => {
        card.classList.remove('hidden');
      });
    }
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
  <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
</body>

</html>