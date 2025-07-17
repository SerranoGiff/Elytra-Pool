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
$fullName = trim($firstName . ' ' . $lastName);
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
            <a href="staking.php" class="nav-link">Staking</a>
            <a href="leaderboard.php" class="nav-link">Leaderboard</a>
            <a href="deposit.php" class="nav-link">Deposit</a>
            <a href="withdraw.php" class="nav-link">Withdraw</a>
            <a href="Convert.php" class="nav-link">Convert</a>
        </div>
    </nav>

    <!-- Main Section -->
    <main class="flex items-center justify-center px-4 pt-28 pb-20">
        <section class="bg-[#1a1f36] border border-purple-500 p-8 rounded-2xl w-full max-w-3xl shadow-2xl space-y-8 transition">
            <h2 class="text-3xl font-bold text-purple-400 text-center">KYC Verification</h2>

            <!-- KYC FORM -->
            <form id="kycForm" action="../config/kyc_verification.php" method="POST" enctype="multipart/form-data">

                <!-- USER DETAILS -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm text-slate-300">Full Name</label>
                            <input type="text" name="full_name" value="<?= htmlspecialchars($fullName) ?>" requiredd
                                class="mt-1 w-full bg-slate-800 border border-slate-600 rounded-lg p-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500" />
                        </div>
                        <div>
                            <label class="text-sm text-slate-300">Date of Birth</label>
                            <input type="date" name="birth_date" value="<?= htmlspecialchars($birthday) ?>" readonly
                                class="mt-1 w-full bg-slate-800 border border-slate-600 rounded-lg p-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500" />
                        </div>
                        <div>
                            <label class="text-sm text-slate-300">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly
                                class="mt-1 w-full bg-slate-800 border border-slate-600 rounded-lg p-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500" />
                        </div>
                        <div>
                            <label class="text-sm text-slate-300">Username</label>
                            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" readonly
                                class="mt-1 w-full bg-slate-800 border border-slate-600 rounded-lg p-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500" />
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm text-slate-300">Country</label>
                            <input type="text" name="country" placeholder="e.g., Philippines" required
                                class="mt-1 w-full bg-slate-800 border border-slate-600 rounded-lg p-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500" />
                        </div>
                        <div>
                            <label class="text-sm text-slate-300">Document Type</label>
                            <input type="text" name="doc_type" placeholder="e.g., Passport, ID" required
                                class="mt-1 w-full bg-slate-800 border border-slate-600 rounded-lg p-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500" />
                        </div>
                        <div>
                            <label class="text-sm text-slate-300">ID Number</label>
                            <input type="text" name="id_number" required
                                class="mt-1 w-full bg-slate-800 border border-slate-600 rounded-lg p-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500" />
                        </div>
                    </div>
                </div>

                <!-- FILE UPLOADS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="text-sm text-slate-300">ID Front</label>
                        <input type="file" name="front_image" id="idFront" accept="image/*" required
                            class="mt-1 block w-full text-sm text-slate-300 file:mr-3 file:px-4 file:py-2 file:border-0 file:rounded-lg file:bg-purple-600 file:text-white hover:file:bg-purple-700" />
                    </div>
                    <div>
                        <label class="text-sm text-slate-300">ID Back</label>
                        <input type="file" name="back_image" id="idBack" accept="image/*" required
                            class="mt-1 block w-full text-sm text-slate-300 file:mr-3 file:px-4 file:py-2 file:border-0 file:rounded-lg file:bg-purple-600 file:text-white hover:file:bg-purple-700" />
                    </div>
                    <div>
                        <label class="text-sm text-slate-300">Selfie (optional)</label>
                        <input type="file" name="selfie" accept="image/*"
                            class="mt-1 block w-full text-sm text-slate-300 file:mr-3 file:px-4 file:py-2 file:border-0 file:rounded-lg file:bg-purple-600 file:text-white hover:file:bg-purple-700" />
                    </div>
                </div>

                <!-- SUBMIT -->
                <div class="text-right pt-4">
                    <button type="submit"
                        class="bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-400 hover:to-indigo-500 px-4 py-2 rounded w-full font-semibold text-white text-sm">
                        Submit Verification
                    </button>
                </div>
            </form>

            <!-- Support Floating Button -->
            <div class="fixed bottom-6 right-6 z-50 group">
                <button id="supportButton"
                    class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-full shadow-lg hover:shadow-purple-500/50 transition duration-300 ease-in-out animate-bounce hover:scale-110">
                    <i class="fas fa-headset text-white text-xl"></i>
                </button>
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
        document.getElementById("idFront").addEventListener("change", function() {
            const name = this.files[0]?.name.toLowerCase();
            if (!name.includes("front")) {
                alertify.alert("⚠️ Warning", "Please make sure this is the *FRONT* of your ID.");
            }
        });

        document.getElementById("idBack").addEventListener("change", function() {
            const name = this.files[0]?.name.toLowerCase();
            if (!name.includes("back")) {
                alertify.alert("⚠️ Warning", "Please make sure this is the *BACK* of your ID.");
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("kycForm");

            form.addEventListener("submit", function(e) {
                e.preventDefault();

                const formData = new FormData(form); // Get all KYC form data

                fetch("../config/kyc_verification.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            sessionStorage.setItem("showKycSuccess", "true");

                            alertify.alert("KYC Submitted", "Your KYC documents have been submitted successfully. Please wait for admin approval.")
                                .set('onok', function() {
                                    window.location.href = "user.php";
                                });

                        } else {
                            alertify.error(data.message || "KYC submission failed.");
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alertify.error("Something went wrong. Please try again.");
                    });
            });
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