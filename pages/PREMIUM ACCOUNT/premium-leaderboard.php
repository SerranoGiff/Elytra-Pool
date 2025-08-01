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
  <title>Elytra Pool | Asset Wallet</title>
  <link rel="stylesheet" href="../../assets/css/style.css" />
  <link rel="stylesheet" href="../../assets/css/user.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="shortcut icon" href="../../assets/img/ELYTRA.jpg" type="image/x-icon" />

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
  
  <!-- Premium Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 max-w-5xl mx-auto mt-24 px-4">
    <!-- Card Template -->
    <div
      class="relative bg-gradient-to-tr from-[#1e1b36] to-[#2e1065] border border-purple-600 p-6 rounded-xl shadow-lg hover:shadow-purple-500/40 hover:scale-105 transition-transform duration-300 overflow-hidden">
      <div class="absolute inset-0 bg-purple-400 opacity-10 blur-xl rounded-xl animate-pulse"></div>
      <div class="relative z-10 text-purple-300">
        <div class="text-sm text-gray-400 mb-1">Yesterday's Profit</div>
        <div class="text-3xl font-bold">+1,000 <span class="text-purple-400">ELTR</span></div>
        <div class="text-xs text-gray-500 mt-1">Platform-wide earnings</div>
      </div>
    </div>

    <div
      class="relative bg-gradient-to-tr from-[#1e1b36] to-[#2e1065] border border-purple-600 p-6 rounded-xl shadow-lg hover:shadow-purple-500/40 hover:scale-105 transition-transform duration-300 overflow-hidden">
      <div class="absolute inset-0 bg-purple-400 opacity-10 blur-xl rounded-xl animate-pulse"></div>
      <div class="relative z-10 text-purple-300">
        <div class="text-sm text-gray-400 mb-1">30 Days Cumulative Income</div>
        <div class="text-3xl font-bold">+18,420 <span class="text-purple-400">ELTR</span></div>
        <div class="text-xs text-gray-500 mt-1">Platform-wide earnings</div>
      </div>
    </div>

    <div
      class="relative bg-gradient-to-tr from-[#1e1b36] to-[#2e1065] border border-purple-600 p-6 rounded-xl shadow-lg hover:shadow-purple-500/40 hover:scale-105 transition-transform duration-300 overflow-hidden">
      <div class="absolute inset-0 bg-purple-400 opacity-10 blur-xl rounded-xl animate-pulse"></div>
      <div class="relative z-10 text-purple-300">
        <div class="text-sm text-gray-400 mb-1">Active Users</div>
        <div class="text-3xl font-bold"><span id="activeUsers">--</span></div>
        <div class="text-xs text-gray-500 mt-1">In the past hour</div>
      </div>
    </div>
  </div>

  <!-- Premium Leaderboard Section -->
  <section
    class="bg-gradient-to-br from-[#1e1b36] to-[#0f172a] px-4 sm:px-6 py-8 rounded-xl w-full max-w-5xl mx-auto shadow-2xl border border-purple-700 mt-10">
    <h2
      class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-fuchsia-500 to-indigo-400 text-center tracking-tight animate-pulse mb-2">
      🏆 Leaderboards
    </h2>
    <p class="text-sm text-slate-400 text-center mb-6">Real-time platform data for top contributors</p>

    <!-- Glowing Tabs -->
    <div class="flex flex-wrap justify-center gap-2 mb-6">
      <button
        class="leaderboard-tab active-tab bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-md hover:scale-105 transition"
        data-tab="stakers">Top Stakers ✨</button>
      <button
        class="leaderboard-tab bg-[#2e2e4d] text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-purple-700 transition"
        data-tab="holders">Top Holders</button>
      <button
        class="leaderboard-tab bg-[#2e2e4d] text-white px-4 py-2 rounded-full text-sm font-semibold hover:bg-purple-700 transition"
        data-tab="earners">Top Earners</button>
    </div>

    <!-- Tables -->
    <div class="w-full overflow-x-auto rounded-lg">
      <!-- Top Stakers -->
      <table id="tab-stakers"
        class="leaderboard-table min-w-full divide-y divide-[#334155] text-sm text-white bg-[#1e1b36]">
        <thead class="bg-[#0f172a] text-[#c084fc]">
          <tr>
            <th class="py-3 px-4 text-left">Rank</th>
            <th class="py-3 px-4 text-left">User</th>
            <th class="py-3 px-4 text-left">Total Staked</th>
          </tr>
        </thead>
        <tbody id="tbody-stakers" class="divide-y divide-[#2e1065]"></tbody>
      </table>

      <!-- Top Holders -->
      <table id="tab-holders"
        class="leaderboard-table hidden min-w-full divide-y divide-[#334155] text-sm text-white bg-[#1e1b36]">
        <thead class="bg-[#0f172a] text-[#c084fc]">
          <tr>
            <th class="py-3 px-4 text-left">Rank</th>
            <th class="py-3 px-4 text-left">User</th>
            <th class="py-3 px-4 text-left">Balance</th>
          </tr>
        </thead>
        <tbody id="tbody-holders" class="divide-y divide-[#2e1065]"></tbody>
      </table>

      <!-- Top Earners -->
      <table id="tab-earners"
        class="leaderboard-table hidden min-w-full divide-y divide-[#334155] text-sm text-white bg-[#1e1b36]">
        <thead class="bg-[#0f172a] text-[#c084fc]">
          <tr>
            <th class="py-3 px-4 text-left">Rank</th>
            <th class="py-3 px-4 text-left">User</th>
            <th class="py-3 px-4 text-left">Rewards</th>
          </tr>
        </thead>
        <tbody id="tbody-earners" class="divide-y divide-[#2e1065]"></tbody>
      </table>
    </div>

    <!-- Your Stats -->
    <div
      class="mt-10 bg-gradient-to-tr from-[#0f172a] to-[#1e1b36] border border-purple-700 rounded-xl p-6 sm:p-8 text-center shadow-xl">
      <h3 class="text-xl font-semibold text-[#a78bfa] mb-4 animate-bounce">Your Stats 🌟</h3>
      <div class="grid sm:grid-cols-3 gap-4 text-sm sm:text-base">
        <p>👑 Rank: <span id="yourRank" class="font-bold text-white animate-pulse">--</span></p>
        <p>💰 Total Staked: <span id="yourEarnings" class="font-bold text-[#c084fc]">--</span></p>
        <p>📦 ELTR Balance: <span id="yourBalance" class="font-bold text-[#c084fc]">--</span></p>
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


  <!-- Navbar Toggle + Leaderboard Script -->
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

    // Generate 100 unique usernames
    const usernames = Array.from({ length: 100 }, (_, i) => {
      const adjectives = ["Swift", "Fuzzy", "Dark", "Neon", "Silent", "Crypto", "Shadow", "Golden", "Nova", "Iron"];
      const animals = ["Tiger", "Wolf", "Bear", "Eagle", "Shark", "Hawk", "Falcon", "Fox", "Lion", "Panther"];
      return (
        adjectives[i % adjectives.length] +
        animals[i % animals.length] +
        Math.floor(Math.random() * 9999)
      );
    });

    const generateUsers = () => {
      return usernames.map(name => ({
        username: name,
        staked: Math.floor(Math.random() * 100000) + 1000,
        balance: Math.floor(Math.random() * 100000) + 2000,
        rewards: Math.floor(Math.random() * 20000) + 1000,
        prevRank: {
          staked: null,
          balance: null,
          rewards: null,
        }
      }));
    };

    let allUsers = JSON.parse(localStorage.getItem("elytra_users")) || generateUsers();
    const loggedInUser = "GoldenFox3829"; // Replace with dynamic user session

    function getRankChangeIcon(prev, current) {
      if (prev === null) return "🟢";
      if (current < prev) return "🔺";
      if (current > prev) return "🔻";
      return "🟢";
    }

    const fadeUpdate = (id, newVal) => {
      const el = document.getElementById(id);
      if (!el) return;
      el.classList.add("transition", "duration-500", "opacity-0");
      setTimeout(() => {
        el.innerText = newVal;
        el.classList.remove("opacity-0");
      }, 200);
    };

    const renderTable = (type, key, tbodyId) => {
      const tbody = document.getElementById(tbodyId);
      const sorted = [...allUsers].sort((a, b) => b[key] - a[key]).slice(0, 10);
      tbody.innerHTML = "";

      sorted.forEach((user, i) => {
        const rank = i + 1;
        const isLogged = user.username === loggedInUser;
        const indicator = getRankChangeIcon(user.prevRank[type], rank);
        const medal = rank === 1 ? "🥇" : rank === 2 ? "🥈" : rank === 3 ? "🥉" : rank;
        const value = user[key].toLocaleString();

        const row = document.createElement("tr");
        row.className = isLogged ? "bg-purple-800 bg-opacity-20" : "";
        row.innerHTML = `
        <td class="py-3 px-4 font-semibold text-[#c084fc]">
          <span class="inline-block animate-pulse">${medal}</span>
          <span class="ml-1">${indicator}</span>
        </td>
        <td class="py-3 px-4 text-white">${user.username}</td>
        <td class="py-3 px-4 font-medium text-purple-200">${value} ELTR</td>
      `;
        tbody.appendChild(row);

        user.prevRank[type] = rank;

        if (isLogged && type === "staked") {
          fadeUpdate("yourRank", `#${rank}`);
          fadeUpdate("yourEarnings", `${user.staked.toLocaleString()} ELTR`);
          fadeUpdate("yourBalance", `${user.balance.toLocaleString()} ELTR`);
        }
      });
    };

    const updateLeaderboard = () => {
      allUsers.forEach(user => {
        user.staked += Math.floor(Math.random() * 5000);
        user.balance += Math.floor(Math.random() * 2000);
        user.rewards += Math.floor(Math.random() * 800);
      });

      renderTable("staked", "staked", "tbody-stakers");
      renderTable("balance", "balance", "tbody-holders");
      renderTable("rewards", "rewards", "tbody-earners");

      localStorage.setItem("elytra_users", JSON.stringify(allUsers));
    };

    // Tab logic
    const tabs = document.querySelectorAll(".leaderboard-tab");
    const tables = {
      stakers: document.getElementById("tab-stakers"),
      holders: document.getElementById("tab-holders"),
      earners: document.getElementById("tab-earners")
    };

    tabs.forEach(tab => {
      tab.addEventListener("click", () => {
        tabs.forEach(t => {
          t.classList.remove("active-tab", "bg-gradient-to-r", "from-purple-600", "to-indigo-600");
          t.classList.add("bg-[#2e2e4d]");
        });
        tab.classList.add("active-tab", "bg-gradient-to-r", "from-purple-600", "to-indigo-600");
        Object.values(tables).forEach(tbl => tbl.classList.add("hidden"));
        tables[tab.dataset.tab].classList.remove("hidden");
      });
    });

    updateLeaderboard();
    setInterval(updateLeaderboard, 3600000); // every hour
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
    userInput.addEventListener("keypress", function (e) {
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

</body>

</html>