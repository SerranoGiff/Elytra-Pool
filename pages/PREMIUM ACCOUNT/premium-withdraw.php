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
  <title>Elytra Pool | Withdraw & Transfer</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <!-- AlertifyJS CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
  <!-- Optional: Default theme -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
  <link rel="shortcut icon" href="../../assets/img/ELYTRA.jpg" type="image/x-icon" />
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="../../assets/css/user.css">
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

  <!-- Main Section -->
  <main class="max-w-5xl mx-auto pt-28 px-4 pb-16">
    <section class="grid grid-cols-1 md:grid-cols-2 gap-8">

      <!-- Withdraw Section -->
      <section class="bg-[#1a1f36] border border-purple-500 p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-purple-400 mb-6 text-center">Withdraw Funds</h2>

        <div class="mb-4">
          <label class="block text-sm mb-2 text-purple-300">Select Network</label>
          <select id="withdrawNetwork"
            class="w-full px-3 py-2 rounded bg-slate-900 text-white text-sm border border-purple-400" onchange="updateWallet()">
            <option value="">Choose Network</option>
            <option value="USDT">USDT</option>
            <option value="BTC">BTC</option>
            <option value="ETH">ETH</option>
          </select>
        </div>

        <div class="mb-4">
          <label for="recipientAddress" class="block text-sm mb-2 text-purple-300">Recipient Wallet Address</label>
          <input type="text" id="recipientAddress"
            class="w-full px-3 py-2 rounded bg-slate-900 text-white text-sm border border-purple-400"
            placeholder="Enter recipient address" />
        </div>

        <div class="mb-4">
          <label for="withdrawAmount" class="block text-sm mb-2 text-purple-300">Amount</label>
          <input type="number" id="withdrawAmount"
            class="w-full px-3 py-2 rounded bg-slate-900 text-white text-sm border border-purple-400"
            placeholder="0.00" />
        </div>

        <button onclick="submitWithdrawal()"
          class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded w-full font-semibold bg-gradient-to-r from-pink-500 to-red-500 hover:from-pink-400 hover:to-red-400 text-white text-sm transition">Confirm
          Withdrawal</button>
      </section>

      <!-- Transfer Section -->
      <section class="bg-[#1a1f36] border border-purple-500 p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-purple-400 mb-6 text-center">Transfer Elytra</h2>

        <div class="mb-4">
          <label for="transferUsername" class="block text-sm mb-2 text-purple-300">Recipient Username</label>
          <input type="text" id="transferUsername"
            class="w-full px-3 py-2 rounded bg-slate-900 text-white text-sm border border-purple-400"
            placeholder="Enter username" />
        </div>

        <div class="mb-4">
          <label for="transferAmount" class="block text-sm mb-2 text-purple-300">Amount (ELYTRA)</label>
          <input type="number" id="transferAmount"
            class="w-full px-3 py-2 rounded bg-slate-900 text-white text-sm border border-purple-400"
            placeholder="0.00 ELYTRA" />
        </div>

        <button onclick="submitTransfer()"
          class="bg-indigo-500 hover:bg-indigo-600 px-4 py-2 rounded w-full font-semibold bg-gradient-to-r from-pink-500 to-red-500 hover:from-pink-400 hover:to-red-400 text-white text-sm transition">
          Confirm Transfer
        </button>
      </section>

    </section>
  </main>

  <?php
  $userId = $_SESSION['user_id'] ?? null;

  $showKycModal = false;

  if ($userId) {
    $stmt = $conn->prepare("SELECT status FROM kyc_verifications WHERE user_id = ? ORDER BY submitted_at DESC LIMIT 1");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $kyc = $result->fetch_assoc();

    if (!$kyc || $kyc['status'] !== 'approved') {
      $showKycModal = true;
    }
  }
  ?>

  <?php if ($showKycModal): ?>
    <!-- KYC Verification Modal (No Close Button) -->
    <div id="kycModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50">
      <div
        class="bg-[#1a1f36] border border-purple-500 rounded-lg shadow-2xl w-full max-w-md p-6 text-white relative animate-fade-in">
        <div class="text-center">
          <h2 class="text-2xl font-bold text-purple-400 mb-3">Verify Your Identity</h2>
          <p class="text-sm text-slate-400 mb-5">
            To continue using Elytra Pool’s features, please complete your KYC verification.
            This ensures the security of your funds and unlocks withdrawals and staking.
          </p>
          <a href="kyc_verification.php"
            class="inline-block bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 text-white font-semibold py-2 px-6 rounded shadow-md shadow-purple-700/30 transition">
            Start Verification
          </a>
        </div>
      </div>
    </div>
  <?php endif; ?>

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
          <a href="index.php" class="flex items-center">
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
            <li><a href="#staking" class="hover:text-white">Staking</a></li>
            <li><a href="#mining" class="hover:text-white">Assets</a></li>
            <li>
              <a href="#earnings" class="hover:text-white">Leaderboard</a>
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
    let isSubmitting = false;

    function validateWalletAddress(address) {
      // Must be alphanumeric and at least 20 characters
      return /^[a-zA-Z0-9]{20,}$/.test(address);
    }

    function submitWithdrawal() {
      if (isSubmitting) return;

      const network = document.getElementById("withdrawNetwork").value.trim();
      const address = document.getElementById("recipientAddress").value.trim();
      const amount = parseFloat(document.getElementById("withdrawAmount").value);
      const button = document.querySelector("button[onclick='submitWithdrawal()']");

      // Network validation
      if (!["USDT", "BTC", "ETH"].includes(network)) {
        alertify.error("Please select a valid network (USDT, BTC, ETH).");
        return;
      }

      // Wallet address validation
      if (!validateWalletAddress(address)) {
        alertify.error("Invalid wallet address. Must be alphanumeric and at least 20 characters.");
        return;
      }

      // Amount validation
      if (isNaN(amount) || amount <= 0) {
        alertify.error("Enter a valid withdrawal amount.");
        return;
      }

      if (amount < 1000) {
        alertify.error("Minimum withdrawal is ₱1000 USDT. Applies to BTC and ETH as well.");
        return;
      }

      // Lock submission
      isSubmitting = true;
      button.disabled = true;
      button.innerText = "Processing...";

      // Prepare form data
      const formData = new FormData();
      formData.append("network", network);
      formData.append("recipient", address);
      formData.append("amount", amount);
      formData.append("action", "withdraw");

      // Submit to backend
      fetch("../../config/submit_withdrawal.php", {
          method: "POST",
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          console.log(data);
          if (data.status === "success") {
            alertify.success("Withdrawal submitted. Wait for admin approval within 24 hours.");

            // Reset fields
            document.getElementById("withdrawNetwork").value = "";
            document.getElementById("recipientAddress").value = "";
            document.getElementById("withdrawAmount").value = "";
          } else {
            alertify.error(data.message || "An error occurred. Please try again.");
          }
        })
        .catch(() => {
          alertify.error("Server error. Please try again later.");
        })
        .finally(() => {
          isSubmitting = false;
          button.disabled = false;
          button.innerText = "Confirm Withdrawal";
        });
    }
  </script>

  <script>
    function submitTransfer() {
      const username = document.getElementById("transferUsername").value.trim();
      const amount = parseFloat(document.getElementById("transferAmount").value);

      if (!username || amount <= 0) {
        alertify.error("Please enter valid recipient and amount.");
        return;
      }

      fetch("../../config/transfer_elytra.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded"
          },
          body: `username=${encodeURIComponent(username)}&amount=${amount}`
        })
        .then(res => res.json())
        .then(data => {
          if (data.status === "success") {
            alertify.success(data.message);
            document.getElementById("transferUsername").value = "";
            document.getElementById("transferAmount").value = "";
          } else {
            alertify.error(data.message);
          }
        })
        .catch(() => {
          alertify.error("Something went wrong.");
        });
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
    // Show only if user is NOT verified
    window.addEventListener('DOMContentLoaded', () => {
      const isVerified = false; // 🔁 Change this based on your verification logic
      if (isVerified) {
        document.getElementById('kycModal').classList.add('hidden');
      } else {
        document.getElementById('kycModal').classList.remove('hidden');
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
  <!-- AlertifyJS Script -->
  <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
</body>

</html>