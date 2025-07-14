<?php
session_start();
include '../config/dbcon.php';

// NO CACHE HEADERS
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// VALIDATE SESSION
if (!isset($_SESSION['user_id']) || $_SESSION['type'] !== 'free') {
  header("Location: ../index.php?error=Unauthorized access.");
  exit;
}

$userId = $_SESSION['user_id'] ?? null;

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

$query = "SELECT first_name, last_name, birthday, username, about_me, email, wallet_address, profile_photo 
          FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Set default values with fallback to 'N/A'
$firstName = $user['first_name'] ?? 'N/A';
$lastName = $user['last_name'] ?? 'N/A';
$birthday = $user['birthday'] ?? '';
$username = $user['username'] ?? 'N/A';
$aboutMe = $user['about_me'] ?? 'N/A';
$email = $user['email'] ?? 'N/A';
$walletAddress = $user['wallet_address'] ?? '';
$profileImg = !empty($user['profile_photo']) ? "../" . $user['profile_photo'] : '../assets/default-avatar.png';
$profileImg .= '?v=' . time(); // Cache buster to avoid browser caching old image
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Elytra Pool | Deposit</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <!-- AlertifyJS CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
  <!-- Optional: Default theme -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />

  <link rel="shortcut icon" href="../assets/img/ELYTRA.jpg" type="image/x-icon" />
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/user.css">
</head>

<body class="min-h-screen">
  <!-- Navigation -->
  <nav
    class="fixed top-0 left-0 w-full z-50 backdrop-blur-lg bg-white/10 border-b border-white/10 shadow-md transition-all duration-500 ease-in-out">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">
      <!-- Logo -->
      <div class="flex items-center space-x-2">
        <a href="user.php" class="flex items-center">
          <div
            class="w-10 h-10 rounded-full flex items-center justify-center pulse hover:scale-105 transition-transform duration-200">
            <img src="../assets/img/Elytra Logo.png" alt="Elytra Logo"
              class="w-full h-full rounded-full object-cover" />
          </div>
          <span class="text-xl font-bold text-white hover:text-blue-200 transition-colors duration-200">Elytra
            Pool</span>
        </a>
      </div>

      <!-- Desktop Nav Links -->
      <div class="hidden md:flex nav-links space-x-6 items-center" id="nav-links">
        <a href="user.php" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Home</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="staking.html" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Staking</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="leaderboard.html"
          class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Leaderboard</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="deposit.php" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Deposit</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="withdraw.html"
          class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Withdraw</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="Convert.html" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
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
          class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg text-sm text-black hidden z-50">
          <a href="settings.php" class="block px-4 py-2 hover:bg-gray-100">Settings</a>
          <a href="../config/logout.php" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
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
            <a href="../config/logout.php" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Navigation Links -->
    <div id="mobile-menu" class="nav-links">
      <a href="user.php" class="nav-link">Home</a>
      <a href="staking.html" class="nav-link">Staking</a>
      <a href="leaderboard.html" class="nav-link">Leaderboard</a>
      <a href="deposit.php" class="nav-link">Deposit</a>
      <a href="withdraw.html" class="nav-link">Withdraw</a>
      <a href="Convert.html" class="nav-link">Convert</a>
    </div>
  </nav>

  <!-- Main Section -->
  <main class="flex items-center justify-center px-4 pt-28 pb-20">
    <section class="bg-[#1a1f36] border border-purple-500 p-6 pb-10 rounded-lg w-full max-w-md shadow-lg">
      <h2 class="text-2xl font-semibold text-purple-400 mb-6 text-center">Deposit Funds</h2>

      <!-- Wallet Selector -->
      <section class="mb-4">
        <label for="walletSelect" class="block text-sm mb-2 text-purple-300">Select Wallet</label>
        <select id="walletSelect" onchange="updateWallet()"
          class="w-full px-3 py-2 rounded bg-slate-900 text-white text-sm border border-purple-400">
          <option value="">(Choose Wallet)</option>
          <option value="Gate">Gate</option>
          <option value="Binance">Binance</option>
          <option value="Bybit">Bybit</option>
          <option value="Bitget">Bitget</option>
          <option value="OKX">OKX</option>
          <option value="OTHERS">OTHERS</option>
        </select>
      </section>

      <!-- Network Selector -->
      <section class="mb-4">
        <label for="networkSelect" class="block text-sm mb-2 text-purple-300">Select Network</label>
        <select id="networkSelect" onchange="updateWallet()"
          class="w-full px-3 py-2 rounded bg-slate-900 text-white text-sm border border-purple-400">
          <option value="">(Choose Network)</option>
          <option value="USDT">USDT</option>
          <option value="BTC">BTC</option>
          <option value="ETH">ETH</option>
        </select>
      </section>

      <!-- Amount -->
      <section class="mb-4">
        <label for="depositAmount" class="block text-sm mb-2 text-purple-300">Amount</label>
        <input type="number" id="depositAmount" placeholder="0.00"
          class="w-full px-3 py-2 rounded bg-slate-900 text-white text-sm border border-purple-400" />
      </section>

      <!-- Wallet Address & QR -->
      <section class="mb-4">
        <label class="block text-sm mb-2 text-purple-300">Wallet Address / QR</label>
        <div class="bg-[#0D1B2A] border border-purple-400 p-2 rounded flex justify-between items-center text-sm text-white">
          <span id="walletAddress" class="truncate">---</span>
          <div class="flex gap-2">
            <button onclick="copyToClipboard()" class="text-yellow-400 hover:text-yellow-300 text-xs">📋</button>
            <button onclick="showQR()" class="text-blue-400 hover:text-blue-300 text-xs">🔳</button>
          </div>
        </div>
      </section>

      <!-- Upload Proof -->
      <section class="mb-4">
        <label for="receiptUpload" class="block text-sm mb-2 text-purple-300">Upload Receipt</label>
        <input type="file" id="receiptUpload" accept="image/*"
          class="w-full px-2 py-1 rounded bg-slate-900 text-white text-sm border border-purple-400" />
      </section>

      <!-- Agreement -->
      <section class="mb-4">
        <label class="flex items-center gap-2 text-sm text-purple-300">
          <input type="checkbox" id="agree" class="accent-yellow-400" />
          I agree that my wallet address is correct and verified.
        </label>
      </section>

      <!-- Confirm Button -->
      <section>
        <button onclick="confirmDeposit()"
          class="bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 px-4 py-2 rounded w-full font-semibold text-white text-sm">
          Confirm Deposit
        </button>
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

  <!-- QR Code Popup -->
  <section>
    <div id="qrModal" class="fixed inset-0 hidden items-center justify-center z-50">
      <div class="overlay absolute inset-0" onclick="closeQR()"></div>
      <div class="bg-[#1e293b] p-4 rounded-lg z-10 border border-purple-500">
        <img id="qrImage" src="" alt="QR Code" class="w-64 h-64 mx-auto" />
        <button onclick="closeQR()" class="mt-4 w-full text-sm text-purple-400 hover:underline">Close</button>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-12 px-6 border-t border-gray-800">
    <div class="max-w-7xl mx-auto">
      <div class="grid md:grid-cols-4 gap-8 mb-8">
        <div class="flex items-center space-x-2">
          <a href="index.html" class="flex items-center">
            <div
              class="w-10 h-10 rounded-full flex items-center justify-center pulse hover:scale-105 transition-transform duration-200">
              <img src="../assets/img/Elytra Logo.png" alt="Elytra Logo"
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
              <a href="pages/about.html" class="hover:text-white">FAQ</a>
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
    function copyToClipboard() {
      const address = document.getElementById("walletAddress").textContent;
      navigator.clipboard.writeText(address)
        .then(() => alertify.success("Wallet address copied!"))
        .catch(() => alertify.error("Failed to copy address."));
    }

    function confirmDeposit() {
      const amount = document.getElementById("depositAmount").value;
      const agreed = document.getElementById("agree").checked;
      const receipt = document.getElementById("receiptUpload").files[0];

      if (!amount || amount <= 0) {
        alertify.warning("Please enter a valid amount.");
        return;
      }

      if (!receipt) {
        alertify.warning("Please upload your receipt.");
        return;
      }

      if (!agreed) {
        alertify.warning("You must agree before proceeding.");
        return;
      }

      alertify.success("Your balance will update in 10–20 minutes.");
      alertify.message("📧 Email sent! Contact support if not reflected.");
    }

    function generateQRPopup() {
      const address = document.getElementById("walletAddress").textContent;
      const qrImage = document.getElementById("qrImage");
      const qrWalletText = document.getElementById("qrWalletText");

      if (!address || address === "---") {
        alertify.error("Invalid wallet address.");
        return;
      }

      qrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(address)}`;
      qrWalletText.textContent = address;

      document.getElementById("qrPopup").classList.remove("hidden");
      document.getElementById("qrPopup").classList.add("flex");
    }

    function closeQRPopup() {
      document.getElementById("qrPopup").classList.add("hidden");
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

  <!--wallet addreses-->
  <script>
    const addresses = {
      "Bitget": {
        "USDT": {
          address: "TDfNhM7zLR9MeQSGCNBCvW2NdBZPnhMcXy",
          qr: "../assets/qr_codes/Bitget_USDT.png"
        },
        "BTC": {
          address: "bc1pdse4xuk2r8f0eg8ycfksupw6syq5gw8e4y28p29gdxyma57v5rnqh87jd5",
          qr: "../assets/qr_codes/Bitget_BTC.png"
        },
        "ETH": {
          address: "0x13383459DF26E6ff19EcD27F748e29C302CD8e26",
          qr: "../assets/qr_codes/Bitget_ETH.png"
        }
      },
      "OKX": {
        "USDT": {
          address: "TLZTcjWXdP57hx4NCZ1SdMuNvPE61gvLUo",
          qr: "../assets/img/OKX_USDT.jpg"
        },
        "BTC": {
          address: "bc1q83f3shcnn9ufxrm4jjxtpajk0sh06uummkaww68uxd3279sulpvq4u6q34",
          qr: "../assets/qr_codes/OKX_BTC.png"
        },
        "ETH": {
          address: "0x96b90389212dc3ef3bbf550d9d98be9feecd11dc",
          qr: "../assets/qr_codes/OKX_ETH.png"
        }
      },
      "Bybit": {
        "USDT": {
          address: "TDPVkMkZsbxYhtH4yUrfV3nbDtfb6GPYAH",
          qr: "../assets/qr_codes/Bybit_USDT.png"
        },
        "BTC": {
          address: "12kFVEtVxgt4GDjoz5uLY4xQyYPsNt1GsL",
          qr: "../assets/qr_codes/Bybit_BTC.png"
        },
        "ETH": {
          address: "0x1fae81ab9ff51645e525d4000556250dc778444f",
          qr: "../assets/qr_codes/Bybit_ETH.png"
        }
      },
      "Gate": {
        "USDT": {
          address: "TKpZDhHDAajPjfmfrQreyr3Sp5JysPZffE",
          qr: "../assets/qr_codes/Gate_USDT.png"
        },
        "BTC": {
          address: "1AWYeZ1QReaS5bu3T9gm4wAJP2i7iHtbaV",
          qr: "../assets/qr_codes/Gate_BTC.png"
        },
        "ETH": {
          address: "0x6c0e6426279F5b793B75B5cd592FAb93eA64638c",
          qr: "../assets/qr_codes/Gate_ETH.png"
        }
      },
      "Binance": {
        "USDT": {
          address: "TLFXrnUuDzHFnhf3Uk4snkVQBVgpzczg2L",
          qr: "../assets/qr_codes/Binance_USDT.png"
        },
        "BTC": {
          address: "12Pkd8jvQipC1kKAzWcUeEaFUWoBRMsTPR",
          qr: "../assets/qr_codes/Binance_BTC.png"
        },
        "ETH": {
          address: "0x85473395be671cef8a767af53bef0d89af5ec83c",
          qr: "../assets/qr_codes/Binance_ETH.png"
        }
      }
    };

    let currentQR = "";

    function updateWallet() {
      const wallet = document.getElementById("walletSelect").value;
      const network = document.getElementById("networkSelect").value;
      const addressSpan = document.getElementById("walletAddress");

      if (!wallet || !network) {
        addressSpan.textContent = "---";
        currentQR = "";
        alertify.error("Please select a wallet and network.");
        return;
      }

      if (addresses[wallet] && addresses[wallet][network]) {
        addressSpan.textContent = addresses[wallet][network].address;
        currentQR = addresses[wallet][network].qr;
      } else {
        addressSpan.textContent = "---";
        currentQR = "";
        alertify.error("Wallet/network combination not supported.");
      }
    }

    function showQR() {
      if (!currentQR) {
        alert("Select wallet & network first!");
        return;
      }

      document.getElementById("qrImage").src = currentQR;
      document.getElementById("qrModal").classList.remove("hidden");
      document.getElementById("qrModal").classList.add("flex");
    }

    function closeQR() {
      document.getElementById("qrModal").classList.add("hidden");
      document.getElementById("qrModal").classList.remove("flex");
    }

    function confirmDeposit() {
  const wallet = document.getElementById("walletSelect").value;
  const network = document.getElementById("networkSelect").value;
  const address = document.getElementById("walletAddress").textContent;
  const amount = parseFloat(document.getElementById("depositAmount").value);
  const agreed = document.getElementById("agree").checked;
  const receipt = document.getElementById("receiptUpload").files[0];

  if (!wallet || !network || address === "---") {
    alertify.error("Please select a wallet and network.");
    return;
  }

  if (isNaN(amount) || amount <= 0) {
    alertify.error("Please enter a valid deposit amount.");
    return;
  }

  if (!receipt) {
    alertify.error("Please upload your deposit receipt.");
    return;
  }

  if (!agreed) {
    alertify.warning("You must agree to the wallet terms.");
    return;
  }

  const formData = new FormData();
  formData.append("wallet", wallet);
  formData.append("network", network);
  formData.append("address", address);
  formData.append("amount", amount);
  formData.append("receipt", receipt);
  formData.append("agreed", agreed ? 1 : 0);

  fetch("../config/submit_deposit.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        // Reset form
        document.getElementById("depositAmount").value = '';
        document.getElementById("receiptUpload").value = '';
        document.getElementById("walletAddress").textContent = '---';
        document.getElementById("walletSelect").selectedIndex = 0;
        document.getElementById("networkSelect").selectedIndex = 0;
        document.getElementById("agree").checked = false;

        // Show confirmation
        alertify.alert("Deposit Submitted", "Your deposit was submitted successfully. It will reflect in your wallet after 3 minutes.");

        // Set flag to show delayed notification on user.php
        sessionStorage.setItem("showDepositSuccess", "true");

        // Redirect to user.php after short delay
        setTimeout(() => {
          window.location.href = "user.php";
        }, 1500);
      } else {
        alertify.error(data.message);
      }
    })
    .catch(() => {
      alertify.error("Something went wrong.");
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

  <!-- AlertifyJS Script -->
  <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

</body>

</html>