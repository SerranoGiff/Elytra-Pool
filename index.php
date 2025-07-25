<?php
session_start();
include 'config/dbcon.php';
// Check if the user is already logged in
if (isset($_SESSION['role']) && $_SESSION['role'] === 'MasterAdmin') {
  header("Location: pages/master admin/masteradmin.php");
  exit;
} elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin') {
  header("Location: pages/admin/admin.php");
  exit;
} elseif (isset($_SESSION['type']) && $_SESSION['type'] === 'Premium') {
  header("Location: pages/PREMIUM ACCOUNT/premium-dashboard.php");
  exit;
} elseif (isset($_SESSION['type'])) {
  header("Location: pages/user.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Elytra Pool | Staking Platform</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="shortcut icon" href="assets/img/ELYTRA.jpg" type="image/x-icon" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gray-950 text-white">
  <!-- Navigation -->
  <nav class="fixed top-0 left-0 w-full z-50 backdrop-blur-lg bg-white/10 border-b border-white/10 shadow-md transition-all duration-500 ease-in-out">
    <div class="max-w-7xl mx-auto flex justify-between items-center px-6 py-4">

      <!-- Logo and Title -->
      <a href="index.php" class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-full overflow-hidden shadow-md hover:scale-105 transition-transform duration-300">
          <img src="assets/img/Elytra Logo.png" alt="Elytra Logo" class="w-full h-full object-cover" />
        </div>
        <span class="text-xl font-semibold tracking-wide text-white hover:text-purple-300 transition duration-300">
          Elytra Pool
        </span>
      </a>

      <!-- Desktop Navigation Links -->
      <div class="hidden md:flex items-center space-x-8">
        <a href="#home" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Home</span>
          <span class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="#staking" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Staking</span>
          <span class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="#mining" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Features</span>
          <span class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="#earnings" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">Calculator</span>
          <span class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
        <a href="about.php" class="relative group transform hover:scale-105 transition-all duration-300 ease-in-out">
          <span class="text-white hover:text-purple-300 transition">About</span>
          <span class="absolute left-0 -bottom-1 h-0.5 w-0 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
        </a>
      </div>

      <!-- Login Button and Mobile Toggle -->
      <div class="flex items-center space-x-2">
        <!-- Mobile Menu Toggle -->
        <button id="mobile-menu-button" class="md:hidden text-white text-2xl ml-2">
          <i class="fas fa-bars"></i>
        </button>

        <button id="login-button" class="flex items-center space-x-2 px-4 py-2 border border-purple-500 text-purple-400 
        bg-transparent rounded-lg shadow-[0_0_8px_#a855f7]
        hover:bg-purple-600 hover:text-white hover:border-purple-600 
        hover:shadow-[0_0_16px_#9333ea] active:bg-purple-700 
        transition-all duration-300 ease-in-out">
          <i class="fas fa-user transition-colors duration-300"></i>
          <span class="hidden sm:inline transition-colors duration-300">Login</span>
        </button>
      </div>
    </div>

    <!-- Mobile Dropdown Menu -->
    <div id="mobile-menu" class="md:hidden hidden text-white text-center animate-fade-in">
      <a href="#home" class="block px-6 py-4 transition-all duration-300 transform hover:scale-105 hover:text-purple-300 relative group">
        Home
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
      <a href="#staking" class="block px-6 py-4 transition-all duration-300 transform hover:scale-105 hover:text-purple-300 relative group">
        Staking
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
      <a href="#mining" class="block px-6 py-4 transition-all duration-300 transform hover:scale-105 hover:text-purple-300 relative group">
        Features
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
      <a href="#earnings" class="block px-6 py-4 transition-all duration-300 transform hover:scale-105 hover:text-purple-300 relative group">
        Calculator
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
      <a href="about.php" class="block px-6 py-4 transition-all duration-300 transform hover:scale-105 hover:text-purple-300 relative group">
        About
        <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-purple-400 group-hover:w-full transition-all duration-300"></span>
      </a>
    </div>
  </nav>

 <!-- Main Login Modal -->
<div id="loginModal"
  class="fixed inset-0 flex items-center justify-center z-50 hidden transition-opacity duration-300 ease-in-out">
  <div class="bg-gray-900/90 absolute inset-0 backdrop-blur-sm"></div>
  <div
    class="bg-gray-800 border border-purple-500/30 rounded-lg shadow-lg z-10 p-8 max-w-sm w-full overflow-hidden transform transition-transform duration-300 ease-in-out scale-95 opacity-0"
    id="modalContent">
    <div id="modalContentInner">

      <!-- Login Form -->
      <div id="loginForm" class="form-section text-white">
        <h2 class="text-2xl font-bold text-center mb-6">Welcome Back</h2>

        <!-- Notifications -->
        <?php if (isset($_GET['error']) || isset($_GET['success'])): ?>
          <script>
            document.addEventListener("DOMContentLoaded", () => {
              const message = <?= json_encode($_GET['error'] ?? $_GET['success']) ?>;
              const type = <?= isset($_GET['error']) ? json_encode('error') : json_encode('success') ?>;
              showToast(message, type);
              history.replaceState(null, "", window.location.pathname); // remove ?error or ?success
            });
          </script>
        <?php endif; ?>

        <form action="config/login.php" method="POST">
          <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-300">Email Address</label>
            <input type="email" id="email" name="email" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-purple-500 text-white" required />
          </div>
          <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
            <input type="password" id="password" name="password" class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-purple-500 text-white" required />
          </div>
          <div class="flex items-center mb-4">
            <input type="checkbox" id="rememberMe" name="rememberMe" class="h-4 w-4 text-purple-500 focus:ring-purple-500 border-gray-600 rounded bg-gray-700" />
            <label for="rememberMe" class="ml-2 block text-sm text-gray-300 cursor-pointer">Remember Me</label>
          </div>
          <div class="mb-4 text-right">
            <button type="button" id="forgotPasswordBtn" class="text-purple-400 hover:text-purple-300 hover:underline">Forgot Password?</button>
          </div>
          <button type="submit" class="w-full py-2 rounded-lg text-white font-semibold bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:from-indigo-500 hover:via-purple-600 hover:to-indigo-700 transition-all duration-500 ease-in-out">
            Login
          </button>
        </form>

        <div class="mt-4 text-center">
          <button id="switchToSignUp" class="text-purple-400 hover:text-purple-300 hover:underline">Don't have an account? Sign Up</button>
        </div>
      </div>

      <!-- Forgot Password Modal -->
      <div id="forgotPasswordModal"
        class="fixed inset-0 flex items-center justify-center z-50 hidden transition-opacity duration-300 ease-in-out">
        <div class="bg-gray-900/90 absolute inset-0 backdrop-blur-sm"></div>
        <div
          class="bg-gray-800 border border-purple-500/30 rounded-lg shadow-lg z-10 p-6 w-full max-w-md transform transition-transform duration-300 ease-in-out scale-95 opacity-0"
          id="forgotPasswordContent">
          <h2 class="text-lg font-bold mb-4 text-purple-300">Reset Your Password</h2>
          <p class="mb-4 text-sm text-gray-400">Enter your email address and we'll send you a password reset link.</p>
          <form id="forgotPasswordForm">
            <input type="email" id="forgotEmail"
              class="w-full mb-4 bg-gray-700 border border-gray-600 rounded-md p-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
              placeholder="Email address" required />
            <div class="flex justify-end gap-2">
              <button type="button" id="cancelForgot"
                class="px-4 py-2 text-sm bg-gray-600 hover:bg-gray-500 rounded-lg text-white transition">Cancel</button>
              <button type="submit" class="px-4 py-2 text-sm text-white rounded-lg
       bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700
       hover:from-indigo-500 hover:via-purple-600 hover:to-indigo-700
       shadow-md hover:shadow-lg hover:shadow-purple-500/40
       transition-all duration-700 ease-in-out">
                Send Reset Link
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Sign Up Form -->
      <div id="signUpForm" class="form-section hidden text-white">
        <h2 class="text-2xl font-bold text-center mb-6">Create an Account</h2>
        <form action="config/registration.php" method="POST">
          <div class="mb-3">
            <label for="username" class="block text-sm font-medium text-gray-300">Username</label>
            <input type="text" id="username" name="username" required
              class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-purple-500 text-white" />
          </div>
          <div class="mb-3">
            <label for="signUpEmail" class="block text-sm font-medium text-gray-300">Email Address</label>
            <input type="email" id="signUpEmail" name="email" required
              class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-purple-500 text-white" />
          </div>
          <div class="mb-3">
            <label for="signUpPassword" class="block text-sm font-medium text-gray-300">Password</label>
            <input type="password" id="signUpPassword" name="password" required
              class="mt-1 block w-full bg-gray-700 border border-gray-600 rounded-md p-3 focus:outline-none focus:ring-2 focus:ring-purple-500 text-white" />
            <p id="passwordStrength" class="mt-1 text-sm text-gray-400"></p>
          </div>
          <div class="mb-3">
            <div class="flex items-center">
              <input type="checkbox" id="agreeTerms" name="agreeTerms"
                class="h-4 w-4 text-purple-500 focus:ring-purple-500 border-gray-600 rounded bg-gray-700 cursor-pointer" />
              <label for="agreeTerms" class="ml-2 block text-sm text-gray-300 cursor-pointer">I agree to the <span
                  class="text-purple-400 hover:text-purple-300 hover:underline">Terms and Conditions</span></label>
            </div>
          </div>
          <div class="mb-3">
            <div class="flex items-center">
              <input type="checkbox" id="agreePrivacy" name="agreePrivacy"
                class="h-4 w-4 text-purple-500 focus:ring-purple-500 border-gray-600 rounded bg-gray-700 cursor-pointer" />
              <label for="agreePrivacy" class="ml-2 block text-sm text-gray-300 cursor-pointer">I have read and agree
                to the <span class="text-purple-400 hover:text-purple-300 hover:underline">Privacy Policy</span></label>
            </div>
          </div>
          <button type="submit"
            class="w-full py-2 rounded-lg text-white font-semibold bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:from-indigo-500 hover:via-purple-600 hover:to-indigo-700 shadow-md hover:shadow-lg hover:shadow-purple-500/40 transition-all duration-700 ease-in-out">Sign
            Up</button>
        </form>
        <div class="mt-4 text-center">
          <button id="switchToLogin" class="text-purple-400 hover:text-purple-300 hover:underline">Already have an account? Login</button>
        </div>
      </div>

      <!-- Toast Notification Container -->
      <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

    </div>
    <div class="mt-4 text-center">
      <button id="closeModal" class="text-gray-400 hover:text-white transition duration-200">Close</button>
    </div>
  </div>
</div>

<!-- Terms and Conditions Modal -->
<div id="infoModal"
  class="fixed inset-0 flex items-center justify-center z-50 hidden transition-opacity duration-300 ease-in-out">
  <div class="bg-gray-900/90 absolute inset-0 backdrop-blur-sm"></div>
  <div
    class="bg-gray-800 border border-purple-500/30 rounded-lg shadow-lg z-10 p-8 max-w-md w-full overflow-hidden transform transition-transform duration-300 ease-in-out scale-95 opacity-0"
    id="infoModalContent">
    <h2 class="text-purple-300 font-bold mb-4" id="modalTitle">Terms & Conditions</h2>
    <div class="max-h-96 overflow-y-auto" id="termsContent">
      <p id="modalContentText" class="text-gray-300 mb-4">
        <strong class="text-white">ELYTRA POOL TERMS OF SERVICE</strong><br />
        <strong class="text-gray-400">Last Updated:</strong> June 22, 2025<br /><br />
        <strong class="text-white">1. Definitions</strong><br />
        1.1. <strong>"Platform"</strong> refers to Elytra Pool, its affiliates, subsidiaries, and any associated
        decentralized protocols.<br />
        1.2. <strong>"User "</strong> ("you") means any entity interacting with the Platform, including but not
        limited to stakers, referrers, and purchasers of digital assets.<br />
        1.3. <strong>"Rewards"</strong> denote non-guaranteed, variable incentives distributed at the Platform's
        sole discretion, with no expectation of profit.<br /><br />
        <strong class="text-white">2. Acceptance of Terms</strong><br />
        2.1. By accessing the Platform, you <strong>irrevocably consent</strong> to these Terms, our Privacy
        Policy, and any future amendments (posted without notice).<br />
        2.2. Continued use constitutes <strong>binding arbitration agreement</strong> (waiving class action rights
        per Section 9.4).<br /><br />
        <strong class="text-white">3. Eligibility & Account Creation</strong><br />
        3.1. Users affirm they are <strong>not</strong> a citizen/resident of prohibited jurisdictions (e.g., USA,
        Cuba, North Korea) unless compliant with local regulations.<br />
        3.2. <strong>No Guaranteed Access</strong>: Accounts may be terminated without explanation (Section
        7.3).<br /><br />
        <strong class="text-white">4. Staking & Rewards (No Promises)</strong><br />
        4.1. <strong>Variable Rewards</strong>: APY estimates are hypothetical, subject to smart contract risks,
        slashing, and protocol changes.<br />
        4.2. <strong>No Ownership</strong>: Staked assets remain User's property, but rewards are
        <strong>unsecured claims</strong> until distributed.<br />
        4.3. <strong>Tax Liability</strong>: Users alone are responsible for reporting rewards as income (consult
        a tax advisor).<br /><br />
        <strong class="text-white">5. Referral Program (No Pyramid Schemes)</strong><br />
        5.1. Referral rewards are <strong>limited to 10 levels deep</strong> to avoid regulatory classification as
        a security/Ponzi.<br />
        5.2. Platform reserves the right to <strong>withhold referrals</strong> deemed fraudulent (no
        appeals).<br /><br />
        <strong class="text-white">6. NFT Avatars & Microtransactions</strong><br />
        6.1. <strong>Non-Refundable</strong>: All purchases of digital assets (e.g., avatars, gems) are
        final.<br />
        6.2. <strong>No Financial Utility</strong>: Avatars confer no staking advantages—purely
        cosmetic.<br /><br />
        <strong class="text-white">7. Termination & Fund Seizure</strong><br />
        7.1. <strong>At-Will Suspension</strong>: We may freeze accounts for "suspicious activity"
        (undefined).<br />
        7.2. <strong>Abandoned Accounts</strong>: Balances inactive >12 months may be <strong>repurposed as
          protocol fees</strong>.<br /><br />
        <strong class="text-white">8. Disclaimers (No Liability)</strong><br />
        8.1. <strong>As-Is Service</strong>: The Platform disclaims warranties of merchantability, fitness, or
        non-infringement.<br />
        8.2. <strong>Third-Party Risks</strong>: We are not liable for exploits in underlying blockchains (e.g.,
        Ethereum, Solana).<br /><br />
        <strong class="text-white">9. Governing Law & Arbitration</strong><br />
        9.1. <strong>Jurisdiction</strong>: Disputes resolved under [Cayman Islands law] (favors
        arbitration).<br />
        9.2. <strong>Class Action Waiver</strong>: Users may only pursue individual claims (no class
        actions).<br /><br />
        <strong class="text-white">10. Amendments</strong><br />
        10.1. <strong>Unilateral Changes</strong>: We may modify these Terms at any time; continued use =
        acceptance.
      </p>
    </div>
    <div class="text-center mt-4">
      <button id="confirmTerms"
        class="rounded-lg px-4 py-2 transition duration-200 bg-gray-600 text-white cursor-not-allowed" disabled>I
        Accept</button>
    </div>
  </div>
</div>

<!-- Privacy Policy Modal -->
<div id="privacyModal"
  class="fixed inset-0 flex items-center justify-center z-50 hidden transition-opacity duration-300 ease-in-out">
  <div class="bg-gray-900/90 absolute inset-0 backdrop-blur-sm"></div>
  <div
    class="bg-gray-800 border border-purple-500/30 rounded-lg shadow-lg z-10 p-8 max-w-md w-full overflow-hidden transform transition-transform duration-300 ease-in-out scale-95 opacity-0"
    id="privacyModalContent">
    <h2 class="text-purple-300 font-bold mb-4" id="modalTitle">Privacy Policy</h2>
    <div class="max-h-96 overflow-y-auto" id="privacyContent">
      <p id="modalContentText" class="text-gray-300 mb-4">
        <strong class="text-white">ELYTRA POOL PRIVACY POLICY</strong><br />
        <strong class="text-gray-400">Last Updated:</strong> June 22, 2025<br /><br />

        <strong class="text-white">1. Information We Collect</strong><br />
        1.1. We may collect personal information that you provide to us directly, such as your name, email
        address, and any other information you choose to provide.<br />
        1.2. We also collect wallet addresses, on-chain activity, IP addresses, browser/device information, and
        technical metadata.<br /><br />

        <strong class="text-white">2. How We Use Your Information</strong><br />
        2.1. We use your data to enable core services (staking, transactions).<br />
        2.2. We use data to improve security, detect fraud, and comply with legal obligations.<br />
        2.3. We may send updates or marketing messages (opt-out is available).<br /><br />

        <strong class="text-white">3. Sharing Your Information</strong><br />
        3.1. We do not sell or rent your personal information.<br />
        3.2. We may share data with third-party vendors (e.g., KYC providers), validators, cloud infrastructure,
        or when legally required.<br /><br />

        <strong class="text-white">4. Data Retention</strong><br />
        4.1. KYC data is stored for 5 years unless legally mandated otherwise.<br />
        4.2. Wallet activity and public blockchain data are stored indefinitely.<br />
        4.3. Cookies and session data may be retained for up to 30 days.<br /><br />

        <strong class="text-white">5. Security Measures</strong><br />
        5.1. We implement encryption, secure wallets, and periodic audits.<br />
        5.2. However, no system is 100% secure. Use the platform at your own risk.<br /><br />

        <strong class="text-white">6. Your Rights</strong><br />
        6.1. Depending on your jurisdiction, you may access, update, or request deletion of your data.<br />
        6.2. You may opt-out of marketing or withdraw consent, though some features may become
        restricted.<br /><br />

        <strong class="text-white">7. Changes to This Policy</strong><br />
        7.1. We may modify this policy from time to time.<br />
        7.2. Continued use of the platform signifies acceptance of updates.<br /><br />

        <strong class="text-white">8. Contact Us</strong><br />
        For privacy-related concerns:<br />
        Email: privacy@elytrapool.com<br />
        Website: https://elytrapool.com/contact<br /><br />

        By using Elytra Pool, you acknowledge that you have read and agree to this Privacy Policy.
      </p>
    </div>
    <div class="text-center mt-4">
      <button id="confirmPrivacy"
        class="rounded-lg px-4 py-2 transition duration-200 bg-gray-600 text-white cursor-not-allowed" disabled>I
        Accept</button>
    </div>
  </div>
</div>
  </nav>

  <!-- Hero Section -->
  <section id="home" class="pt-32 pb-20 px-6">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
      <!-- Left Text Block -->
      <div class="slide-in-left">
        <h1 class="text-4xl md:text-5xl font-bold mb-6 text-white">Earn Crypto <span class="text-purple-400">Through
            Staking</span></h1>
        <p class="text-lg text-gray-300 mb-8 max-w-xl">
          <strong class="text-white">Earn up to 25% Daily Average Earning by staking crypto through secure,
            decentralized Web3
            protocols.</strong><br />
          Grow your portfolio with blockchain-based & User-friendly, Higher rewards, and no custodians. Join millions of
          Web3 users maximizing yields while staying in full control of your assets.
        </p>
        <div class="flex flex-wrap gap-4 mb-10">
          <button id="start-earning-button"
            class="bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 text-white px-6 py-3 rounded-lg shadow-md hover:shadow-lg hover:brightness-110 transition duration-300 ease-in-out">
            Start Earning
          </button>
          <a href="#howitworks"
            class="border border-purple-500 text-purple-300 px-6 py-3 rounded-lg hover:bg-purple-600 hover:text-white hover:border-transparent transition duration-300 ease-in-out">
            How It Works
          </a>
        </div>

        <div class="mt-8 flex flex-wrap gap-6 text-gray-300">
          <div class="flex items-center">
            <div class="w-3 h-3 bg-green-400 rounded-full mr-2"></div><span>User-friendly Platform</span>
          </div>
          <div class="flex items-center">
            <div class="w-3 h-3 bg-blue-400 rounded-full mr-2"></div><span>On-Chain Rewards</span>
          </div>
          <div class="flex items-center">
            <div class="w-3 h-3 bg-purple-400 rounded-full mr-2"></div><span>24/7 Customer Support</span>
          </div>
          <div class="flex items-center">
            <div class="w-3 h-3 bg-cyan-400 rounded-full mr-2"></div><span>Web3 Automation</span>
          </div>
        </div>
      </div>

      <div class="relative slide-in-right w-full">
        <div class="w-full p-3 sm:p-4 rounded-xl shadow-lg bg-gray-800">
          <!-- Header -->
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-1">
            <h3 class="text-base sm:text-lg font-semibold text-white leading-tight">Staking Dashboard</h3>
            <div id="mining-status" class="text-purple-400 text-xs sm:text-sm font-medium">Active</div>
          </div>

          <!-- Chart -->
          <div class="w-full h-36 sm:h-48 mb-4">
            <canvas id="miningChart" class="w-full h-full"></canvas>
          </div>

          <!-- Stats -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4 text-white text-sm">
            <div>
              <div class="text-xs text-gray-400">Hashrate</div>
              <div id="hashrate" class="text-base font-semibold">0.00 TH/s</div>
            </div>
            <div>
              <div class="text-xs text-gray-400">Daily Earnings</div>
              <div id="daily-earnings" class="text-base font-semibold">0.000000 BTC</div>
            </div>
          </div>

          <!-- Progress -->
          <div>
            <div class="flex justify-between text-xs text-white mb-1">
              <span>Progress</span>
              <span id="progress-percent">0%</span>
            </div>
            <div class="w-full bg-gray-700 rounded-full h-1.5">
              <div id="progress-bar"
                class="bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 h-1.5 rounded-full transition-all duration-500 ease-in-out"
                style="width: 0%"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          class="p-6 rounded-2xl text-center bg-gradient-to-r from-indigo-500 via-purple-600 to-indigo-700 shadow-md transition-transform duration-300 hover:scale-105">
          <div class="text-3xl font-bold text-white mb-2">$1.2B+</div>
          <div class="text-white-600 text-sm">Assets Staked</div>
        </div>

        <div
          class="p-6 rounded-2xl text-center bg-gradient-to-r from-indigo-500 via-purple-600 to-indigo-700 shadow-md transition-transform duration-400 hover:scale-105">
          <div id="active-users" class="text-3xl font-bold text-white mb-2">1,500,000</div>
          <div class="text-white-600 text-sm">Active Users</div>
        </div>

        <div
          class="p-6 rounded-2xl text-center bg-gradient-to-r from-indigo-500 via-purple-600 to-indigo-700 shadow-md transition-transform duration-300 hover:scale-105">
          <div class="text-3xl font-bold text-white mb-2">15+</div>
          <div class="text-white-600 text-sm">Supported Coins</div>
        </div>

        <div
          class="p-6 rounded-2xl text-center bg-gradient-to-r from-indigo-500 via-purple-600 to-indigo-700 shadow-md transition-transform duration-300 hover:scale-105">
          <div id="uptime" class="text-3xl font-bold text-white mb-2">99.9%</div>
          <div class="text-white-600 text-sm">Uptime</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Staking Section -->
  <section id="staking" class="py-20 px-6">
    <div class="max-w-7xl mx-auto">
      <h2 class="text-3xl font-bold mb-2 text-center text-purple-400">Earn Rewards On Staking</h2>
      <p class="text-gray-400 mb-12 text-center max-w-2xl mx-auto">Earn passive income by staking your cryptocurrencies
        with our secure platform.</p>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Bitcoin Staking -->
        <div class="staking-card p-6 rounded-2xl cursor-pointer fade-in" style="animation-delay: 0.1s">
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
            <div id="btc-apy" class="bg-blue-500/10 text-blue-400 px-3 py-1 rounded-full text-sm">6.5% APY</div>
          </div>
          <div class="mb-6">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-400">Total Staked</span>
              <span id="btc-staked">12,450 BTC</span>
            </div>
            <div class="w-full bg-gray-800 rounded-full h-2">
              <div class="progress-bar" style="width: 65%"></div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
              <div class="text-sm text-gray-400">Min. Stake</div>
              <div id="btc-min" class="font-semibold">0.00094 BTC</div>
            </div>
            <div>
              <div class="text-sm text-gray-400">Lock Period</div>
              <div id="btc-lock" class="font-semibold">30 Days</div>
            </div>
          </div>
          <button id="btc-stake-button"
            class="btn-primary w-full py-2 rounded-lg text-center block bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:bg-blue-700">Stake
            Now</button>
        </div>

        <!-- Ethereum Staking -->
        <div class="staking-card p-6 rounded-2xl cursor-pointer fade-in" style="animation-delay: 0.2s">
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
            <div id="eth-apy" class="bg-blue-500/10 text-blue-400 px-3 py-1 rounded-full text-sm">5.2% APY</div>
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
              <div id="eth-min" class="font-semibold">0.041 ETH</div>
            </div>
            <div>
              <div class="text-sm text-gray-400">Lock Period</div>
              <div id="eth-lock" class="font-semibold">14 Days</div>
            </div>
          </div>
          <button id="eth-stake-button"
            class="btn-primary w-full py-2 rounded-lg text-center block bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:bg-blue-700">Stake
            Now</button>
        </div>

        <!-- Solana Staking -->
        <div class="staking-card p-6 rounded-2xl cursor-pointer fade-in" style="animation-delay: 0.3s">
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
            <div id="sol-apy" class="bg-blue-500/10 text-blue-400 px-3 py-1 rounded-full text-sm">8.7% APY</div>
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
              <div id="sol-min" class="font-semibold">0.65 SOL</div>
            </div>
            <div>
              <div class="text-sm text-gray-400">Lock Period</div>
              <div id="sol-lock" class="font-semibold">7 Days</div>
            </div>
          </div>
          <button id="sol-stake-button"
            class="btn-primary w-full py-2 rounded-lg text-center block bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:bg-blue-700">Stake
            Now</button>
        </div>
      </div>
      <div class="mt-12 text-center">
        <button id="view-all-button" class="btn-secondary px-6 py-3 rounded-lg">View All Staking Options</button>
      </div>

      <!-- Hidden Section for Additional Cryptocurrencies -->
      <div id="additional-cryptos" class="hidden mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card for Cardano -->
        <div class="staking-card p-6 rounded-2xl cursor-pointer fade-in h-full" style="animation-delay: 0.1s">
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center">
              <div
                class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3 text-white text-lg font-bold">
                ₳</div>
              <div>
                <div class="font-semibold">Cardano</div>
                <div class="text-sm text-gray-400">ADA Staking</div>
              </div>
            </div>
            <div id="ada-apy" class="bg-blue-500/10 text-blue-400 px-3 py-1 rounded-full text-sm">5.0% APY</div>
          </div>
          <div class="mb-6">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-400">Total Staked</span>
              <span id="ada-staked">100,000 ADA</span>
            </div>
            <div class="w-full bg-gray-800 rounded-full h-2">
              <div class="progress-bar" style="width: 50%"></div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
              <div class="text-sm text-gray-400">Min. Stake</div>
              <div id="ada-min" class="font-semibold">10 ADA</div>
            </div>
            <div>
              <div class="text-sm text-gray-400">Lock Period</div>
              <div id="ada-lock" class="font-semibold">14 Days</div>
            </div>
          </div>
          <button id="ada-stake-button"
            class="btn-primary w-full py-2 rounded-lg text-center block bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:bg-blue-700">Stake
            Now</button>
        </div>

        <!-- Card for Polkadot -->
        <div class="staking-card p-6 rounded-2xl cursor-pointer fade-in h-full" style="animation-delay: 0.4s">
          <div class="flex justify-between items-start mb-4">
            <div class="flex items-center">
              <div
                class="w-10 h-10 bg-pink-500 rounded-full flex items-center justify-center mr-3 text-white text-lg font-bold">
                ●</div>
              <div>
                <div class="font-semibold">Polkadot</div>
                <div class="text-sm text-gray-400">DOT Staking</div>
              </div>
            </div>
            <div id="dot-apy" class="bg-blue-500/10 text-blue-400 px-3 py-1 rounded-full text-sm">7.0% APY</div>
          </div>
          <div class="mb-6">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-gray-400">Total Staked</span>
              <span id="dot-staked">500,000 DOT</span>
            </div>
            <div class="w-full bg-gray-800 rounded-full h-2">
              <div class="progress-bar" style="width: 60%"></div>
            </div>
          </div>
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
              <div class="text-sm text-gray-400">Min. Stake</div>
              <div id="dot-min" class="font-semibold">5 DOT</div>
            </div>
            <div>
              <div class="text-sm text-gray-400">Lock Period</div>
              <div id="dot-lock" class="font-semibold">28 Days</div>
            </div>
          </div>
          <button id="dot-stake-button"
            class="btn-primary w-full py-2 rounded-lg text-center block bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:bg-blue-700">Stake
            Now</button>
        </div>
      </div>

      <!-- Close View Button -->
      <div id="close-view" class="hidden mt-12 text-center">
        <button id="close-view-button" class="btn-secondary px-6 py-3 rounded-lg">Close View</button>
      </div>
    </div>
  </section>

  <!-- Mining Section -->
  <section id="mining" class="py-20 px-6 text-center">
    <div class="max-w-7xl mx-auto">
      <h2 class="text-3xl font-bold mb-2 text-purple-400">Staking Features</h2>
      <p class="text-gray-400 mb-12 max-w-2xl mx-auto">Start mining cryptocurrency without hardware or technical
        knowledge.</p>

      <!-- Cards Container -->
      <div class="flex flex-col md:flex-row justify-center items-stretch gap-6">
        <!-- Starter Plan -->
        <div
          class="mining-card p-6 rounded-2xl cursor-pointer fade-in w-full md:w-1/3 flex flex-col h-full relative border-2"
          style="animation-delay: 0.1s; border-color: #CF9FFF;">
          <div class="text-center mb-6">
            <h3 class="text-xl font-semibold">Starter Plan</h3>
            <div class="text-3xl font-bold mt-2"><span class="text-red-400 line-through text-base mr-2">199
                USDT</span><span class="text-emerald-600">Free</span></div>
            <div class="text-gray-400 text-sm">Limited Time Only</div>
          </div>

          <div class="space-y-4 mb-6 flex-1">
            <div class="flex items-center"><i class="fas fa-check text-emerald-400 mr-2"></i><span>100 GH/s
                Hashrate</span></div>
            <div class="flex items-center"><i class="fas fa-check text-emerald-400 mr-2"></i><span>2x a Month
                Withdrawal</span></div>
            <div class="flex items-center"><i class="fas fa-check text-emerald-400 mr-2"></i><span>5% Pool Fee</span>
            </div>
            <div class="flex items-center"><i class="fas fa-check text-emerald-400 mr-2"></i><span>3 Cycles in a Row
                Limit</span></div>
            <div class="flex items-center"><i class="fas fa-check text-emerald-400 mr-2"></i><span>Real Time
                Rewards</span></div>
            <div class="flex items-center"><i class="fas fa-times text-red-400 mr-2"></i><span>24/7 Monitoring</span>
            </div>
            <div class="flex items-center"><i class="fas fa-times text-red-400 mr-2"></i><span>Priority
                Support</span></div>
            <div class="flex items-center"><i class="fas fa-times text-red-400 mr-2"></i><span>Limited Access</span>
            </div>
          </div>

          <button id="start-staking-button"
            class="btn-primary w-full py-2 rounded-lg mt-auto bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 hover:bg-blue-700 text-white font-semibold transition duration-200">Start
            Staking</button>
        </div>

        <!-- Premium Plan -->
        <div
          class="mining-card fire-hover transition-all duration-300 ease-in-out p-6 rounded-2xl cursor-pointer fade-in w-full md:w-1/3 flex flex-col h-full relative border-2"
          style="animation-delay: 0.1s; border-color: #CF9FFF;">
          <div class="absolute top-0 right-0 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg"
            style="background-color: #CF9FFF;">HOT DEALS 🔥</div>

          <div class="text-center mb-6">
            <h3 class="text-xl font-semibold">Premium</h3>
            <div class="text-3xl font-bold mt-2">499 USDT</div>
            <div class="text-gray-400">per month</div>
          </div>
          <div class="space-y-4 mb-6 flex-1">
            <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i><span>1 TH/s Hashrate</span>
            </div>
            <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i><span>Weekly
                Withdrawals</span></div>
            <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i><span>1.9% Pool Fee</span>
            </div>
            <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i><span>24/7 Live
                Monitoring</span></div>
            <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i><span>Priority Support
                Access</span></div>
            <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i><span>Unlimited Staking
                Cycles</span></div>
            <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i><span>Advanced Analytics
                Dashboard</span></div>
            <div class="flex items-center"><i class="fas fa-check text-green-400 mr-2"></i><span>Exclusive Access</span>
            </div>
          </div>
          <button id="premium-stake-button"
            class="btn-primary w-full py-2 bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 rounded-lg mt-auto">Start
            Staking</button>
        </div>
      </div>
    </div>
  </section>

  <section id="earnings" class="py-20 px-4 sm:px-6 text-center">
    <div class="max-w-2xl mx-auto">
      <h2 class="text-3xl font-bold mb-4">Earnings Calculator</h2>
      <p class="text-gray-400 mb-8">Estimate your staking rewards in USDT. Minimum amount depends on duration.</p>

      <div class="bg-gray-900 bg-opacity-60 p-6 sm:p-8 rounded-2xl shadow-xl text-left">
        <!-- Staking Period -->
        <div class="mb-6">
          <label for="period" class="block text-sm text-gray-400 mb-2">Staking Period</label>
          <select id="period" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2">
            <option value="3">3 Days</option>
            <option value="7">7 Days</option>
            <option value="15">15 Days</option>
            <option value="30">30 Days</option>
            <option value="60">60 Days</option>
            <option value="90">90 Days</option>
          </select>
        </div>

        <!-- Crypto Selection -->
        <div class="mb-6">
          <label for="crypto" class="block text-sm text-gray-400 mb-2">Cryptocurrency</label>
          <select id="crypto" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2">
            <option value="bitcoin">Bitcoin (BTC)</option>
            <option value="ethereum">Ethereum (ETH)</option>
            <option value="solana">Solana (SOL)</option>
            <option value="cardano">Cardano (ADA)</option>
            <option value="polkadot">Polkadot (DOT)</option>
          </select>
        </div>

        <!-- Amount Input -->
        <div class="mb-2">
          <label for="amount" class="flex items-center gap-2 text-sm text-gray-400 mb-2">
            Amount to Stake (USDT)
            <span class="relative group cursor-pointer">
              <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path
                  d="M18 10A8 8 0 112 10a8 8 0 0116 0zM9 7a1 1 0 112 0v1a1 1 0 01-2 0V7zm0 4a1 1 0 012 0v3a1 1 0 01-2 0v-3z" />
              </svg>
              <div
                class="absolute z-10 hidden group-hover:block w-56 text-xs text-white bg-gray-700 p-2 rounded shadow-lg mt-2 left-1/2 -translate-x-1/2">
                Minimum stake varies by duration:
                <ul class="list-disc list-inside">
                  <li>3D: 100</li>
                  <li>7D: 3,000</li>
                  <li>15D: 7,000</li>
                  <li>30D: 10,000</li>
                  <li>60D: 30,000</li>
                  <li>90D: 50,000</li>
                </ul>
              </div>
            </span>
          </label>
          <input id="amount" type="number" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2"
            value="1000" />
        </div>
        <p id="min-note" class="text-sm text-yellow-400 mb-6"></p>

        <!-- Calculate Button -->
        <button id="calculateBtn" type="button"
          class="relative w-full py-3 bg-gradient-to-r from-purple-500 via-indigo-600 to-purple-700 rounded-lg text-white flex items-center justify-center">
          <span>Calculate Earnings</span>
          <svg id="spinner" class="hidden ml-3 w-5 h-5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
          </svg>
        </button>

        <p id="error-msg" class="text-red-500 mt-4 text-sm"></p>

        <!-- 2.5 Result Box -->
        <div id="results" class="bg-gray-800 p-4 rounded-lg mt-6 space-y-3 hidden">
          <div class="flex justify-between"><span class="text-gray-400">APY</span><span id="apy"
              class="font-semibold text-white"></span></div>
          <div class="flex justify-between"><span class="text-gray-400">Estimated Rewards</span><span id="rewards"
              class="font-semibold text-white"></span></div>
          <div class="flex justify-between"><span class="text-gray-400">Total Return</span><span id="total"
              class="font-semibold text-white"></span></div>
          <div class="flex justify-between"><span class="text-gray-400">Daily Earnings</span><span id="daily"
              class="font-semibold text-white"></span></div>
          <div class="flex justify-between border-t border-gray-700 pt-2"><span class="text-gray-400">Earnings (in
              USDT)</span><span id="earnings-usdt" class="font-semibold text-green-400"></span></div>
          <div class="flex justify-between border-t border-gray-700 pt-2"><span class="text-gray-400">Earnings (in
              Elytra)</span><span id="earnings-elytra" class="font-semibold text-purple-400"></span></div>
        </div>

      </div>
    </div>
  </section>

  <!-- How It Works -->
  <section id="howitworks" class="py-20 px-6 text-white">
    <div class="max-w-7xl mx-auto">
      <h2 class="text-3xl md:text-4xl font-bold mb-4 text-center"><span class="text-white">How</span> <span
          class="text-purple-500">It Works</span></h2>
      <p class="text-gray-400 mb-16 text-center max-w-3xl mx-auto">Get started with cryptocurrency staking in just four
        simple steps. Our streamlined process makes it easy for anyone to start earning rewards.</p>

      <div class="grid md:grid-cols-4 gap-8 text-center">
        <!-- Step Template -->
        <div
          class="border border-purple-700 p-6 rounded-lg bg-gray-900 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-xl hover:shadow-purple-500/40 active:scale-95 cursor-pointer">
          <div
            class="w-12 h-12 rounded-full bg-purple-500 text-white flex items-center justify-center mx-auto mb-4 text-sm font-bold">
            01</div>
          <div class="text-purple-400 text-3xl mb-4"><i class="fas fa-wallet"></i></div>
          <h3 class="text-lg font-semibold mb-2">Connect Your Wallet</h3>
          <p class="text-gray-400 text-sm">Securely connect your cryptocurrency wallet to our platform using
            industry-standard protocols.</p>
        </div>

        <div
          class="border border-purple-700 p-6 rounded-lg bg-gray-900 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-xl hover:shadow-purple-500/40 active:scale-95 cursor-pointer">
          <div
            class="w-12 h-12 rounded-full bg-purple-500 text-white flex items-center justify-center mx-auto mb-4 text-sm font-bold">
            02</div>
          <div class="text-purple-400 text-3xl mb-4"><i class="fas fa-coins"></i></div>
          <h3 class="text-lg font-semibold mb-2">Choose Your Assets</h3>
          <p class="text-gray-400 text-sm">Select from a wide range of supported cryptocurrencies and staking pools with
            competitive APY rates.</p>
        </div>

        <div
          class="border border-purple-700 p-6 rounded-lg bg-gray-900 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-xl hover:shadow-purple-500/40 active:scale-95 cursor-pointer">
          <div
            class="w-12 h-12 rounded-full bg-purple-500 text-white flex items-center justify-center mx-auto mb-4 text-sm font-bold">
            03</div>
          <div class="text-purple-400 text-3xl mb-4"><i class="fas fa-lock"></i></div>
          <h3 class="text-lg font-semibold mb-2">Stake & Secure</h3>
          <p class="text-gray-400 text-sm">Your assets are securely staked in our enterprise-grade infrastructure with
            institutional security.</p>
        </div>

        <div
          class="border border-purple-700 p-6 rounded-lg bg-gray-900 transition-all duration-300 transform hover:-translate-y-2 hover:shadow-xl hover:shadow-purple-500/40 active:scale-95 cursor-pointer">
          <div
            class="w-12 h-12 rounded-full bg-purple-500 text-white flex items-center justify-center mx-auto mb-4 text-sm font-bold">
            04</div>
          <div class="text-purple-400 text-3xl mb-4"><i class="fas fa-chart-line"></i></div>
          <h3 class="text-lg font-semibold mb-2">Earn Rewards</h3>
          <p class="text-gray-400 text-sm">Watch your rewards grow daily with automatic compounding and real-time
            tracking.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Call to Action -->
  <section class="py-20 px-6 bg-gradient-to-br from-purple-900 to-indigo-900">
    <div class="max-w-7xl mx-auto text-center">
      <h2 class="text-3xl font-bold mb-4">Ready to Start Earning with Elytra Pool?</h2>
      <p class="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">Earn up to <span class="font-semibold text-white">25%
          Daily Average Earnings</span> by staking crypto through secure, decentralized protocols. Grow your portfolio
        with high-yield
        staking no middlemen, just full control and reliable rewards.</p>

      <button id="get-started-button"
        class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">Get Started
        Now</button>
    </div>
  </section>

  <!-- Footer -->
  <footer class="py-12 px-6 border-t border-gray-800">
    <div class="max-w-7xl mx-auto">
      <div class="grid md:grid-cols-4 gap-8 mb-8">
        <div class="flex items-center space-x-2">
          <a href="index.php" class="flex items-center">
            <div
              class="w-10 h-10 rounded-full flex items-center justify-center pulse hover:scale-105 transition-transform duration-200">
              <img src="assets/img/Elytra Logo.png" alt="Elytra Logo" class="w-full h-full rounded-full object-cover" />
            </div>
            <span class="text-xl font-bold text-white hover:text-blue-200 transition-colors duration-200">Elytra
              Pool</span>
          </a>
        </div>

        <div>
          <h4 class="font-semibold mb-4">Products</h4>
          <ul class="space-y-2 text-sm text-gray-400">
            <li><a href="#staking" class="hover:text-white">Staking</a></li>
            <li><a href="#mining" class="hover:text-white">Premium</a></li>
            <li><a href="#earnings" class="hover:text-white">Calculator</a></li>
            <li><a href="faq.php" class="hover:text-white">FAQ</a></li>
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
        <div class="text-sm text-gray-400 mb-4 md:mb-0">© 2023 - 2025 Elytra Pool. All rights reserved.</div>
        <div class="flex space-x-6">
          <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
          <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-telegram"></i></a>
          <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-discord"></i></a>
        </div>
      </div>
    </div>
  </footer>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const menuButton = document.getElementById("menu-button");
      const navLinks = document.getElementById("nav-links");

      menuButton.addEventListener("click", () => {
        navLinks.classList.toggle("active");
      });
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const menuBtn = document.getElementById("mobile-menu-button");
      const mobileMenu = document.getElementById("mobile-menu");

      menuBtn?.addEventListener("click", () => {
        mobileMenu.classList.toggle("hidden");
      });
    });
  </script>

  <script>
    const viewAllButton = document.getElementById("view-all-button");
    const closeViewButton = document.getElementById("close-view-button");
    const additionalCryptos = document.getElementById("additional-cryptos");
    const closeView = document.getElementById("close-view");

    viewAllButton.addEventListener("click", function() {
      additionalCryptos.classList.remove("hidden");
      viewAllButton.classList.add("hidden");
      closeView.classList.remove("hidden");
    });

    closeViewButton.addEventListener("click", function() {
      additionalCryptos.classList.add("hidden");
      viewAllButton.classList.remove("hidden");
      closeView.classList.add("hidden");
    });
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
      }, 3000); // show for 3 seconds
    }
  </script>

  <script>
    const loginModal = document.getElementById("loginModal");
    const closeModal = document.getElementById("closeModal");
    const switchToSignUp = document.getElementById("switchToSignUp");
    const loginForm = document.getElementById("loginForm");

    document.getElementById("login-button").addEventListener("click", () => {
      loginForm.classList.remove("hidden");
      loginModal.classList.remove("hidden");
      setTimeout(() => {
        loginModal.querySelector("#modalContent").classList.remove("scale-95", "opacity-0");
        loginModal.querySelector("#modalContent").classList.add("scale-100", "opacity-100");
      }, 10);
    });

    closeModal.addEventListener("click", () => {
      loginModal.querySelector("#modalContent").classList.add("scale-95", "opacity-0");
      setTimeout(() => {
        loginModal.classList.add("hidden");
      }, 300);
    });

    switchToSignUp.addEventListener("click", () => {
      loginForm.classList.add("hidden");
      document.getElementById("signUpForm").classList.remove("hidden");
    });

    window.addEventListener("click", (e) => {
      if (e.target === loginModal) closeModal.click();
    });

    const forgotPasswordBtn = document.getElementById("forgotPasswordBtn");
    const forgotPasswordModal = document.getElementById("forgotPasswordModal");
    const forgotPasswordContent = document.getElementById("forgotPasswordContent");
    const cancelForgot = document.getElementById("cancelForgot");
    const forgotPasswordForm = document.getElementById("forgotPasswordForm");

    forgotPasswordBtn.addEventListener("click", (e) => {
      e.preventDefault();
      forgotPasswordModal.classList.remove("hidden");
      setTimeout(() => {
        forgotPasswordContent.classList.remove("scale-95", "opacity-0");
        forgotPasswordContent.classList.add("scale-100", "opacity-100");
      }, 10);
    });

    cancelForgot.addEventListener("click", () => {
      forgotPasswordContent.classList.add("scale-95", "opacity-0");
      setTimeout(() => {
        forgotPasswordModal.classList.add("hidden");
      }, 300);
    });

    forgotPasswordForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const email = document.getElementById("forgotEmail").value;
      alert("Reset link sent to: " + email);
      cancelForgot.click();
    });
  </script>

  <script>
    const signUpForm = document.getElementById("signUpForm");
    const switchToLogin = document.getElementById("switchToLogin");
    const infoModal = document.getElementById("infoModal");
    const privacyModal = document.getElementById("privacyModal");
    const confirmTerms = document.getElementById("confirmTerms");
    const confirmPrivacy = document.getElementById("confirmPrivacy");
    const termsContent = document.getElementById("termsContent");
    const privacyContent = document.getElementById("privacyContent");
    const agreeTerms = document.getElementById("agreeTerms");
    const agreePrivacy = document.getElementById("agreePrivacy");

    const signUpBtn = signUpForm.querySelector('button[type="submit"]');

    function updateSubmitState() {
      const bothChecked = agreeTerms.checked && agreePrivacy.checked;
      signUpBtn.disabled = !bothChecked;
      signUpBtn.classList.toggle("opacity-50", !bothChecked);
      signUpBtn.classList.toggle("cursor-not-allowed", !bothChecked);
      signUpBtn.classList.toggle("cursor-pointer", bothChecked);
    }

    agreeTerms.addEventListener("change", updateSubmitState);
    agreePrivacy.addEventListener("change", updateSubmitState);

    // Initial state
    updateSubmitState();

    let termsConfirmed = false;
    let privacyConfirmed = false;

    switchToLogin.addEventListener("click", () => {
      signUpForm.classList.add("hidden");
      document.getElementById("loginForm").classList.remove("hidden");
    });

    agreeTerms.addEventListener("mousedown", function(e) {
      if (!termsConfirmed && !this.checked) {
        e.preventDefault();
        infoModal.classList.remove("hidden");
        setTimeout(() => {
          infoModal.querySelector("#infoModalContent").classList.remove("scale-95", "opacity-0");
          infoModal.querySelector("#infoModalContent").classList.add("scale-100", "opacity-100");
        }, 10);
      }
    });

    agreePrivacy.addEventListener("mousedown", function(e) {
      if (!privacyConfirmed && !this.checked) {
        e.preventDefault();
        privacyModal.classList.remove("hidden");
        setTimeout(() => {
          privacyModal.querySelector("#privacyModalContent").classList.remove("scale-95", "opacity-0");
          privacyModal.querySelector("#privacyModalContent").classList.add("scale-100", "opacity-100");
        }, 10);
      }
    });

    confirmTerms.addEventListener("click", () => {
      termsConfirmed = true;
      agreeTerms.checked = true;
      updateSubmitState(); // ✅ This line enables the button
      infoModal.querySelector("#infoModalContent").classList.add("scale-95", "opacity-0");
      setTimeout(() => {
        infoModal.classList.add("hidden");
      }, 300);
    });

    confirmPrivacy.addEventListener("click", () => {
      privacyConfirmed = true;
      agreePrivacy.checked = true;
      updateSubmitState(); // ✅ This line enables the button
      privacyModal.querySelector("#privacyModalContent").classList.add("scale-95", "opacity-0");
      setTimeout(() => {
        privacyModal.classList.add("hidden");
      }, 300);
    });

    termsContent.addEventListener("scroll", () => {
      const scrolled = termsContent.scrollTop + termsContent.clientHeight >= termsContent.scrollHeight;
      confirmTerms.disabled = !scrolled;
      confirmTerms.classList.toggle("bg-blue-600", scrolled);
      confirmTerms.classList.toggle("hover:bg-blue-700", scrolled);
      confirmTerms.classList.toggle("cursor-pointer", scrolled);
      confirmTerms.classList.toggle("bg-gray-400", !scrolled);
      confirmTerms.classList.toggle("cursor-not-allowed", !scrolled);
    });

    privacyContent.addEventListener("scroll", () => {
      const scrolled = privacyContent.scrollTop + privacyContent.clientHeight >= privacyContent.scrollHeight;
      confirmPrivacy.disabled = !scrolled;
      confirmPrivacy.classList.toggle("bg-blue-600", scrolled);
      confirmPrivacy.classList.toggle("hover:bg-blue-700", scrolled);
      confirmPrivacy.classList.toggle("cursor-pointer", scrolled);
      confirmPrivacy.classList.toggle("bg-gray-400", !scrolled);
      confirmPrivacy.classList.toggle("cursor-not-allowed", !scrolled);
    });

    window.addEventListener("click", (e) => {
      if (e.target === infoModal) {
        infoModal.querySelector("#infoModalContent").classList.add("scale-95", "opacity-0");
        setTimeout(() => infoModal.classList.add("hidden"), 300);
      }
      if (e.target === privacyModal) {
        privacyModal.querySelector("#privacyModalContent").classList.add("scale-95", "opacity-0");
        setTimeout(() => privacyModal.classList.add("hidden"), 300);
      }
    });

    signUpForm.querySelector("form").addEventListener("submit", async (e) => {
      e.preventDefault();

      const form = e.target;
      const formData = new FormData(form);

      // Required checkbox validation
      if (!agreeTerms.checked || !agreePrivacy.checked) {
        showToast("❌ You must agree to both Terms and Privacy Policy.", 'error');
        return;
      }

      try {
        const response = await fetch("config/registration.php", {
          method: "POST",
          body: formData
        });

        const result = await response.json();

        if (result.status === "success") {
          showToast("✅ " + result.message, 'success');
          form.reset();
          signUpForm.classList.add("hidden");
          document.getElementById("loginForm").classList.remove("hidden");
        } else {
          showToast("❌ " + result.message, 'error');
        }
      } catch (error) {
        console.error("Error:", error);
        showToast("❌ An error occurred while registering.", 'error');

      }
    });

    function showToast(message, type = 'success') {
      const toast = document.createElement('div');
      toast.className = `
    px-4 py-3 rounded-md shadow-md text-white text-sm animate-slide-in-right
    ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}
  `;
      toast.textContent = message;

      document.getElementById('toastContainer').appendChild(toast);

      setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 500);
      }, 3000);
    }

    // Password strength validation
    const signUpPassword = document.getElementById("signUpPassword");
    const passwordStrengthText = document.createElement("p");
    passwordStrengthText.classList.add("mt-1", "text-sm", "font-semibold");
    signUpPassword.parentNode.appendChild(passwordStrengthText);

    signUpPassword.addEventListener("input", () => {
      const password = signUpPassword.value;
      const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password);
      let strength = '';
      let strengthColor = '';

      if (password.length < 6) {
        strength = 'Weak';
        strengthColor = 'text-red-600';
      } else if (password.length >= 6 && password.length < 10) {
        strength = 'Medium';
        strengthColor = 'text-yellow-600';
      } else if (password.length >= 10 && /[A-Z]/.test(password) && /[0-9]/.test(password) && hasSpecialChar) {
        strength = 'Strong';
        strengthColor = 'text-green-600';
      } else {
        strength = 'Medium';
        strengthColor = 'text-yellow-600';
      }

      if (!hasSpecialChar) {
        strength = 'Must include at least one special character';
        strengthColor = 'text-red-600';
      }

      passwordStrengthText.textContent = strength;
      passwordStrengthText.className = `mt-1 text-sm font-semibold ${strengthColor}`;
    });
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", async () => {
      try {
        const response = await fetch("https://stake.lido.fi/api/stats");
        const data = await response.json();

        const apy = (data.apy * 100).toFixed(2); // Example: 3.92
        const totalStaked = (data.totalPooledEther / 1e18).toLocaleString(undefined, {
          maximumFractionDigits: 0
        });
        const percent = Math.min((data.totalPooledEther / 1e18 / 10000000) * 100, 100).toFixed(0); // Target = 10M ETH

        document.getElementById("eth-apy").textContent = `${apy}% APY`;
        document.getElementById("eth-staked").textContent = `${totalStaked} ETH`;

        const progressBar = document.getElementById("eth-progress-bar");
        if (progressBar) {
          progressBar.style.width = `${percent}%`;
        }
      } catch (error) {
        document.getElementById("eth-apy").textContent = "Unavailable";
        document.getElementById("eth-staked").textContent = "Error loading...";
        console.error("Failed to fetch staking data:", error);
      }

      // Crypto staking
      const cryptoData = {
        BTC: {
          apyEl: document.getElementById("btc-apy"),
          stakedEl: document.getElementById("btc-staked"),
          minEl: document.getElementById("btc-min"),
          lockEl: document.getElementById("btc-lock"),
          maxStake: 20000,
        },
        ETH: {
          apyEl: document.getElementById("eth-apy"),
          stakedEl: document.getElementById("eth-staked"),
          minEl: document.getElementById("eth-min"),
          lockEl: document.getElementById("eth-lock"),
          maxStake: 10000000,
        },
        SOL: {
          apyEl: document.getElementById("sol-apy"),
          stakedEl: document.getElementById("sol-staked"),
          minEl: document.getElementById("sol-min"),
          lockEl: document.getElementById("sol-lock"),
          maxStake: 10000000,
        },
        ADA: {
          apyEl: document.getElementById("ada-apy"),
          stakedEl: document.getElementById("ada-staked"),
          minEl: document.getElementById("ada-min"),
          lockEl: document.getElementById("ada-lock"),
          maxStake: 30000000,
        },
      };

      function random(min, max) {
        return (Math.random() * (max - min) + min).toFixed(2);
      }

      function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
      }

      function updateData() {
        Object.entries(cryptoData).forEach(([key, data]) => {
          const apy = random(2, 10);
          const staked = formatNumber(Math.floor(Math.random() * data.maxStake));
          const minStake = random(0.01, 5).toString();
          const lockDays = Math.floor(Math.random() * 30) + 7;

          data.apyEl.innerText = `${apy}% APY`;
          data.stakedEl.innerText = `${staked} ${key}`;
          data.minEl.innerText = `${minStake} ${key}`;
          data.lockEl.innerText = `${lockDays} Days`;

          // Optional: Update progress bar width
          const progressBar = data.stakedEl.closest(".mb-6").querySelector(".progress-bar");
          if (progressBar) {
            const percent = Math.floor((parseInt(staked.replace(/,/g, "")) / data.maxStake) * 100);
            progressBar.style.width = `${percent}%`;
          }
        });
      }

      updateData();
      setInterval(updateData, 5000); // Update every 5 seconds
    });
  </script>

  <!-- Real-time JS -->
  <script>
    // Initialize chart with saved data or default values
    const savedData = localStorage.getItem('cryptoChartData');
    const initialData = savedData ? JSON.parse(savedData) : Array(15).fill(0).map(() => (Math.random() * 2 + 1).toFixed(2));

    const ctx = document.getElementById('miningChart').getContext('2d');
    const miningChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: Array(15).fill(''),
        datasets: [{
          label: 'Staking Rewards (USDT)',
          data: initialData,
          borderColor: '#3B82F6',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          fill: true,
          tension: 0.4,
          borderWidth: 2,
          pointRadius: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 0
        },
        scales: {
          x: {
            display: false,
            grid: {
              display: false
            }
          },
          y: {
            ticks: {
              color: '#9CA3AF'
            },
            grid: {
              color: 'rgba(156, 163, 175, 0.1)'
            }
          }
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            enabled: true,
            mode: 'index',
            intersect: false,
            backgroundColor: '#1F2937',
            titleColor: '#E5E7EB',
            bodyColor: '#D1D5DB',
            borderColor: '#374151',
            borderWidth: 1,
            padding: 12,
            callbacks: {
              label: function(context) {
                return ` ${context.dataset.label}: $${context.parsed.y}`;
              }
            }
          }
        }
      }
    });

    // Simulate real-time data updates
    let hashrate = localStorage.getItem('lastHashrate') || '0.00';
    let earnings = localStorage.getItem('lastEarnings') || '0.000000';
    let progress = localStorage.getItem('lastProgress') || 0;

    function updateDashboard() {
      // Simulate new price data (in a real app, this would come from WebSocket)
      const newPrice = (Math.random() * 2 + 1).toFixed(2);
      miningChart.data.datasets[0].data.push(newPrice);
      miningChart.data.datasets[0].data.shift();
      miningChart.update();

      // Save current data to localStorage
      localStorage.setItem('cryptoChartData', JSON.stringify(miningChart.data.datasets[0].data));

      // Simulate hashrate and earnings changes
      hashrate = (parseFloat(hashrate) + (Math.random() * 0.5 - 0.25)).toFixed(2);
      earnings = (parseFloat(earnings) + (Math.random() * 0.0001 - 0.00005)).toFixed(6);
      progress = (parseFloat(progress) + Math.random() * 2) % 100;

      // Update UI
      document.getElementById('hashrate').textContent = `${hashrate} TH/s`;
      document.getElementById('daily-earnings').textContent = `${earnings} BTC`;
      document.getElementById('progress-percent').textContent = `${progress.toFixed(0)}%`;
      document.getElementById('progress-bar').style.width = `${progress.toFixed(0)}%`;

      // Save current state
      localStorage.setItem('lastHashrate', hashrate);
      localStorage.setItem('lastEarnings', earnings);
      localStorage.setItem('lastProgress', progress);
    }

    // Initial update
    document.getElementById('hashrate').textContent = `${hashrate} TH/s`;
    document.getElementById('daily-earnings').textContent = `${earnings} BTC`;
    document.getElementById('progress-percent').textContent = `${progress}%`;
    document.getElementById('progress-bar').style.width = `${progress}%`;

    // Update every 3 seconds (simulating real-time updates)
    setInterval(updateDashboard, 3000);

    // For a real application, you would replace the setInterval with a WebSocket connection:
    const socket = new WebSocket('wss://crypto-price-feed.example.com');
    socket.onmessage = function(event) {
      const data = JSON.parse(event.data);
      // Process real data and update chart
      updateChartWithRealData(data);
    };
  </script>

  <script>
    window.onload = function() {
      const savedEmail = localStorage.getItem("rememberedEmail");
      const remember = localStorage.getItem("rememberMeChecked");
      if (savedEmail && remember === "true") {
        document.getElementById("email").value = savedEmail;
        document.getElementById("rememberMe").checked = true;
      }
    };

    document.querySelector("form").addEventListener("submit", () => {
      const remember = document.getElementById("rememberMe").checked;
      const email = document.getElementById("email").value;
      if (remember) {
        localStorage.setItem("rememberedEmail", email);
        localStorage.setItem("rememberMeChecked", "true");
      } else {
        localStorage.removeItem("rememberedEmail");
        localStorage.removeItem("rememberMeChecked");
      }
    });
  </script>

  <script>
    // Connect buttons to the login modal
    document.addEventListener("DOMContentLoaded", function() {
      const loginModal = document.getElementById("loginModal");
      const closeModal = document.getElementById("closeModal");

      // Function to show the login modal
      function showLoginModal() {
        loginModal.classList.remove("hidden");
        setTimeout(() => {
          loginModal.querySelector("#modalContent").classList.remove("scale-95", "opacity-0");
          loginModal.querySelector("#modalContent").classList.add("scale-100", "opacity-100");
        }, 10);
      }

      // Connect "Start Earning" button
      document.getElementById("start-earning-button").addEventListener("click", showLoginModal);

      // Connect "Stake Now" buttons
      document.querySelectorAll("button[id$='-stake-button']").forEach(button => {
        button.addEventListener("click", showLoginModal);
      });

      // Connect "Get Started Now" button
      document.getElementById("get-started-button").addEventListener("click", showLoginModal);

      // Close modal functionality
      closeModal.addEventListener("click", () => {
        loginModal.querySelector("#modalContent").classList.add("scale-95", "opacity-0");
        setTimeout(() => {
          loginModal.classList.add("hidden");
        }, 300);
      });
    });
  </script>

  <script src="assets/js/script.js"></script>
  <script src="assets/js/randomizer.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="assets/js/calculator.js"></script>
  <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>
</body>


</html>